<?php

namespace App\Http\Controllers;

use App\Enums\BankAccountOwner;
use App\Enums\Currency;
use App\Enums\InvoicePaymentType;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Enums\ShipmentTerms;
use App\Exports\InvoicesExport;
use App\Http\Requests\InvoiceCreateRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Mail\SendInvoicesEmailOptions;
use App\Models\Animal;
use App\Models\BankAccount;
use App\Models\Contact;
use App\Models\CurrencyRate;
use App\Models\Invoice;
use App\Models\OrderAction;
use App\Services\CreateZipService;
use App\Services\OfferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::with(['order']);

        $companies            = BankAccountOwner::get();
        $bankAccounts         = BankAccount::orderBy('beneficiary_fullname')->pluck('beneficiary_fullname', 'id');
        $invoice_type         = InvoiceType::get();
        $invoice_payment_type = InvoicePaymentType::get();
        $orderStatuses        = OrderStatus::get();
        $payment_type         = InvoicePaymentType::get();
        $price_type           = ShipmentTerms::get();

        $orderByOptions   = ['invoice_date' => 'Invoice date', 'full_number' => 'Invoice number', 'order_number' => 'Order number'];
        $orderByDirection = null;
        $orderByField     = null;

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('invoice.filter')) {
            $request = session('invoice.filter');

            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_invoice_year'])) {
                $invoices->whereYear('invoice_date', $request['filter_invoice_year']);

                $filterData = Arr::add($filterData, 'filter_invoice_year', 'Invoice year: ' . $request['filter_invoice_year']);
            }

            if (isset($request['filter_invoice_month'])) {
                $invoices->whereMonth('invoice_date', $request['filter_invoice_month']);

                $filterData = Arr::add($filterData, 'filter_invoice_month', 'Invoice month: ' . $request['filter_invoice_month']);
            }

            if (isset($request['filter_order_year'])) {
                $invoices->whereHas('order', function ($query) use ($request) {
                    $query->whereYear('created_at', $request['filter_order_year']);
                });

                $filterData = Arr::add($filterData, 'filter_order_year', 'Order year: ' . $request['filter_order_year']);
            }

            if (isset($request['filter_order_status'])) {
                $invoices->whereHas('order', function ($query) use ($request) {
                    $query->where('order_status', $request['filter_order_status']);
                });

                $filterData = Arr::add($filterData, 'filter_order_status', 'Order status: ' . $request['filter_order_status']);
            }

            if (isset($request['filter_invoice_species_id'])) {
                $filterAnimal = Animal::where('id', $request['filter_invoice_species_id'])->first();

                $invoices->whereHas('order.offer.offer_species.oursurplus', function ($query) use ($filterAnimal) {
                    $query->where('our_surplus.animal_id', $filterAnimal->id);
                });

                $filterData = Arr::add($filterData, 'filter_invoice_species_id', 'Species: ' . $filterAnimal->common_name);
            }

            if (isset($request['filter_invoice_contact_id'])) {
                $filterContact = Contact::where('id', $request['filter_invoice_contact_id'])->first();

                $invoices->where('invoice_contact_id', $filterContact->id);

                $filterData = Arr::add($filterData, 'filter_invoice_contact_id', 'contact person: ' . $filterContact->name);
            }

            if (isset($request['filter_invoice_company'])) {
                $bankAccountOwner = BankAccountOwner::getValue($request['filter_invoice_company']);

                $invoices->whereHas('bank_account', function ($query) use ($bankAccountOwner) {
                    $query->where('company_name', $bankAccountOwner);
                });

                $filterData = Arr::add($filterData, 'filter_invoice_company', 'Company: ' . $request['filter_invoice_company']);
            }

            if (isset($request['filter_bank_account_id'])) {
                $bankAccount = BankAccount::where('id', $request['filter_bank_account_id'])->first();

                $invoices->where('bank_account_id', $bankAccount->id);

                $filterData = Arr::add($filterData, 'filter_bank_account_id', 'Bank account: ' . $bankAccount->beneficiary_fullname);
            }

            if (isset($request['filter_invoice_payment_type'])) {
                $invoices->where('payment_type', $request['filter_invoice_payment_type']);

                $filterData = Arr::add($filterData, 'filter_invoice_payment_type', 'Payment: ' . $request['filter_invoice_payment_type']);
            }

            if (isset($request['filter_invoice_type'])) {
                $invoices->where('invoice_type', $request['filter_invoice_type']);

                $filterData = Arr::add($filterData, 'filter_invoice_type', 'Type: ' . $request['filter_invoice_type']);
            }

            if (isset($request['filter_paid_value']) && $request['filter_paid_value'] != 'any') {
                if ($request['filter_paid_value'] == 'yes') {
                    $invoices->where('paid_value', '<>', 0);
                } elseif ($request['filter_paid_value'] == 'no') {
                    $invoices->where(function ($query) {
                        $query->where('paid_value', 0)
                            ->orWhere('paid_value', null);
                    });
                }

                $filterData = Arr::add($filterData, 'filter_paid_value', 'Paid: ' . $request['filter_paid_value']);
            }

            if (isset($request['filter_banking_cost']) && $request['filter_banking_cost'] != 'any') {
                if ($request['filter_banking_cost'] == 'ok') {
                    $invoices->whereRaw('(paid_value + banking_cost = invoice_amount');
                    $invoices->orWhereRaw('paid_value = invoice_amount)');
                } elseif ($request['filter_banking_cost'] == 'not_set') {
                    $invoices->whereRaw('(banking_cost is null && (paid_value < invoice_amount');
                    $invoices->orWhereRaw('paid_value is null))');
                } elseif ($request['filter_banking_cost'] == 'amount_missing') {
                    $invoices->whereRaw('(banking_cost is not null && paid_value + banking_cost < invoice_amount)');
                }

                $filterData = Arr::add($filterData, 'filter_banking_cost', 'Banking cost: ' . $request['filter_banking_cost']);
            }

            if (isset($request['filter_invoice_remark'])) {
                $invoices->where('remark', 'like', '%' . $request['filter_invoice_remark'] . '%');

                $filterData = Arr::add($filterData, 'filter_invoice_remark', 'Remark: ' . $request['filter_invoice_remark']);
            }
            //dump(DB::getQueryLog()); // Show results of log
            if (isset($request['filter_price_type'])) {
                $p_type = $request['filter_price_type'];
                $invoices->whereHas('order.offer', function ($query) use ($p_type) {
                    $query->where('sale_price_type', $p_type);
                });

                $filterData = Arr::add($filterData, 'filter_price_type', 'Price type: ' . $request['filter_price_type']);
            }
        } else {
            $invoices->orderByDesc('invoice_date');
        }

        $invoices = $invoices->get();

        if (isset($request)) {
            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                switch ($orderByField) {
                    case 'order_number':
                        if ($orderByDirection == 'desc') {
                            $invoices = $invoices->sortByDesc(function ($invoice, $key) {
                                return $invoice->order->full_number;
                            });
                        } else {
                            $invoices = $invoices->sortBy(function ($invoice, $key) {
                                return $invoice->order->full_number;
                            });
                        }
                        break;
                    default:
                        if ($orderByDirection == 'desc') {
                            $invoices = $invoices->sortByDesc(function ($invoice, $key) use ($orderByField) {
                                return $invoice->$orderByField;
                            });
                        } else {
                            $invoices = $invoices->sortBy(function ($invoice, $key) use ($orderByField) {
                                return $invoice->$orderByField;
                            });
                        }
                        break;
                }
            }
        }

        $array_object_results = [];
        foreach ($invoices as $invoice) {
            array_push($array_object_results, $invoice);
        }

        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $perPage      = 50;
        $currentItems = array_slice($array_object_results, $perPage * ($currentPage - 1), $perPage);

        $invoices = new LengthAwarePaginator($currentItems, count($array_object_results), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view('invoices.index', compact(
            'invoices',
            'companies',
            'bankAccounts',
            'payment_type',
            'invoice_type',
            'invoice_payment_type',
            'orderStatuses',
            'orderByOptions',
            'orderByDirection',
            'orderByField',
            'filterData',
            'price_type'
        ));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('invoice.filter');

        return redirect(route('invoices.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies   = Currency::get();
        $companies    = BankAccountOwner::get();
        $bankAccounts = BankAccount::orderBy('beneficiary_fullname')->pluck('beneficiary_fullname', 'id');
        $invoice_type = InvoiceType::get();

        return view('invoices.create', compact('companies', 'bankAccounts', 'invoice_type', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\InvoiceCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceCreateRequest $request)
    {
        $request['belong_to_order'] = false;

        if ($request->hasFile('invoiceFile')) {
            $file = $request->file('invoiceFile');

            //File Name
            $file_name               = $file->getClientOriginalName();
            $request['invoice_file'] = $file_name;

            $path = Storage::putFileAs(
                'public/other_invoices', $file, $file_name
            );
        }

        Invoice::create($request->all());

        return redirect(route('invoices.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        $currencies       = Currency::get();
        $companies        = BankAccountOwner::get();
        $invoice->company = BankAccountOwner::getIndex($invoice->bank_account->company_name);
        $bankAccounts     = BankAccount::where('company_name', $invoice->bank_account->company_name)->orderBy('beneficiary_fullname')->pluck('beneficiary_fullname', 'id');
        $invoice_type     = InvoiceType::get();

        $invoice->invoiceContact = $invoice->contact->email;

        return view('invoices.edit', compact('invoice', 'currencies', 'companies', 'bankAccounts', 'invoice_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\InvoiceUpdateRequest  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        if ($request->hasFile('invoiceFile')) {
            Storage::delete('public/other_invoices/' . $invoice->invoice_file);

            $file = $request->file('invoiceFile');

            //File Name
            $file_name               = $file->getClientOriginalName();
            $request['invoice_file'] = $file_name;

            $path = Storage::putFileAs(
                'public/other_invoices', $file, $file_name
            );
        }

        $invoice->update($request->all());

        return redirect(route('invoices.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->belong_to_order) {
            $folderName = $invoice->order->full_number;
            if ($invoice->invoice_type === 'credit') {
                Storage::delete('public/orders_docs/' . $folderName . '/outgoing_invoices/' . $invoice->invoice_file);
            } elseif ($invoice->invoice_type === 'debit') {
                Storage::delete('public/orders_docs/' . $folderName . '/incoming_invoices/' . $invoice->invoice_file);
            }
        } else {
            Storage::delete('public/other_invoices/' . $invoice->invoice_file);
        }

        $invoice->delete();

        return redirect()->back();
    }

    /**
     * Get invoice by id throught ajax.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetInvoiceById(Request $request)
    {
        $invoice                 = Invoice::find($request->invoiceId);
        $invoice['invoice_date'] = ($invoice->invoice_date) ? date('Y-m-d', strtotime($invoice->invoice_date)) : null;
        $invoice['paid_date']    = ($invoice->paid_date) ? date('Y-m-d', strtotime($invoice->paid_date)) : null;

        return response()->json(['success' => true, 'invoice' => $invoice]);
    }

    /**
     * Filter invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterInvoices(Request $request)
    {
        // Set session invoice filter
        session(['invoice.filter' => $request->query()]);

        return redirect(route('invoices.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('invoice.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['invoice.filter' => $query]);

        return redirect(route('invoices.index'));
    }

    /**
     * Remove from invoice session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromInvoiceSession($key)
    {
        $query = session('invoice.filter');
        Arr::forget($query, $key);
        session(['invoice.filter' => $query]);

        return redirect(route('invoices.index'));
    }

    //Export excel document with invoices info.
    public function export(Request $request)
    {
        $file_name = 'Invoices list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $invoices = Invoice::whereIn('id', explode(',', $request->items))->orderBy('invoice_date')->get();
        foreach ($invoices as $invoice) {
            $rate_usd = ($invoice->invoice_currency != 'USD') ? number_format(CurrencyRate::latest()->value($invoice->invoice_currency . '_USD'), 2, '.', '') : 1;

            $invoice->paid_value_usd = 0;
            if ($invoice->paid_value != null) {
                $invoice->paid_value_usd = $invoice->paid_value * $rate_usd;
            }

            $invoice->offer_totals = OfferService::calculate_offer_totals($invoice->order()->first()->offer_id)->getAttributes();
        }
        $export = new InvoicesExport($invoices);

        return Excel::download($export, $file_name);
    }

    //Export excel document with invoices info.
    public function exportZip(Request $request)
    {
        $public_dir  = public_path();
        $date        = Carbon::now()->format('Y-m-d H:i:s');
        $zipFileName = $file_name = 'invoices_list_' . Carbon::now()->format('Y-m-d') . '_' . strtotime($date) . '.zip';
        $zip         = new CreateZipService();
        $invoices    = Invoice::whereIn('id', explode(',', $request->items))->orderBy('invoice_date')->get();
        $files       = [];
        foreach ($invoices as $row) {
            if ($row->invoice_type === 'credit') {
                array_push($files, ['url' => $public_dir . Storage::url('orders_docs/' . $row->order->full_number . '/outgoing_invoices/' . $row->invoice_file), 'name' => $row->invoice_file]);
            } else {
                array_push($files, ['url' => $public_dir . Storage::url('orders_docs/' . $row->order->full_number . '/incoming_invoices/' . $row->invoice_file), 'name' => $row->invoice_file]);
            }
        }
        $url_file = $zip->createZip($files, $zipFileName);

        $headers = [
            'Content-Type' => 'application/octet-stream',
        ];
        // Create Download Response
        if (file_exists($url_file)) {
            return response()->download($url_file, $zipFileName, $headers);
        } else {
            return redirect()->back()->with('error', 'The pdf of the selected invoices do not exist');
        }
    }

    /**
     * Set order invoice payment.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setInvoicePayment(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        $invoice->update($request->all());

        return redirect()->back();
    }

    /**
     * Order client invoice email.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMultipleClientInvoice(Request $request)
    {
        if (!empty($request->items)) {
            $invoices = Invoice::whereIn('id', explode(',', $request->items))->orderBy('invoice_date')->get();
            $items    = $request->items;
            if ($invoices->count() > 0) {
                $fileName          = [];
                $client            = $invoices[0]['order']['client'];
                $orders            = [];
                $order_new         = 0;
                $order_full_number = [];

                foreach ($invoices as $key => $row) {
                    if ($client['id'] != $row->order->client->id) {
                        return redirect(route('invoices.index'))->with('error', 'The invoices must belong to the same client');
                    }

                    if (!Storage::exists('public/orders_docs/' . $row->order->full_number . '/outgoing_invoices/' . $row->invoice_file)) {
                        return redirect()->back()->with('error', 'The pdf related with this invoice is missing.');
                    }

                    $year = Carbon::parse($row->invoice_date)->format('Y');
                    array_push($fileName, ucfirst($row->invoice_type) . ' ' . $row->invoice_file);

                    if ($order_new != $row->order->id) {
                        array_push($order_full_number, $row->order->full_number);
                        $order_new = $row->order->id;
                    }

                    array_push($orders, $row->order);
                }

                $email_from        = 'info@zoo-services.com';
                $email_code        = 'multiple_client_invoice';
                $email_to          = $client->email;
                $email_cc          = '';
                $email_bcc         = 'joke@zoo-services.com, dw51@verkoop.exactonline.nl, johnrens@zoo-services.com';
                $email_subject     = 'Invoices Order ' . implode(' | ', $order_full_number);
                $email_body        = view('emails.send-multiple-order-client-invoice', compact('orders', 'client'))->render();
                $email_attachments = $fileName;

                return view('invoices.invoices_email_view', compact('items', 'email_code', 'email_from', 'email_to', 'email_cc', 'email_subject', 'email_body', 'email_attachments', 'email_bcc'));
            } else {
                return redirect(route('invoices.index'))->with('error', 'Invoice not found');
            }
        } else {
            return redirect(route('invoices.index'))->with('error', 'There is no item selected');
        }
    }

    /**
     * Send email option.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function invoicesSendEmail(Request $request)
    {
        $invoices = Invoice::whereIn('id', explode(',', $request->items))->orderBy('invoice_date')->get();

        $email_bcc_array = [];
        if ($request->email_bcc != null) {
            $email_bcc_array = array_map('trim', explode(',', $request->email_bcc));
        }

        Mail::to($request->email_to)->bcc($email_bcc_array)->send(new SendInvoicesEmailOptions($invoices, $request->email_option, $request->email_from, $request->email_subject, $request->email_body));

        $order = 0;
        foreach ($invoices as $row) {
            if ($order != $row->order->id) {
                $orderAction = OrderAction::where('order_id', $row->order->id)->whereHas('action', function ($query) use ($request) {
                    $query->where('action_code', $request->email_option);
                })->first();
                if ($orderAction != null) {
                    $orderAction->update(['action_date' => Carbon::now()->format('Y-m-d H:i:s')]);
                }

                $order = $row->order->id;
            }
        }

        return redirect(route('invoices.index'))->with('success', 'Email successfully sent.');
    }
}
