<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * BalanceReportExport
 *
 * Exports the category-wise daily balance ledger matching the on-screen
 * template: a title banner, column header, per-month banner rows, daily
 * category amounts, expense ledger rows, a Total row and an AVG. label row.
 */
class BalanceReportExport implements FromArray, ShouldAutoSize
{
    protected $data;

    /**
     * __construct
     *
     * @param  array $data  Output of ReportsController::buildBalanceData()
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * array
     *
     * @return array
     */
    public function array(): array
    {
        $categories     = $this->data['categories'];
        $dates          = $this->data['dates'];
        $rows           = $this->data['rows'];
        $categoryTotals = $this->data['categoryTotals'];
        $expenseRows    = $this->data['expenseRows'] ?? [];

        $colCount = count($categories) + 1;
        $out      = [];

        // Title banner row (padded to full width).
        $out[] = $this->pad([__('custom.balance_report_heading')], $colCount);

        // Column header.
        $header = [__('custom.date')];
        foreach ($categories as $category) {
            $header[] = strtoupper($category->name);
        }
        $out[] = $header;

        // Daily rows grouped by month banner.
        $currentMonth = null;
        foreach ($dates as $date) {
            $carbon   = Carbon::parse($date);
            $monthKey = $carbon->format('Y-m');

            if ($monthKey !== $currentMonth) {
                $currentMonth = $monthKey;
                $out[] = $this->pad(
                    [strtoupper($carbon->format('F')) . ' .... ' . $carbon->format('Y')],
                    $colCount
                );
            }

            $row = [$carbon->format('j/n/y')];
            foreach ($categories as $category) {
                $value = $rows[$date][$category->id] ?? 0;
                $row[] = $value > 0 ? round($value, 0) : '';
            }
            $out[] = $row;
        }

        // Expense ledger rows (Salary, Bonus, EXP., REP. ...).
        foreach ($expenseRows as $expense) {
            $row   = [$expense['name']];
            $row[] = $expense['total'] > 0 ? round($expense['total'], 0) : '';
            $out[] = $this->pad($row, $colCount);
        }

        // Total row.
        $totalRow = [__('custom.total') . ' :'];
        foreach ($categories as $category) {
            $totalRow[] = round($categoryTotals[$category->id] ?? 0, 0);
        }
        $out[] = $totalRow;

        // AVG. label row.
        $out[] = $this->pad([__('custom.avg') . ':'], $colCount);

        return $out;
    }

    /**
     * pad
     *
     * Pad a row with empty cells up to the given column count.
     *
     * @param  array $row
     * @param  int   $count
     * @return array
     */
    protected function pad(array $row, int $count): array
    {
        return array_pad($row, $count, '');
    }
}
