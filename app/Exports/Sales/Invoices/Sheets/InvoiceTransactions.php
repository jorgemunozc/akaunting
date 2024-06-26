<?php

namespace App\Exports\Sales\Invoices\Sheets;

use App\Abstracts\Export;
use App\Models\Banking\Transaction as Model;
use App\Interfaces\Export\WithParentSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvoiceTransactions extends Export implements WithColumnFormatting, WithParentSheet
{
    public function collection()
    {
        return Model::with('account', 'category', 'contact', 'document')->income()->isDocument()->collectForExport($this->ids, ['paid_at' => 'desc'], 'document_id');
    }

    public function map($model): array
    {
        $document = $model->document;

        if (empty($document)) {
            return [];
        }

        $model->invoice_number = $document->document_number;
        $model->account_name = $model->account->name;
        $model->category_name = $model->category->name;
        $model->contact_email = $model->contact->email;
        $model->transaction_number = $model->number;

        return parent::map($model);
    }

    public function fields(): array
    {
        return [
            'invoice_number',
            'transaction_number',
            'paid_at',
            'amount',
            'currency_code',
            'currency_rate',
            'account_name',
            'contact_email',
            'category_name',
            'description',
            'payment_method',
            'reference',
            'reconciled',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
