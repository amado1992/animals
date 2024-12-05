<?php

namespace App\Http\Controllers;

use App\Enums\ActionOrderCategory;
use App\Enums\AgeGroup;
use App\Enums\BankAccountOwner;
use App\Enums\Currency;
use App\Enums\InvoiceFrom;
use App\Enums\InvoicePaymentType;
use App\Enums\OrderOrderByOptions;
use App\Enums\OrderStatus;
use App\Enums\ShipmentTerms;
use App\Enums\Size;
use App\Enums\TaskActions;
use App\Exports\OrdersExport;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Mail\SendGeneralEmail;
use App\Mail\SendOrderEmailOptions;
use App\Models\Action;
use App\Models\AdditionalCost;
use App\Models\Airport;
use App\Models\Animal;
use App\Models\BankAccount;
use App\Models\Contact;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\Invoice;
use App\Models\Labels;
use App\Models\Offer;
use App\Models\OfferAction;
use App\Models\OfferAdditionalCost;
use App\Models\OfferSpecies;
use App\Models\Order;
use App\Models\OrderAction;
use App\Models\Organisation;
use App\Models\Origin;
use App\Models\Region;
use App\Models\Role;
use App\Models\StdText;
use App\Models\Task;
use App\Models\User;
use App\Services\OfferService;
use Carbon\Carbon;
use DOMPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\GraphService;
use Illuminate\Support\Facades\App;

class OrderController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('id', Auth::id())->first();

        if (!$user->hasPermission('orders.see-all-orders')) {
            $orders = Order::where('manager_id', Auth::id());
        }
        //  $orders = Order::orderByDesc(DB::raw("YEAR(created_at)"))->orderByDesc('order_number');

        $roles         = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins        = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $companies     = BankAccountOwner::get();
        $orderStatuses = OrderStatus::get();
        $orderStatuses = Arr::prepend($orderStatuses, 'All', 'all');
        $countries     = Country::orderBy('name')->pluck('name', 'id');
        $currencies    = Currency::get();
        $price_type    = ShipmentTerms::get();
        $bankAccounts  = BankAccount::orderBy('beneficiary_fullname')->pluck('beneficiary_fullname', 'id');

        $orderByOptions   = OrderOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;
        $statusField      = null;

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('order.filter')) {
            $request = session('order.filter');

            if (isset($request['orderByDirection']) && isset($request['orderByField']) && $request['orderByField'] === 'invoice_number') {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];
                $orders           = Order::select('*', 'orders.id as id', 'orders.created_at as created_at', 'orders.updated_at as updated_at')
                    ->join('invoices', 'invoices.order_id', '=', 'orders.id')
                    ->where('invoices.invoice_type', 'credit')
                    ->orderBy('invoices.created_at', $orderByDirection);
            } elseif (isset($request['orderByDirection']) && isset($request['orderByField']) && $request['orderByField'] !== 'invoice_number') {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                $orders = Order::orderBy($orderByField, $orderByDirection);
            } else {
                $orders = Order::orderBy('order_status', 'ASC');
            }

            if (isset($request['statusField']) && $request['statusField'] != 'all') {
                $statusField = $request['statusField'];

                $orders->where('order_status', $statusField);

                $filterData = Arr::add($filterData, 'statusField', 'Status: ' . $statusField);
            }

            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_order_number'])) {
                $orders->where('order_number', $request['filter_order_number']);

                $filterData = Arr::add($filterData, 'filter_order_number', 'Order No: ' . $request['filter_order_number']);
            }

            if (isset($request['filter_order_year'])) {
                $orders->whereYear('orders.created_at', $request['filter_order_year']);

                $filterData = Arr::add($filterData, 'filter_order_year', 'Order year: ' . $request['filter_order_year']);
            }

            if (isset($request['filter_realized_year'])) {
                $orders->whereYear('realized_date', $request['filter_realized_year']);

                $filterData = Arr::add($filterData, 'filter_realized_year', 'Realized year: ' . $request['filter_realized_year']);
            }

            if (isset($request['filter_project_manager'])) {
                $filterUser = User::where('id', $request['filter_project_manager'])->first();

                $orders->where('manager_id', $filterUser->id);

                $filterData = Arr::add($filterData, 'filter_project_manager', 'Manager: ' . $filterUser->name);
            }

            if (isset($request['filter_order_company'])) {
                $orders->where('company', $request['filter_order_company']);

                $filterData = Arr::add($filterData, 'filter_order_company', 'Company: ' . $request['filter_order_company']);
            }

            if (isset($request['filter_animal_id'])) {
                $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                $orders->whereHas(
                    'offer.offer_species.oursurplus', function ($query) use ($filterAnimal) {
                        $query->where('our_surplus.animal_id', $filterAnimal->id);
                    }
                );

                $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
            }

            if (isset($request['filter_client_id'])) {
                $filterClient = Contact::where('id', $request['filter_client_id'])->first();

                $orders->where('client_id', $filterClient->id);

                $filterData = Arr::add($filterData, 'filter_client_id', 'Client: ' . $filterClient->full_name);
            }

            if (isset($request['filter_supplier_id'])) {
                $filterSupplier = Contact::where('id', $request['filter_supplier_id'])->first();

                $orders->where('supplier_id', $filterSupplier->id);

                $filterData = Arr::add($filterData, 'filter_supplier_id', 'Supplier: ' . $filterSupplier->full_name);
            }

            if (isset($request['filter_start_date'])) {
                $orders->where('created_at', '>=', $request['filter_start_date']);

                $filterData = Arr::add($filterData, 'filter_start_date', 'Created start at: ' . $request['filter_start_date']);
            }

            if (isset($request['filter_end_date'])) {
                $orders->where('created_at', '<=', $request['filter_end_date']);

                $filterData = Arr::add($filterData, 'filter_end_date', 'Created end at: ' . $request['filter_end_date']);
            }

            if (isset($request['filter_intern_remarks'])) {
                $orders->where('order_remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_intern_remarks', 'Intern remarks: ' . $request['filter_intern_remarks']);
            }
        } else {
            $orders = Order::orderBy('order_status', 'ASC');
        }

        //query for orderBy "order_status" in "admin panel -> orders list"
        DB::table('orders')
            ->selectRaw(
                "(CASE
            WHEN (order_status = 'Pending') THEN 3
            WHEN (order_status = 'Realized') THEN 2
            WHEN (order_status = 'Cancelled') THEN 1
            ELSE 0 END) AS order_status"
            )
            // ->addSelect('order_status')
            // ->orderBy('order_status', 'DESC')
            ->get();

        if (isset($request) && isset($request['recordsPerPage'])) {
            $orders = $orders->paginate($request['recordsPerPage']);
        } else {
            $orders = $orders->paginate(50);
        }

        //dump(DB::getQueryLog()); // Show results of log

        $totalCostUsdOrder   = 0;
        $totalCostOrder      = 0;
        $totalSaleUsdOrder   = 0;
        $totalSaleOrder      = 0;
        $totalProfitUsdOrder = 0;
        $totalProfitOrder    = 0;

        foreach ($orders as $order) {
            $order->offer = $this->offerService->calculate_offer_totals($order->offer->id);
            $totalCostUsdOrder   += $order->offer->offerTotalCostPriceUSD;
            $totalCostOrder      += $order->offer->offerTotalCostPrice;
            $totalSaleUsdOrder   += $order->offer->offerTotalSalePriceUSD;
            $totalSaleOrder      += $order->offer->offerTotalSalePrice;
            $totalProfitUsdOrder += $order->offer->offerTotalSalePriceUSD - $order->offer->offerTotalCostPriceUSD;
            $totalProfitOrder    += $order->offer->offerTotalSalePrice    - $order->offer->offerTotalCostPrice;
        }

        $query             = session('order.filter');
        $query['orderTab'] = '#animalsTab';
        session(['order.filter' => $query]);

        return view(
            'orders.index', compact(
                'orders',
                'admins',
                'companies',
                'orderStatuses',
                'statusField',
                'orderByOptions',
                'orderByDirection',
                'orderByField',
                'filterData',
                'totalCostUsdOrder',
                'totalCostOrder',
                'totalSaleUsdOrder',
                'totalSaleOrder',
                'totalProfitUsdOrder',
                'totalProfitOrder',
                'countries',
                'currencies',
                'price_type',
                'bankAccounts'
            )
        );
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('order.filter');

        return redirect(route('orders.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('order.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['order.filter' => $query]);

        return redirect(route('orders.index'));
    }

    /**
     * Quick change order.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function quickChangeOrder(Request $request)
    {
        $query             = session('order.filter');
        $query['orderTab'] = '#animalsTab';
        session(['order.filter' => $query]);

        return redirect(route('orders.show', $request->orderFullNumberValue));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $cities    = Airport::orderBy('name')->pluck('city', 'id');

        $roles         = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins        = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $currencies    = Currency::get();
        $orderStatuses = OrderStatus::get();
        $price_type    = ShipmentTerms::get();
        $companies     = BankAccountOwner::get();
        $bankAccounts  = BankAccount::orderBy('beneficiary_fullname')->pluck('beneficiary_fullname', 'id');

        return view(
            'orders.create', compact(
                'admins',
                'countries',
                'companies',
                'bankAccounts',
                'cities',
                'currencies',
                'orderStatuses',
                'price_type'
            )
        );
    }

    public function updateRemark(Request $request)
    {
        $order = Order::find($request->id);
        if (!empty($order)) {
            $order['order_remarks'] = $request->remarks ?? '';
            $order->save();

            return response()->json(['error' => false, 'message' => 'The remark was updated successfully', 'remark' => $order['order_remarks']]);
        } else {
            return response()->json(['error' => true, 'message' => 'Order not found']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OrderCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderCreateRequest $request)
    {
        $year = Carbon::now()->format('Y');

        if ($request->client_id == null) {
            $request['client_id'] = $request->contact_client_id;
        }

        if ($request->supplier_id != null) {
            $request['supplier_id'] = $request->supplier_id;
        } elseif ($request->contact_supplier_id != null) {
            $request['supplier_id'] = $request->contact_supplier_id;
        } else {
            $izsContact = Contact::where('email', 'izs@zoo-services.com')->first();

            $request['supplier_id'] = ($izsContact) ? $izsContact->id : null;
        }

        $newOffer                        = new Offer();
        $newOffer['manager_id']          = Auth::id();
        $newOffer['creator']             = 'IZS';
        $newOffer['client_id']           = $request->client_id;
        $newOffer['supplier_id']         = $request->supplier_id;
        $newOffer['airfreight_agent_id'] = $request->airfreight_agent_id;
        $offerNumber                     = Offer::whereYear('created_at', $year)->max('offer_number');
        $newOffer['offer_number']        = ($offerNumber) ? $offerNumber + 1 : 1;
        $newOffer['offer_status']        = 'Ordered';
        $newOffer['offer_currency']      = $request->sale_currency;
        $newOffer['sale_price_type']     = $request->sale_price_type;
        $newOffer['delivery_country_id'] = $request->delivery_country_id;
        $newOffer['delivery_airport_id'] = $request->delivery_airport_id;
        $newOffer->save();

        $offerActions = Action::where(
            function ($query) {
                $query->where('belongs_to', 'Offer')
                    ->orWhere('belongs_to', 'Offer_Order');
            }
        )->get();
        foreach ($offerActions as $offerAction) {
            $newOfferAction             = new OfferAction();
            $newOfferAction->offer_id   = $newOffer->id;
            $newOfferAction->action_id  = $offerAction->id;
            $newOfferAction->toBeDoneBy = $offerAction->toBeDoneBy;
            $newOfferAction->save();
        }

        $request['offer_id'] = $newOffer->id;
        $orderNumber         = Order::whereYear('created_at', $year)->max('order_number');

        if ($request->order_number == null) {
            $request['order_number'] = ($orderNumber) ? $orderNumber + 1 : 1;
        }
        if ($request->created_at == null) {
            $request['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $order = Order::create($request->all());
        if ($request->order_status === 'Realized') {
            $order['realized_order_send'] = '1';
            $order['realized_date']       = Carbon::now()->format('Y-m-d H:i:s');
            $order->save();
        } else {
            $order['new_order_send'] = '1';
            $order->save();
        }

        $additionalCosts = AdditionalCost::get();

        foreach ($additionalCosts as $ac) {
            if ($ac->is_test === 1) {
                $quantity = 0;
            } else {
                $quantity = 1;
            }
            $newOfferAdditionalCost            = new OfferAdditionalCost();
            $newOfferAdditionalCost->offer_id  = $newOffer->id;
            $newOfferAdditionalCost->name      = $ac->name;
            $newOfferAdditionalCost->quantity  = $quantity;
            $newOfferAdditionalCost->currency  = $order->sale_currency;
            $newOfferAdditionalCost->costPrice = ($order->sale_currency == 'USD') ? $ac->usdCostPrice : $ac->eurCostPrice;
            $newOfferAdditionalCost->salePrice = ($order->sale_currency == 'USD') ? $ac->usdSalePrice : $ac->eurSalePrice;
            $newOfferAdditionalCost->is_test   = $ac->is_test;
            $newOfferAdditionalCost->save();
        }

        $actions = Action::where(
            function ($query) {
                $query->where('belongs_to', 'Order')
                    ->orWhere('belongs_to', 'Offer_Order');
            }
        )->get();
        foreach ($actions as $action) {
            $orderAction             = new OrderAction();
            $orderAction->order_id   = $order->id;
            $orderAction->action_id  = $action->id;
            $orderAction->toBeDoneBy = $action->toBeDoneBy;
            $orderAction->save();
        }

        $query             = session('order.filter');
        $query['orderTab'] = '#animalsTab';
        session(['order.filter' => $query]);

        return redirect(route('orders.show', $order->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $origin            = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup          = AgeGroup::get();
        $sizes             = Size::get();
        $price_type        = ShipmentTerms::get();
        $payment_type      = InvoicePaymentType::get();
        $invoice_from      = InvoiceFrom::get();
        $task_actions      = TaskActions::get();
        $action_categories = ActionOrderCategory::get();
        $regionsNames      = Region::pluck('name', 'id');

        $regions      = Region::orderBy('name')->pluck('name', 'id');
        $from_country = Country::orderBy('name')->pluck('name', 'id');
        $to_country   = Country::orderBy('name')->pluck('name', 'id');
        $dashboards   = Dashboard::where('main', 1)->orderBy('order', 'ASC')->get();

        $html = '<div class="custom-dd dd" id="nestable_list_1">
                    <ol class="dd-list">
                        ';
        $html = $this->getHtmlDashboarSon($dashboards, $html);
        $html .= '</div>
        </ol>
            ';

        $dashboards = $html;

        $offer        = $this->offerService->calculate_offer_totals($order->offer->id);
        $order->offer = $offer;

        $bankAccounts = BankAccount::orderBy('beneficiary_fullname')->pluck('beneficiary_fullname', 'id');

        $year                 = Carbon::now()->format('Y');
        $invoiceBankAccountNo = Invoice::whereYear('created_at', $year)->max('bank_account_number');
        $invoiceBankAccountNo = ($invoiceBankAccountNo) ? $invoiceBankAccountNo + 1 : 1;

        $order_invoices = $order->invoices;

        $totalCreditInvoiceAmount            = 0;
        $totalCreditInvoicePaidAmount        = 0;
        $totalCreditInvoiceBankingCostAmount = 0;
        $totalDebitInvoiceAmount             = 0;
        $totalDebitInvoicePaidAmount         = 0;
        $totalDebitInvoiceBankingCostAmount  = 0;
        foreach ($order_invoices as $invoice) {
            if ($invoice->invoice_type == 'credit') {
                $totalCreditInvoiceAmount            += $invoice->invoice_amount;
                $totalCreditInvoicePaidAmount        += $invoice->paid_value;
                $totalCreditInvoiceBankingCostAmount += $invoice->banking_cost;
            } else {
                $totalDebitInvoiceAmount            += $invoice->invoice_amount;
                $totalDebitInvoicePaidAmount        += $invoice->paid_value;
                $totalDebitInvoiceBankingCostAmount += $invoice->banking_cost;
            }
        }

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $roles = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $users = User::whereRoleIs(Arr::pluck($roles, 'name'))->orderBy('name')->pluck('name', 'id');

        $selectedOrderTab = '#animalsTab';
        if (session()->has('order.filter')) {
            $sessionData = session('order.filter');

            if (isset($sessionData['orderTab'])) {
                $selectedOrderTab = $sessionData['orderTab'];
            }
        }

        $reservationActions = OrderAction::where('order_id', $order->id)->whereHas(
            'action', function ($query) {
                $query->where('category', 'reservation');
            }
        )->get();
        $permitActions = OrderAction::where('order_id', $order->id)->whereHas(
            'action', function ($query) {
                $query->where('category', 'permit');
            }
        )->get();
        $veterinaryActions = OrderAction::where('order_id', $order->id)->whereHas(
            'action', function ($query) {
                $query->where('category', 'veterinary');
            }
        )->get();
        $crateActions = OrderAction::where('order_id', $order->id)->whereHas(
            'action', function ($query) {
                $query->where('category', 'crate');
            }
        )->get();
        $transportActions = OrderAction::where('order_id', $order->id)->whereHas(
            'action', function ($query) {
                $query->where('category', 'transport');
            }
        )->get();

        $allOrders = Order::orderByDesc(DB::raw('YEAR(created_at)'))->orderByDesc('order_number')->get();
        if (!empty($order->offer) && $order->offer->client) {
            $client = $order->offer->client;
        } else {
            $client = $order->offer->organisation;
        }

        // Select alle emails received for this order and/or linked offer
        $num_or = $order->id;
        $num_of = $order->offer_id;
        $emails_received = Email::where(
            function ($query) use ($num_or, $num_of) {
                $query->where('order_id', '=', $num_or)
                    ->orWhere('offer_id', '=', $num_of);
            }
        )->where('is_send', 0);

        // Select alle emails sent for this order and/or linked offer
        $emails = Email::where(
            function ($query) use ($num_or, $num_of) {
                $query->where('order_id', '=', $num_or)
                    ->orWhere('offer_id', '=', $num_of);
            }
        )->where('is_send', 1);        

        if (session()->has('order.filterShow')) {
            $sessionData = session('order.filterShow');
        }
        $sortselected = !empty($sessionData['sortselected']) ? $sessionData['sortselected'] : '';
        if (!empty($sortselected)) {
           
            $ec = new EmailsController();
            $email_order = $ec->emailOrdening($emails_received, $emails, $sortselected);
            $emails_received = $email_order['emails_received'];
            $emails = $email_order['emails'];
        }

        $emails_received = $emails_received->paginate(10);
        $emails = $emails->paginate(10);

        $filter = 'orders.filterEmailsOrder';

        return view(
            'orders.show', compact(
                'order',
                'selectedOrderTab',
                'allOrders',
                'offer',
                'general_rate_usd',
                'general_rate_eur',
                'regions',
                'from_country',
                'to_country',
                'origin',
                'ageGroup',
                'sizes',
                'price_type',
                'payment_type',
                'invoice_from',
                'bankAccounts',
                'invoiceBankAccountNo',
                'order_invoices',
                'totalCreditInvoiceAmount',
                'totalCreditInvoicePaidAmount',
                'totalCreditInvoiceBankingCostAmount',
                'totalDebitInvoiceAmount',
                'totalDebitInvoicePaidAmount',
                'totalDebitInvoiceBankingCostAmount',
                'task_actions',
                'users',
                'action_categories',
                'reservationActions',
                'permitActions',
                'veterinaryActions',
                'crateActions',
                'transportActions',
                'regionsNames',
                'emails_received',
                'emails',
                'dashboards',
                'sortselected',
                'filter',
            )
        );
    }

    public function getHtmlDashboarSon($parents, $html)
    {
        foreach ($parents as $key => $parent) {
            if (!empty($parent->parent_id) && $parent->type_style == 'default') {
                $html .= '<li class="dd-item" data-id="1">
                            <div class="dd-handle">
                                <input type="radio" class="selector-parent" style="margin: -1px 7px 0px 0;" name="parent_id" value="' . $parent->id . '" />' . $parent->name . '
                            </div>
                        ';
            } else {
                $html .= '<li class="dd-item" data-id="1">
                            <div class="dd-handle">
                                <input type="radio" class="selector-parent" style="margin: -1px 7px 0px 0;" name="parent_id" value="' . $parent->id . '" />' . $parent->name . '
                            </div>
                        ';
            }

            if (!empty($parent->dashboards->toArray())) {
                $html .= '<ol class="dd-list">';
                $html = $this->getHtmlDashboarSon($parent->dashboards, $html);
                $html .= '</li>';
            } else {
                $html .= '</li>';
            }
        }
        $html .= '</ol>';

        return $html;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $cities    = Airport::orderBy('name')->pluck('city', 'id');

        $roles         = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins        = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $currencies    = Currency::get();
        $orderStatuses = OrderStatus::get();
        $price_type    = ShipmentTerms::get();
        $companies     = BankAccountOwner::get();
        $bankAccounts  = BankAccount::orderBy('beneficiary_fullname')->pluck('beneficiary_fullname', 'id');

        $order->realized_date = ($order->realized_date != null) ? date('Y-m-d', strtotime($order->realized_date)) : null;
        $order->created_date  = ($order->created_at    != null) ? Carbon::parse($order->created_at)->format('Y-m-d') : null;

        return view(
            'orders.edit', compact(
                'order',
                'admins',
                'companies',
                'bankAccounts',
                'countries',
                'cities',
                'currencies',
                'orderStatuses',
                'price_type'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OrderUpdateRequest $request
     * @param  \App\Order                            $order
     * @return \Illuminate\Http\Response
     */
    public function update(OrderUpdateRequest $request, Order $order)
    {
        if ($request->client_id != null) {
            if ($order->client_id != $request->client_id) {
                $request['client_id'] = $request->client_id;
                $order->offer->update(['client_id' => $request->client_id]);
            }
        } elseif ($request->contact_client_id != null) {
            if ($order->client_id != $request->contact_client_id) {
                $request['client_id'] = $request->contact_client_id;
                $order->offer->update(['client_id' => $request->client_id]);
            }
        } else {
            $request['client_id'] = $order->client_id;
        }

        if ($request->supplier_id != null) {
            if ($order->supplier_id != $request->supplier_id) {
                $request['supplier_id'] = $request->supplier_id;
                $order->offer->update(['supplier_id' => $request->supplier_id]);
            }
        } elseif ($request->contact_supplier_id != null) {
            if ($order->supplier_id != $request->contact_supplier_id) {
                $request['supplier_id'] = $request->contact_supplier_id;
                $order->offer->update(['supplier_id' => $request->supplier_id]);
            }
        } else {
            $request['supplier_id'] = $order->supplier_id;
        }

        if (!$request->has('contact_final_destination_id')) {
            $request['contact_final_destination_id'] = null;
        }

        if (!$request->has('contact_origin_id')) {
            $request['contact_origin_id'] = null;
        }

        if (!$request->has('airfreight_agent_id')) {
            $request['airfreight_agent_id'] = null;
            $order->offer->update(['airfreight_agent_id' => null]);
        } elseif ($order->airfreight_agent_id != $request->airfreight_agent_id) {
            $order->offer->update(['airfreight_agent_id' => $request->airfreight_agent_id]);
        }

        if ($order->sale_currency != $request->sale_currency) {
            $order->offer->update(['offer_currency' => $request->sale_currency]);
            $order->offer->additional_costs()->update(['currency' => $request->sale_currency]);
        }

        if ($order->sale_price_type != $request->sale_price_type) {
            $order->offer->update(['sale_price_type' => $request->sale_price_type]);
        }

        if ($order->delivery_country_id != $request->delivery_country_id) {
            $order->offer->update(['delivery_country_id' => $request->delivery_country_id]);
        }

        if ($order->delivery_airport_id != $request->delivery_airport_id) {
            $order->offer->update(['delivery_airport_id' => $request->delivery_airport_id]);
        }

        if ($order->order_status != $request->order_status && $request->order_status === 'Realized') {
            $order['realized_order_send'] = 1;
            $order->save();
            $request['realized_date'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $order->update((is_null($request->input('set_number_year'))) ? $request->except(['order_number', 'created_at']) : $request->all());

        return redirect(route('orders.show', $order->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        Storage::deleteDirectory('public/orders_docs/' . $order->full_number);
        Storage::deleteDirectory('public/offers_docs/' . $order->offer->full_number);

        $order->offer->delete();

        $order->delete();

        return redirect(route('orders.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $deleteItem = Order::findOrFail($id);
                Storage::deleteDirectory('public/orders_docs/' . $deleteItem->full_number);
                Storage::deleteDirectory('public/offers_docs/' . $deleteItem->offer->full_number);
                $deleteItem->offer->delete();
                $deleteItem->delete();
            }
        }

        return response()->json();
    }

    /**
     * Remove the selected species.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete_species(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $speciesDelete = OfferSpecies::findOrFail($id);
                $speciesDelete->delete();
            }
        }

        return response()->json();
    }

    /**
     * Upload files related with the order.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload_file(Request $request)
    {
        if ($request->hasFile('file')) {
            $order = Order::findOrFail($request->orderId);

            $docCategory = $request->docCategory;

            $folderName = $order->full_number;

            $file = $request->file('file');

            //File Name
            $file_name = $file->getClientOriginalName();

            switch ($docCategory) {
            case 'airfreight':
                $path = Storage::putFileAs('public/offers_docs/' . $order->offer->full_number . '/airfreight/', $file, $file_name);

                if ($order->offer->airfreight_type === 'pallets' && $order->offer->airfreight_pallet != null && $order->offer->airfreight_pallet->airfreight_id != null) {
                    Storage::copy('public/offers_docs/' . $order->offer->full_number . '/airfreight/' . $file_name, 'public/airfreights_docs/' . $order->offer->airfreight_pallet->airfreight_id . '/' . $file_name);
                }
                break;
            case 'crates':
                $path = Storage::putFileAs('public/offers_docs/' . $order->offer->full_number . '/crates/', $file, $file_name);
                break;
            case 'cites_docs':
                $path = Storage::putFileAs('public/offers_docs/' . $order->offer->full_number . '/cites_docs/', $file, $file_name);
                break;
            case 'veterinary_docs':
                $path = Storage::putFileAs('public/offers_docs/' . $order->offer->full_number . '/veterinary_docs/', $file, $file_name);
                break;
            case 'documents':
                $path = Storage::putFileAs('public/offers_docs/' . $order->offer->full_number . '/documents/', $file, $file_name);
                break;
            case 'suppliers_offers':
                $path = Storage::putFileAs('public/offers_docs/' . $order->offer->full_number . '/suppliers_offers/', $file, $file_name);
                break;
            case 'others':
                $path = Storage::putFileAs('public/offers_docs/' . $order->offer->full_number . '/documents/', $file, $file_name);
                break;
            default:
                $path = Storage::putFileAs('public/orders_docs/' . $folderName, $file, $file_name);
                break;
            }
        }

        //return redirect()->back()->with('status', 'Successfully uploaded file');
        return response()->json(['success' => true], 200);
    }

    /**
     * Upload files related with the order.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadOrderActionDocument(Request $request)
    {
        $orderAction = OrderAction::where('id', $request->id)->first();

        if ($request->hasFile('file_to_upload')) {
            $file = $request->file('file_to_upload');

            //File Name
            $file_name = $file->getClientOriginalName();

            switch ($orderAction->action->category) {
            case 'permit':
                Storage::delete('public/offers_docs/' . $orderAction->order->offer->full_number . '/cites_docs/' . $orderAction->action_document);
                $path = Storage::putFileAs('public/offers_docs/' . $orderAction->order->offer->full_number . '/cites_docs/', $file, $file_name);
                break;
            case 'veterinary':
                Storage::delete('public/offers_docs/' . $orderAction->order->offer->full_number . '/veterinary_docs/' . $orderAction->action_document);
                $path = Storage::putFileAs('public/offers_docs/' . $orderAction->order->offer->full_number . '/veterinary_docs/', $file, $file_name);
                break;
            case 'crate':
                Storage::delete('public/offers_docs/' . $orderAction->order->offer->full_number . '/crates/' . $orderAction->action_document);
                $path = Storage::putFileAs('public/offers_docs/' . $orderAction->order->offer->full_number . '/crates/', $file, $file_name);
                break;
            case 'transport':
                Storage::delete('public/offers_docs/' . $orderAction->order->offer->full_number . '/airfreight/' . $orderAction->action_document);
                $path = Storage::putFileAs('public/offers_docs/' . $orderAction->order->offer->full_number . '/airfreight/', $file, $file_name);
                break;
            default:
                Storage::delete('public/orders_docs/' . $orderAction->order->full_number . '/' . $orderAction->action_document);
                $path = Storage::putFileAs('public/orders_docs/' . $orderAction->order->full_number, $file, $file_name);
                break;
            }

            $orderAction->update(['action_document' => $file_name]);
        }

        return redirect()->back();
    }

    /**
     * Delete order file.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_file($order_id, $file_name, $folder = null)
    {
        $order = Order::findOrFail($order_id);

        $parentFolderName = $order->full_number;

        switch ($folder) {
        case 'airfreight':
            Storage::delete('public/offers_docs/' . $order->offer->full_number . '/airfreight/' . $file_name);
            break;
        case 'crates':
            Storage::delete('public/offers_docs/' . $order->offer->full_number . '/crates/' . $file_name);
            break;
        case 'cites_docs':
            Storage::delete('public/offers_docs/' . $order->offer->full_number . '/cites_docs/' . $file_name);
            break;
        case 'veterinary_docs':
            Storage::delete('public/offers_docs/' . $order->offer->full_number . '/veterinary_docs/' . $file_name);
            break;
        case 'documents':
            Storage::delete('public/offers_docs/' . $order->offer->full_number . '/documents/' . $file_name);
            break;
        case 'suppliers_offers':
            Storage::delete('public/offers_docs/' . $order->offer->full_number . '/suppliers_offers/' . $file_name);
            break;
        case 'others':
            Storage::delete('public/offers_docs/' . $order->offer->full_number . '/' . $file_name);
            break;
        case 'outgoing_invoices':
            Storage::delete('public/orders_docs/' . $parentFolderName . '/' . $folder . '/' . $file_name);
            break;
        case 'incoming_invoices':
            Storage::delete('public/orders_docs/' . $parentFolderName . '/' . $folder . '/' . $file_name);
            break;
        default:
            Storage::delete('public/orders_docs/' . $parentFolderName . '/' . $file_name);
            break;
        }

        $orderAction = $order->order_actions()->where('action_document', $file_name)->first();
        if ($orderAction != null) {
            $orderAction->update(['action_document' => null]);
        }

        return redirect(route('orders.show', $order_id));
    }

    /**
     * Get company bank accounts.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getBankAccountsByCompany(Request $request)
    {
        $bankAccounts = null;
        if ($request->has('value')) {
            $bankAccounts = BankAccount::where('company_name', BankAccountOwner::getValue($request->value))->pluck('beneficiary_fullname', 'id');
        }

        return response()->json(['success' => true, 'bankAccounts' => $bankAccounts]);
    }

    /**
     * Get invoice amount based on percent.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getInvoiceAmountBasedOnPercent(Request $request)
    {
        $order = Order::findOrFail($request->idOrder);

        $offer = $this->offerService->calculate_offer_totals($order->offer->id);

        $invoice = null;
        if ($request->has('idInvoice')) {
            $invoice = Invoice::findOrFail($request->idInvoice);
        }

        $orderInvoicesTotalPercent = $order->invoices->sum('invoice_percent');
        $percentPossibleValue      = 100 - $orderInvoicesTotalPercent;

        $amount = 0;
        if ($request->has('value')) {
            if ($request->value <= $percentPossibleValue) {
                if ($offer->order->sale_currency !== 'USD') {
                    $amount = round((($offer->offerTotalSalePrice * $request->value) / 100), 2);
                } else {
                    $amount = round((($offer->offerTotalSalePriceUSD * $request->value) / 100), 2);
                }
            }

            return response()->json(['success' => true, 'amount' => $amount, 'invoice' => $invoice]);
        }

        return response()->json(['success' => false, 'msg' => 'invalid percent value.', 'invoice' => $invoice]);
    }

    /**
     * Get invoices balance percent left.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getInvoicesBalancePercentLeft(Request $request)
    {
        $order = Order::findOrFail($request->idOrder);

        $offer = $this->offerService->calculate_offer_totals($order->offer->id);

        $orderInvoicesTotalPercent = $order->invoices->sum('invoice_percent');
        $percentValueLeft          = 100 - $orderInvoicesTotalPercent;

        $amount = round((($offer->offerTotalSalePrice * $percentValueLeft) / 100), 2);

        return response()->json(['success' => true, 'amount' => $amount, 'percent' => $percentValueLeft]);
    }

    /**
     * Create order invoice.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create_invoice(Request $request)
    {
        $order = Order::findOrFail($request->post('order_id'));

        if (!Storage::exists('public/orders_docs/' . $order->full_number . '/' . 'Reservation client ' . $order->full_number . '.pdf')) {
            return response()->json(['success' => false, 'msg' => 'Before printing fisrt invoice, Reservation client must be printed.']);
        }

        $origin   = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup = AgeGroup::get();
        $sizes    = Size::get();

        $offer = $this->offerService->calculate_offer_totals($order->offer->id);

        $invoiceNumber = ($request->post('bank_account_number') != null) ? $request->post('bank_account_number') : (Invoice::where('bank_account_id', $order->bank_account->id)->max('bank_account_number') + 1);

        $invoice_payment_type = $request->post('payment_type');
        $invoice_percent      = $request->post('invoice_percent');
        $invoice_amount       = $request->post('invoice_amount');
        $invoice_number       = $invoiceNumber;
        $invoice_date         = $request->post('invoice_date');

        $bankAccountsEurBV = [];
        if ($order->sale_currency == 'EUR') {
            $bankAccountsEurBV = BankAccount::where('company_name', 'IZS-BV')->where('currency', 'EUR')->orderByDesc('created_at')->get();
        }

        $amountBtw         = 0;
        $importantMessages = [];
        if ($order->within_europe && $order->client->organisation != null && $order->client->organisation->vat_number == null) {
            array_push($importantMessages, '- VAT number of client is required (Shipment within EU)');
        }
        if ($order->within_europe && $order->supplier->organisation != null && $order->supplier->organisation->vat_number == null) {
            array_push($importantMessages, '- VAT number of supplier is required (Shipment within EU)');
        }
        if ($order->within_europe && $order->invoices->count() == 0 && $invoice_amount != $offer->offerTotalSalePrice) {
            array_push($importantMessages, '- Please note that most species of Cites II or less can be transported immediately. Cites I need a EU certificate. Check first if the supplier has everything ready and transport can occur within 10 days; in that case the total amount can be charged on the invoice. If not, 30%.');
        }
        if ($order->within_netherlands) {
            $amountBtw      = number_format(($invoice_amount * 1.21) - $invoice_amount, 2, '.', '');
            $invoice_amount = number_format($invoice_amount  * 1.21, 2, '.', '');
            array_push($importantMessages, '- It is transaction within same country, and we pay VAT to supplier, in dutch called BTW, which we get back after Michael send a survey of all invoices to Tax-office - this is why he asked you every month the list!');
        }

        $invoice_info = view('pdf_documents.invoice_pdf', compact('order', 'offer', 'bankAccountsEurBV', 'invoice_payment_type', 'invoice_percent', 'invoice_amount', 'amountBtw', 'invoice_number', 'invoice_date'))->render();

        $checkRulesMessages = null;
        $index              = 0;
        foreach ($importantMessages as $importantMessage) {
            $checkRulesMessages .= ($index != 0) ? "\n\n" . $importantMessage : $importantMessage;
            $index++;
        }

        return response()->json(
            [
            'success'              => true,
            'checkRulesMessages'   => $checkRulesMessages,
            'invoice_info'         => $invoice_info,
            'invoice_payment_type' => $invoice_payment_type,
            'invoice_percent'      => $invoice_percent,
            'invoice_amount'       => $invoice_amount,
            'invoice_number'       => $invoice_number,
            'invoice_date'         => $invoice_date,
            ]
        );
    }

    /**
     * Export invoice pdf.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export_invoice_pdf(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $folderName = $order->full_number;

        if ($request->invoice_id == null) {
            //create invoice in database
            $request['invoice_contact_id'] = $order->client->id;
            $request['invoice_type']       = 'credit';
            $request['invoice_currency']   = $order->sale_currency;
            $request['bank_account_id']    = $order->bank_account->id;
            $invoice                       = Invoice::create($request->all());
            ////////////////////////////
        } else {
            $invoice = Invoice::findOrFail($request->invoice_id);
            $invoice->update(
                [
                'bank_account_number' => $request->bank_account_number,
                'invoice_date'        => $request->invoice_date,
                'invoice_percent'     => $request->invoice_percent,
                'invoice_currency'    => $order->sale_currency,
                'invoice_amount'      => $request->invoice_amount,
                ]
            );

            if ($invoice->invoice_type === 'credit') {
                Storage::delete('public/orders_docs/' . $folderName . '/outgoing_invoices/' . $invoice->invoice_file);
            } elseif ($invoice->invoice_type === 'debit') {
                Storage::delete('public/orders_docs/' . $folderName . '/incoming_invoices/' . $invoice->invoice_file);
            }
        }

        $year     = Carbon::parse($request->invoice_date)->format('Y');
        $fileName = 'Invoice ' . $order->bank_account->company_name . '-' . $year . '-' . $request['bank_account_number'] . '-' . $order->full_number . '-' . $order->sale_currency . '.pdf';

        $invoice->update(['invoice_file' => $fileName]);

        $html = str_replace('http://127.0.0.1:8000', base_path() . '/public', $request->invoice_html);

        $pdf = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

        Storage::put('public/orders_docs/' . $folderName . '/outgoing_invoices/' . $fileName, $pdf->output());

        return redirect()->back();
    }

    /**
     * Upload invoice.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload_invoice(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $folderName = $order->full_number;

        $file_name = null;
        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $file_name = $file->getClientOriginalName();
            $path      = Storage::putFileAs(
                'public/orders_docs/' . $folderName . '/incoming_invoices',
                $file,
                $file_name
            );
        }

        //create invoice in database
        $request['payment_type']     = 'balance';
        $request['invoice_type']     = 'debit';
        $request['invoice_currency'] = $order->cost_currency;
        $request['invoice_amount']   = $request->upload_invoice_amount;
        $request['invoice_date']     = Carbon::now()->format('Y-m-d H:i:s');
        $request['invoice_file']     = $file_name;
        Invoice::create($request->all());
        ////////////////////////////

        return redirect()->back();
    }

    /**
     * Create order documents.
     *
     * @param  int id
     * @param  string doc_code
     * @return \Illuminate\Http\Response
     */
    public function create_order_documents_pdf($id, $code)
    {
        $order = Order::findOrFail($id);

        $offer = $this->offerService->calculate_offer_totals($order->offer->id);

        $bankAccountsEurBV = [];
        if ($order->sale_currency == 'EUR') {
            $bankAccountsEurBV = BankAccount::where('company_name', 'IZS-BV')->where('currency', 'EUR')->orderByDesc('created_at')->get();
        }

        switch ($code) {
        case 'reservation_client':
            $reservationClientText = StdText::where('code', 'order-rc')->first();
            $file_name             = 'Reservation client ' . $order->full_number . '.pdf';
            $document_html         = view('pdf_documents.reservation_client_pdf', compact('order', 'offer', 'reservationClientText'))->render();
            break;
        case 'reservation_supplier':
            $reservationSupplierText = StdText::where('code', 'order-rs')->first();
            $file_name               = 'Reservation supplier ' . $order->full_number . '.pdf';
            $document_html           = view('pdf_documents.reservation_supplier_pdf', compact('order', 'offer', 'reservationSupplierText'))->render();
            break;
        case 'proforma_invoice':
            $file_name     = 'Proforma invoice ' . $order->full_number . '.pdf';
            $document_html = view('pdf_documents.proforma_invoice_pdf', compact('order', 'offer', 'bankAccountsEurBV'))->render();
            break;
        case 'statement_izs':
            $file_name     = 'Statement IZS ' . $order->full_number . '.pdf';
            $document_html = view('pdf_documents.statement_izs', compact('order', 'offer'))->render();
            break;
        default:
            $file_name     = 'Error ' . $order->full_number . '.pdf';
            $document_html = '';
            break;
        }

        return view('orders.document_preview', compact('order', 'code', 'document_html', 'file_name'));
    }

    /**
     * Create order documents.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create_packing_list(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $code  = 'packing_list';

        $listRows = $request->list_rows;
        $awbNo    = $request->awb_number;
        $kgValue  = $request->kg_value;

        $speciesText = '';
        foreach ($order->offer->species_ordered as $species) {
            $speciesText .= $species->oursurplus->animal->common_name . ', ';
        }

        $file_name     = 'Packing list ' . $order->full_number . '.pdf';
        $document_html = view('pdf_documents.packing_list_pdf', compact('order', 'listRows', 'awbNo', 'kgValue', 'speciesText'))->render();

        return view('orders.document_preview', compact('order', 'code', 'document_html', 'file_name'));
    }

    /**
     * Export document pdf.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export_document_pdf(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $folderName = $order->full_number;

        $fileName = $request->file_name;

        $html = str_replace('http://127.0.0.1:8000', base_path() . '/public', $request->document_html);

        $pdf = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

        Storage::put('public/orders_docs/' . $folderName . '/' . $fileName, $pdf->output());

        if ($request->code === 'reservation_client' && !Storage::exists('public/orders_docs/' . $order->full_number . '/' . 'Checklist of documents ' . $order->full_number . '.pdf')) {
            Storage::copy('public/orders_docs/Checklist of documents.pdf', 'public/orders_docs/' . $folderName . '/Checklist of documents ' . $folderName . '.pdf');
        }

        return redirect(route('orders.show', $order));
    }

    /**
     * Edit order invoice.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editOrderInvoice(Request $request)
    {
        $invoice = Invoice::findOrFail($request->post('invoice_id'));

        $origin   = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup = AgeGroup::get();
        $sizes    = Size::get();

        $order = $invoice->order;
        $offer = $this->offerService->calculate_offer_totals($invoice->order->offer->id);

        $invoice_payment_type = $invoice->payment_type;
        $invoice_percent      = $request->post('invoice_percent');
        $invoice_amount       = $request->post('invoice_amount');
        $invoice_number       = $request->post('bank_account_number');
        $invoice_date         = $request->post('invoice_date');

        $bankAccountsEurBV = [];
        if ($order->sale_currency == 'EUR') {
            $bankAccountsEurBV = BankAccount::where('company_name', 'IZS-BV')->where('currency', 'EUR')->orderByDesc('created_at')->get();
        }

        $amountBtw = 0;
        if ($order->within_netherlands) {
            $amountBtw      = number_format(($invoice_amount * 1.21) - $invoice_amount, 2, '.', '');
            $invoice_amount = number_format($invoice_amount  * 1.21, 2, '.', '');
        }

        $invoice_info = view('pdf_documents.invoice_pdf', compact('order', 'offer', 'bankAccountsEurBV', 'invoice_payment_type', 'invoice_percent', 'invoice_amount', 'amountBtw', 'invoice_number', 'invoice_date'))->render();

        return response()->json(
            [
            'success'              => true,
            'invoice_id'           => $invoice->id,
            'invoice_info'         => $invoice_info,
            'invoice_payment_type' => $invoice_payment_type,
            'invoice_percent'      => $invoice_percent,
            'invoice_amount'       => ($invoice_amount),
            'invoice_number'       => $invoice_number,
            'invoice_date'         => $invoice_date,
            ]
        );
    }

    /**
     * Set order invoice payment.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setOrderInvoicePayment(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        $invoice->update($request->all());

        return redirect()->back();
    }

    /**
     * Filter orders.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function filterOrders(Request $request)
    {
        // Set session order filter
        $order = session('order.filter');
        foreach ($request->query() as $key => $row) {
            if (!empty($row)) {
                $order[$key] = $row;
            }
        }
        session(['order.filter' => $order]);

        return redirect(route('orders.index'));
    }

    /**
     * Filter emails of 1 order to be able to order by date or containing attachments
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function filterEmailsOrder(Request $request)
    {
        // Set session order filter
        session(['order.filterShow' => $request->query()]);

        return redirect(route('orders.show', $request->id));
    }
    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('order.filter');
        $query['statusField']      = $request->statusField;
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['order.filter' => $query]);

        return redirect(route('orders.index'));
    }

    /**
     * Quick order status selection.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function ordersWithStatus(Request $request)
    {
        $query                = session('order.filter');
        $query['statusField'] = $request->statusField;
        session(['order.filter' => $query]);

        return redirect(route('orders.index'));
    }

    /**
     * Save order selected tab in session.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function selectedOrderTab(Request $request)
    {
        $query             = session('order.filter');
        $query['orderTab'] = $request->orderTab;
        session(['order.filter' => $query]);

        $order = Order::findOrFail($request->orderId);
        $offer = $this->offerService->calculate_offer_totals($order->offer->id);

        $html = view('offers.totals_offer_table', compact('offer'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    /**
     * Remove from order session.
     *
     * @param  string $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromOrderSession($key)
    {
        $query = session('order.filter');
        Arr::forget($query, $key);
        session(['order.filter' => $query]);

        return redirect(route('orders.index'));
    }

    //Export excel document with orders info.
    public function export(Request $request)
    {
        $file_name = 'Orders list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $ordersByYear = Order::select('*', DB::raw('YEAR(created_at) as year, MONTH(created_at) as month'))->whereIn('id', explode(',', $request->items))->get()->groupBy(['year', 'month']);

        $export = new OrdersExport($ordersByYear);

        return Excel::download($export, $file_name);
    }

    /**
     * Create new order task.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function orderTask(Request $request)
    {
        if (isset($request->task_id)) {
            $task = Task::findOrFail($request->task_id);

            $task->description = $request->description;
            $task->action      = $request->action;
            $task->user_id     = $request->user_id;

            $task->update();
        } else {
            $order = Order::findOrFail($request->id);

            $task              = new Task();
            $task->description = $request->description;
            $task->action      = $request->action;
            $task->user_id     = $request->user_id;
            $task->created_by  = Auth::id();

            $order->tasks()->save($task);
            $order->refresh();
        }

        $now = Carbon::now();
        switch ($request->quick_action_dates) {
        case 'today':
            $due_date = $now;
            break;
        case 'tomorrow':
            $due_date = $now->addDays(1);
            break;
        case 'week':
            $due_date = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d H:i');
            break;
        case 'month':
            $due_date = $now->endOfMonth()->format('Y-m-d H:i');
            break;
        case 'specific':
            $due_date = $request->due_date;
            break;
        case 'none':
            $due_date = null;
            break;
        default:
            $due_date = $task->due_date;
            break;
        }
        $task->due_date = $due_date;
        $task->update();

        if (!isset($request->task_id) && $task->user_id != null) {
            $email_body    = view('emails.task-to-user', compact('task'))->render();
            $email_subject = $task->description;
            try{
                $email_create = $this->createSentEmail($email_subject, "request@zoo-services.com", $task->user->email, $email_body);
                $email_options = new SendGeneralEmail("request@zoo-services.com", $email_subject, $email_body, $email_create["id"]);
                if (App::environment('production')) {
                    $email_options->sendEmail($task->user->email, $email_options->build());
                }else{
                    Mail::to($task->user->email)->send(new SendGeneralEmail("request@zoo-services.com", $email_subject, $email_body));
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Failed to send mail correctly');
            }
        }

        return redirect()->back()->with('success', 'Task created successfully');
    }

    /**
     * Delete order task.
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function deleteOrderTask($task_id)
    {
        $task = Task::findOrFail($task_id);
        $task->delete();

        return redirect()->back();
    }

    /**
     * Order email option.
     *
     * @param  int order_id
     * @param  string email_code
     * @return \Illuminate\Http\Response
     */
    public function sendEmailOption($id, $email_code, $is_action = false, Request $request)
    {
        if (!$is_action) {
            $order = Order::findOrFail($id);
        } else {
            $orderAction = OrderAction::where('id', $id)->first();
            $order       = $orderAction->order;
        }

        $number_email      = '#OR-' . $order->full_number;
        $email_from        = 'info@zoo-services.com';
        $email_attachments = [];
        $folderName        = $order->full_number;
        $active_subject = false;

        switch ($email_code) {
        case 'reservation_supplier':
            if (!Storage::exists('public/orders_docs/' . $folderName . '/' . 'Reservation supplier ' . $folderName . '.pdf')) {
                return redirect()->back()->with('error_msg', 'You need to generate the reservation supplier document.');
            }

            $email_to          = $order->supplier->email;
            $email_subject     = 'Order : Reservation supplier. ' . $number_email;
            $email_body        = view('emails.send-order-reservation-supplier', compact('order'))->render();
            $email_attachments = ['Reservation supplier ' . $folderName . '.pdf'];
            break;
        case 'reservation_client':
            if (!Storage::exists('public/orders_docs/' . $folderName . '/' . 'Reservation client ' . $folderName . '.pdf')) {
                return redirect()->back()->with('error_msg', 'You need to generate the reservation client document.');
            } elseif (!Storage::exists('public/orders_docs/' . $folderName . '/' . 'Checklist of documents ' . $folderName . '.pdf')) {
                return redirect()->back()->with('error_msg', 'You need to generate the Checklist client document.');
            }

            $email_to          = $order->client->email;
            $email_subject     = 'Order : Reservation client. ' . $number_email;
            $email_body        = view('emails.send-order-reservation-client', compact('order'))->render();
            $email_attachments = ['Reservation client ' . $folderName . '.pdf', 'Checklist of documents ' . $folderName . '.pdf'];
            break;
        case 'checklist_client':
            if (!Storage::exists('public/orders_docs/' . $folderName . '/' . 'Checklist of documents ' . $folderName . '.pdf')) {
                return redirect()->back()->with('error_msg', 'Generate the reservation client to get the checklist document.');
            }

            $email_to          = $order->client->email;
            $email_subject     = 'Order : Checklist of documents. ' . $number_email;
            $email_body        = view('emails.send-order-checklist-client', compact('order'))->render();
            $email_attachments = ['Checklist of documents ' . $folderName . '.pdf'];
            break;
        case 'deposit_payment_client':
            $email_to      = $order->client->email;
            $email_subject = 'Order : Deposit payment received. ' . $number_email;
            $email_body    = view('emails.deposit-payment-client-received', compact('order'))->render();
            break;
        case 'checklist_supplier':
            $email_to      = $order->supplier->email;
            $email_subject = 'Order : Checklist of documents. ' . $number_email;
            $email_body    = view('emails.send-order-checklist-supplier', compact('order'))->render();
            break;
        case 'deposit_payment_supplier':
            $email_to      = $order->supplier->email;
            $email_subject = 'Order : Deposit payment paid. ' . $number_email;
            $email_body    = view('emails.deposit-payment-supplier-paid', compact('order'))->render();
            break;
        case 'proforma_invoice':
            if (!Storage::exists('public/orders_docs/' . $folderName . '/' . 'Proforma invoice ' . $folderName . '.pdf')) {
                return redirect()->back()->with('error_msg', 'You need to generate the proforma invoice document.');
            }

            $email_to          = $order->client->email;
            $email_subject     = 'Order : Proforma invoice. ' . $number_email;
            $email_body        = view('emails.send-order-proforma-invoice', compact('order'))->render();
            $email_attachments = ['Proforma invoice ' . $folderName . '.pdf'];
            break;
        case 'statement_izs':
            if (!Storage::exists('public/orders_docs/' . $folderName . '/' . 'Statement IZS ' . $folderName . '.pdf')) {
                return redirect()->back()->with('error_msg', 'You need to generate the statement document.');
            }

            $email_to          = $order->client->email;
            $email_subject     = 'Order : Statement IZS. ' . $number_email;
            $email_body        = view('emails.send-statement-izs', compact('order'))->render();
            $email_attachments = ['Statement IZS ' . $folderName . '.pdf'];
            break;
        case 'cites_export_permit':
            $email_to      = $order->supplier->email;
            $email_subject = 'Cites export permit ' . $number_email;
            $email_body    = view('emails.cites-export-permit', compact('order'))->render();
            break;
        case 'cites_import_permit':
            $email_to      = $order->client->email;
            $email_subject = 'Cites import permit ' . $number_email;
            $email_body    = view('emails.cites-import-permit', compact('order'))->render();
            break;
        case 'apply_veterinary_client':
            $email_to      = $order->client->email;
            $email_subject = 'Veterinary import requirements ' . $number_email;
            $offer         = $order->offer;
            $email_body    = view('emails.apply-vet-requirements-client', compact('offer'))->render();
            break;
        case 'send_veterinary_supplier':
            $email_to      = $order->supplier->email;
            $email_subject = 'Veterinary import requirements ' . $number_email;
            $email_body    = view('emails.send-vet-requirements-supplier', compact('order'))->render();
            break;
        case 'costs_veterinary_preparations':
            $email_to      = $order->client->email;
            $email_subject = 'Total costs veterinary preparations ' . $number_email;
            $email_body    = view('emails.costs-veterinary-preparations-client', compact('order'))->render();
            break;
        case 'apply_health_certificate':
            $email_to      = $order->client->email;
            $email_subject = 'Health certificate ' . $number_email;
            $email_body    = view('emails.apply-health-certificate', compact('order'))->render();
            break;
        case 'send_healthcertificate_client':
            $email_to      = $order->client->email;
            $email_subject = 'Draft health certificate ' . $number_email;
            $email_body    = view('emails.send-draft-health-certificate', compact('order'))->render();
            break;
        case 'hc_approved_client':
            $email_to      = $order->supplier->email;
            $email_subject = 'Health certificate approved by client ' . $number_email;
            $email_body    = view('emails.hc-approved-by-client', compact('order'))->render();
            break;
        case 'apply_exterior_dimensions_crates':
            $email_to      = $order->supplier->email;
            $email_subject = 'Exterior dimensions crates ' . $number_email;
            $offer         = $order->offer;
            $email_body    = view('emails.apply-exterior-dimensions-crates-supplier', compact('offer'))->render();
            break;
        case 'send_dimensions_crates':
            $email_to      = ($order->airfreight_agent) ? $order->airfreight_agent->email : $order->supplier->email;
            $email_subject = 'Exterior dimensions crates ' . $number_email;
            $email_body    = view('emails.send-dimensions-crates-constructor', compact('order'))->render();
            break;
        case 'transport_quotation':
            $email_to      = ($order->airfreight_agent) ? $order->airfreight_agent->email : $order->supplier->email;
            $email_subject = 'Inquiry of airfreight quotation. ' . $number_email;

            $total_offer_specimens = 0;
            foreach ($order->offer->species_ordered as $species) {
                $total_offer_specimens += ($species->offerQuantityM + $species->offerQuantityF + $species->offerQuantityU);

                $species_crates           = $species->oursurplus->animal->crates;
                $species->crateDimensions = '';
                if ($species_crates->count() > 0) {
                    $species->crateDimensions = $species_crates[0]->length . ' x ' . $species_crates[0]->wide . ' x ' . $species_crates[0]->height . ' cm';
                }
            }

            $offer      = $order->offer;
            $email_body = view('emails.send-offer-freight-application', compact('offer', 'total_offer_specimens'))->render();
            break;
        case 'book_transport_shipper':
            $email_to      = ($order->airfreight_agent) ? $order->airfreight_agent->email : $order->supplier->email;
            $email_subject = 'Booking transport ' . $number_email;
            $email_body    = view('emails.apply-booking-transport', compact('order'))->render();
            break;
        case 'shipp_date_awb_client':
            $email_to      = $order->client->email;
            $email_subject = 'Shipping date ' . $number_email;
            $email_body    = view('emails.shipping-date-awb-client', compact('order'))->render();
            break;
        case 'to_email_link':
            $email_subject = $number_email;
            if(!empty($request->email_to)) {
                $email_to = $request->email_to;
            }
            $active_subject = true;
            $email_body = "<br><br>" . view('emails.email-signature')->render();
            break;
        default:
            $email_to      = '';
            $email_subject = '';
            $email_body    = '';
            break;
        }

        return view('orders.order_email_view', compact('order', 'email_code', 'email_from', 'email_to', 'email_subject', 'email_body', 'email_attachments', 'active_subject'));
    }

    /**
     * Order client invoice email.
     *
     * @param  int order_id
     * @param  int invoice_id
     * @return \Illuminate\Http\Response
     */
    public function sendClientInvoice($order_id, $invoice_id)
    {
        $order   = Order::findOrFail($order_id);
        $invoice = Invoice::findOrFail($invoice_id);

        $fileName = $invoice->invoice_file;

        if (!Storage::exists('public/orders_docs/' . $order->full_number . '/outgoing_invoices/' . $fileName)) {
            return redirect()->back()->with('error_msg', 'The pdf related with this invoice is missing.');
        }

        $email_code        = 'client_invoice';
        $email_from        = 'info@zoo-services.com';
        $email_to          = $order->client->email;
        $email_cc          = '';
        $email_bcc         = 'joke@zoo-services.com, dw51@verkoop.exactonline.nl, johnrens@zoo-services.com';
        $email_subject     = ucfirst($invoice->invoice_type) . ' Invoice - Order ' . $order->full_number;
        $email_body        = view('emails.send-order-client-invoice', compact('order'))->render();
        $email_attachments = [$fileName];

        return view('orders.order_email_view', compact('order', 'invoice', 'email_code', 'email_from', 'email_to', 'email_cc', 'email_subject', 'email_body', 'email_attachments', 'email_bcc'));
    }

    /**
     * Send email option.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function orderSendEmail(Request $request)
    {
        $order   = Order::findOrFail($request->order_id);
        $invoice = ($request->email_option === 'client_invoice') ? Invoice::findOrFail($request->invoice_id) : null;

        $email_bcc_array = [];
        if ($request->email_bcc != null) {
            $email_bcc_array = array_map('trim', explode(',', $request->email_bcc));
        }

        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }



        try{
            $email_create = $this->createSentEmail($request->email_subject, $request->email_from, $request->email_to, $request->email_body, $order->id);
            $email_options = new SendOrderEmailOptions($order, $invoice, $request->email_option, $request->email_from, $request->email_subject, $request->email_body, $email_create["id"]);
            if (App::environment('production')) {
                $email_options->sendEmail($request->email_to, $email_options->build(), $request->email_cc, $request->email_bcc);
            }else{
                Mail::to($request->email_to)->cc($email_cc_array)->bcc($email_bcc_array)->send(new SendOrderEmailOptions($order, $invoice, $request->email_option, $request->email_from, $request->email_subject, $request->email_body, $email_create["id"]));
            }
        } catch (\Throwable $th) {
            return redirect(route('orders.show', $order))->with('error', 'Failed to send mail correctly');
        }

        //$this->createTask($order);

        $orderAction = OrderAction::where('order_id', $order->id)->whereHas(
            'action', function ($query) use ($request) {
                $query->where('action_code', $request->email_option);
            }
        )->first();
        if ($orderAction != null) {
            $orderAction->update(['action_date' => Carbon::now()->format('Y-m-d H:i:s')]);
        }

        return redirect(route('orders.show', $order))->with('success', 'Email successfully sent.');
    }

    public function createTask($order)
    {
        $user                  = Auth::user();
        $task                  = new Task();
        $task['description']   = 'Remember to ask the customer if they received the order and if they have any questions';
        $task['action']        = 'reminder';
        $task['due_date']      = Carbon::now()->addDays(5);
        $task['user_id']       = $user->id;
        $task['taskable_id']   = $order->id;
        $task['taskable_type'] = 'order';
        $task['created_by']    = $user->id;
        $task['status']        = 'new';
        $task->save();
    }

    /**
     * Add actions to order.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addActionsToOrder(Request $request)
    {
        $orderId = $request->orderId;

        foreach ($request->actions as $actionId) {
            $action      = Action::where('id', $actionId)->first();
            $orderAction = OrderAction::where('action_id', $actionId)->first();
            if ($orderAction == null) {
                $newOrderAction             = new OrderAction();
                $newOrderAction->order_id   = $orderId;
                $newOrderAction->action_id  = $actionId;
                $newOrderAction->toBeDoneBy = $action->toBeDoneBy;
                $newOrderAction->save();
            }
        }

        return response()->json();
    }

    /**
     * Edit the selected actions.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editSelectedActions(Request $request)
    {
        if ($request->orderActionId == null) {
            foreach ($request->items as $id) {
                $actionToEdit = OrderAction::findOrFail($id);

                if (isset($request->actionDate)) {
                    $actionToEdit->update(['action_date' => $request->actionDate]);
                }
                if (isset($request->actionRemindDate)) {
                    $actionToEdit->update(['action_remind_date' => $request->actionRemindDate]);
                }
                if (isset($request->actionReceivedDate)) {
                    $actionToEdit->update(['action_received_date' => $request->actionReceivedDate]);
                }
                if (isset($request->actionRemark)) {
                    $actionToEdit->update(['remark' => $request->actionRemark]);
                }
                if (isset($request->toBeDoneBy)) {
                    $actionToEdit->update(['toBeDoneBy' => $request->toBeDoneBy]);
                }
            }
        } else {
            $actionToEdit = OrderAction::findOrFail($request->orderActionId);
            $actionToEdit->update(
                [
                'action_date'          => $request->actionDate,
                'action_remind_date'   => $request->actionRemindDate,
                'action_received_date' => $request->actionReceivedDate,
                'remark'               => $request->actionRemark,
                'toBeDoneBy'           => $request->toBeDoneBy,
                ]
            );
        }

        return response()->json();
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteSelectedActions(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $actionToDelete = OrderAction::findOrFail($id);
                $actionToDelete->delete();
            }
        }

        return response()->json();
    }

    public function validateInvoiceNumber(Request $request)
    {
        $this->validate(
            $request, [
            'bank_account_number' => 'required',
            ], [], []
        );

        $year         = Carbon::now()->format('Y');
        $invoiceValid = Invoice::whereYear('created_at', $year)->max('bank_account_number');
        $invoiceValid = ($invoiceValid) ? $invoiceValid + 1 : 0;
        $is_valid     = Invoice::whereYear('created_at', $year)->where('bank_account_number', $request->bank_account_number)->get();
        $invoice      = Invoice::find($request->id);

        if (!empty($is_valid->toArray()) && $invoice['bank_account_number'] != $request->bank_account_number) {
            return response()->json(['error' => true, 'message' => 'There are already invoices with that number. <br> The new number has to be ' . $invoiceValid, 'number' => $invoiceValid]);
        }
        if ($invoiceValid != $request->bank_account_number && $invoice['bank_account_number'] != $request->bank_account_number) {
            return response()->json(['error' => true, 'message' => 'The new number has to be ' . $invoiceValid, 'number' => $invoiceValid]);
        }

        return response()->json(['error' => false]);
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editSelectedRecords(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $order = Order::findOrFail($id);
                if (isset($request->order_number)) {
                    $order->update(['order_number' => $request->order_number]);
                }
                if (isset($request->created_at)) {
                    $order->update(['created_at' => $request->created_at]);
                }
                if (isset($request->order_status)) {
                    $order->update(['order_status' => $request->order_status]);
                    $order['realized_order_send'] = '1';
                    $order['realized_date']       = Carbon::now()->format('Y-m-d H:i:s');
                    $order->save();
                }
                if (isset($request->manager_id)) {
                    $order->update(['manager_id' => $request->manager_id]);
                }
                if (isset($request->delivery_country_id)) {
                    $order->update(['delivery_country_id' => $request->delivery_country_id]);
                }
                if (isset($request->delivery_airport_id)) {
                    $order->update(['delivery_airport_id' => $request->delivery_airport_id]);
                }
                if (isset($request->hidden_delivery_airport_id)) {
                    $order->update(['hidden_delivery_airport_id' => $request->hidden_delivery_airport_id]);
                }
                if (isset($request->cost_currency)) {
                    $order->update(['cost_currency' => $request->cost_currency]);
                }
                if (isset($request->cost_price_type)) {
                    $order->update(['cost_price_type' => $request->cost_price_type]);
                }
                if (isset($request->sale_currency)) {
                    $order->update(['sale_currency' => $request->sale_currency]);
                }
                if (isset($request->sale_price_type)) {
                    $order->update(['sale_price_type' => $request->sale_price_type]);
                }
                if (isset($request->cost_price_status)) {
                    $order->update(['cost_price_status' => $request->cost_price_status]);
                }
                if (isset($request->company)) {
                    $order->update(['company' => $request->company]);
                }
                if (isset($request->bank_account_id)) {
                    $order->update(['bank_account_id' => $request->bank_account_id]);
                }
                if (isset($request->realized_date)) {
                    $order->update(['realized_date' => $request->realized_date]);
                }
            }
        }

        return response()->json();
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createSentEmail($subject, $from, $email, $body, $id = null)
    {
        $label     = Labels::where('name', 'order')->first();
        $contact   = Contact::where('email', $email)->first();
        $new_email = new Email();
        if (!empty($contact) && $contact->count() > 0) {
            $first_name              = $contact['first_name'] ?? '';
            $last_name               = $contact['last_name']  ?? '';
            $name                    = $first_name . ' ' . $last_name;
            $new_email['contact_id'] = $contact['id'] ?? null;
        } else {
            $organisation                 = Organisation::where('email', $email)->first();
            $new_email['organisation_id'] = $organisation['id']   ?? null;
            $name                         = $organisation['name'] ?? '';
        }
        $new_email['from_email'] = $from;
        $new_email['to_email']   = $email;
        $new_email['body']       = $body;
        $new_email['guid']       = rand(1, 100);
        $new_email['subject']    = $subject;
        $new_email['name']       = $name;
        if (!empty($id)) {
            $new_email['order_id'] = $id;
        }
        $new_email['is_send'] = 1;
        $new_email->save();
        $new_email->labels()->attach($label);

        return $new_email;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function saveSentEmail($email)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        if(!empty($email)) {
            $userToken = $GraphService->getAllUserToken();
            if(!empty($userToken)) {
                foreach ($userToken as $row){
                    $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                    $user_id = $GraphService->getUserByEmail($token, $email["from_email"]);
                    if(!empty($token)) {
                        $email_attachment = [];
                        if(!empty($email->attachments)) {
                            foreach ($email->attachments as $key => $attachment) {
                                $email_attachment[$key]["name"] = $attachment->name;
                                $email_attachment[$key]["type"] = $attachment->type;
                                $email_attachment[$key]["content"] = file_get_contents(Storage::disk('')->path($attachment->path));
                            }
                        }
                        $email_cc_array = [];
                        if ($email["cc_email"] != null) {
                            $email_cc_array = array_map('trim', explode(',', $email["cc_email"]));
                        }

                        $email_bcc_array = [];
                        if ($email["bcc_email"] != null) {
                            $email_bcc_array = array_map('trim', explode(',', $email["bcc_email"]));
                        }
                        $result = $GraphService->saveSentItems($token,  $user_id->getId(), $email["subject"], $email["body"], $email["to_email"],  $email_cc_array,  $email_bcc_array, $email_attachment);
                        $email["guid"] = $result["id"];
                        $email->save();
                        if(!empty($result)) {
                            $result = $GraphService->updateIsDraftEmailInbox($token,  $user_id->getId(), $result["id"]);
                        }
                    }
                }
            }
        }
    }

    public function resetListEmailNewOrderSend()
    {
        $orders = Order::where("new_order_send", 1)->get();
        if(!empty($orders)) {
            foreach ($orders as $row) {
                $row['new_order_send'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Orders';

        return view('components.reset_list_email_new', compact('title_dash'));
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetListEmailRealizedOrderSend()
    {
        $orders = Order::where('realized_order_send', 1)->get();
        if (!empty($orders)) {
            foreach ($orders as $row) {
                $row['realized_order_send'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Orders';

        return view('components.reset_list_email_new', compact('title_dash'));
    }
}
