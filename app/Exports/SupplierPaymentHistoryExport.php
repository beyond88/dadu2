<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SupplierPaymentHistoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'SL#',
            'Date',
            'Supplier Name',
            'Supplier Phone',
            'Purchase Number',
            'Payment Type',
            'Amount',
            'Notes',
            'Created At',
        ];
    }

    public function map($payment): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $payment->date,
            $payment->supplier_name ?? ($payment->purchase->supplier->name ?? 'N/A'),
            $payment->purchase->supplier->phone ?? 'N/A',
            $payment->purchase->purchase_number ?? 'N/A',
            $payment->payment_type,
            number_format($payment->amount, 2),
            $payment->notes ?? '-',
            $payment->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
