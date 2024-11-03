<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Attributes\Post;
use App\Attributes\Route;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\View;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment as Twig;

class InvoiceController
{
    public function __construct(private Twig $twig)
    {
    }

    #[Get('/invoices')]
    public function index(): string
    {
        $invoices = Invoice::query()
            ->where('status', InvoiceStatus::Paid)
            ->get()
            ->map(
                fn(Invoice $invoice) => [
                    'invoiceNumber' => $invoice->invoice_number,
                    'amount'        => $invoice->amount,
                    'status'        => $invoice->status->toString(),
                    'dueDate'       => $invoice->due_date->toDateTimeString(),
                ]
            )
            ->toArray();

        return $this->twig->render('invoices/index.twig', ['invoices' => $invoices]);
    }

    #[Get('/invoices/new')]
    public function create()
    {
        $invoice = new Invoice();

        $invoice->invoice_number = 5;
        $invoice->amount = 20;
        $invoice->status = InvoiceStatus::Pending;

        $invoice->save();

        echo $invoice->id . ', ' . $invoice->due_date->format('m/d/Y');
    }
}