<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Customer ledger: a periodical statement of account for one customer —
 * every invoice and payment in a date range, with the due running down the page.
 */
class CustomerLedgerReportController extends Controller
{
    private const PER_PAGE = 50;

    public function __construct()
    {
        $this->middleware(['permission:Customer Ledger Report']);
    }

    /**
     * index
     */
    public function index(Request $request)
    {
        set_page_meta(__('custom.customer_ledger_report'));

        $customers = Customer::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'phone', 'code']);
        $ledger    = null;

        if ($this->hasFilters($request)) {
            $this->validateFilters($request);
            $ledger = $this->buildLedger(
                Customer::findOrFail($request->customer_id),
                $request->from_date,
                $request->to_date
            );
        }

        $rows = $ledger ? $this->paginateRows($ledger['rows'], $request) : null;

        return view('admin.reports.customer-ledger', compact('customers', 'ledger', 'rows'));
    }

    /**
     * export — same numbers as the screen, rendered as a statement.
     *
     * With ?print=1 the statement is returned as HTML that opens its own print
     * dialog, so printing uses this layout rather than the screen markup.
     */
    public function export(Request $request)
    {
        $this->validateFilters($request);

        $customer = Customer::findOrFail($request->customer_id);
        $ledger   = $this->buildLedger($customer, $request->from_date, $request->to_date);

        if ($request->boolean('print')) {
            return response()->view('admin.reports.pdf.customer-ledger', [
                'ledger'    => $ledger,
                'autoPrint' => true,
            ]);
        }

        $name = 'customer-ledger-' . $customer->id . '-' . $request->from_date . '-to-' . $request->to_date . '.pdf';

        // mPDF, not DomPDF: the bundled Hind Siliguri font carries the ৳ sign and
        // Bangla glyphs, which DomPDF's core fonts render as "?".
        return render_mpdf(
            'admin.reports.pdf.customer-ledger',
            ['ledger' => $ledger],
            $name,
            ['margin_top' => 10, 'margin_bottom' => 12],
            'stream'
        );
    }

    /**
     * A report is only generated once a customer and both dates are given.
     */
    private function hasFilters(Request $request): bool
    {
        return $request->filled('customer_id') || $request->filled('from_date') || $request->filled('to_date');
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
        ]);
    }

    /**
     * Build the statement.
     *
     * Two queries only — the invoices and the payments of the range — merged in
     * date order. Every figure comes from the shared Customer helpers, so the
     * report can never drift from the customer list or the payment screens.
     */
    private function buildLedger(Customer $customer, string $from, string $to): array
    {
        // Everything before the range start is condensed into the opening rows.
        $dayBefore = Carbon::parse($from)->subDay()->format('Y-m-d');

        $openingDue     = $customer->totalDue($dayBefore);
        $openingBalance = $customer->balanceAsOf($dayBefore);

        $invoices = $customer->invoicesQuery($from, $to)
            ->orderBy('date')->orderBy('id')
            ->get(['id', 'date', 'total']);

        $payments = $customer->invoicePaymentsQuery($from, $to)
            ->orderByDate()
            ->get(['id', 'invoice_id', 'date', 'payment_type', 'amount']);

        $entries = collect();

        // Statement columns: an invoice is a charge (debit, the balance goes up),
        // a payment is a receipt (credit, the balance comes down).
        foreach ($invoices as $invoice) {
            $entries->push([
                'sort_date'    => $invoice->getRawOriginal('date'),
                'sort_group'   => 0, // an invoice comes before its own payment
                'sort_id'      => $invoice->id,
                'date'         => $invoice->getRawOriginal('date'),
                'description'  => __('custom.invoice') . ' #' . make8digits($invoice->id),
                'debit'        => (float) $invoice->total,
                'credit'       => 0.0,
                'balance_used' => 0.0,
            ]);
        }

        foreach ($payments as $payment) {
            $method    = strtoupper($payment->payment_type ?: '');
            $isBalance = strtolower($method) === 'balance';

            $entries->push([
                'sort_date'    => substr((string) $payment->getRawOriginal('date'), 0, 10),
                'sort_group'   => 1,
                'sort_id'      => $payment->id,
                'date'         => substr((string) $payment->getRawOriginal('date'), 0, 10),
                'description'  => __('custom.payment') . ' #' . $payment->id
                    . ($method ? ' ' . $method : '')
                    . ($payment->invoice_id ? ' — ' . __('custom.invoice') . ' #' . make8digits($payment->invoice_id) : ''),
                'debit'        => 0.0,
                'credit'       => (float) $payment->amount,
                'balance_used' => $isBalance ? (float) $payment->amount : 0.0,
            ]);
        }

        // Chronological, invoices ahead of same-day payments.
        $entries = $entries->sortBy([
            ['sort_date', 'asc'],
            ['sort_group', 'asc'],
            ['sort_id', 'asc'],
        ])->values();

        // Running balance: a charge raises it, a receipt brings it down.
        $running = $openingDue;
        $rows    = $entries->map(function ($entry) use (&$running) {
            $running += $entry['debit'] - $entry['credit'];
            $entry['running_due'] = round($running, 2);

            return $entry;
        });

        return [
            'customer'         => $customer,
            'from'             => $from,
            'to'               => $to,
            'rows'             => $rows,
            'opening_due'      => $openingDue,
            'opening_balance'  => $openingBalance,
            'total_invoiced'   => $customer->totalInvoiced($from, $to),
            'total_paid'       => $customer->totalPaid($from, $to),
            'total_balance_used' => round((float) $rows->sum('balance_used'), 2),
            'closing_due'      => $customer->totalDue($to),
            'closing_balance'  => $customer->balanceAsOf($to),
        ];
    }

    /**
     * The rows are a merge of two tables, so the running due is worked out over
     * the whole range first and only then cut into pages.
     */
    private function paginateRows(Collection $rows, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $rows->forPage($page, self::PER_PAGE)->values(),
            $rows->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
