<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Airport;
use App\Models\Offer;
use App\Models\OfferAction;
use App\Models\Order;
use App\Models\OrderAction;
use App\Models\Contact;
use App\Models\AreaRegion;
use App\Models\Country;
use App\Models\Organisation;
use App\Models\Task;
use App\Models\User;
use App\Models\Crate;
use App\Models\CurrencyRate;
use App\Models\AdditionalCost;
use App\Models\OfferAdditionalCost;
use App\Models\OfferSpecies;
use App\Models\OurSurplus;
use App\Models\Surplus;
use App\Models\Animal;
use App\Models\Airfreight;
use App\Models\OfferAirfreightPallet;
use App\Models\OfferTransportTruck;
use App\Models\OfferSpeciesAirfreight;
use App\Models\OfferSpeciesCrate;
use App\Models\Role;
use App\Models\Origin;
use App\Models\Email;
use App\Models\Labels;
use App\Http\Requests\OfferCreateRequest;
use App\Http\Requests\OfferUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Enums\AgeGroup;
use App\Enums\Size;
use App\Enums\Currency;
use App\Enums\OfferStatus;
use App\Enums\OfferStatusLevel;
use App\Enums\ShipmentTerms;
use App\Enums\OfferOrderByOptions;
use App\Enums\ActionOfferCategory;
use App\Enums\TaskActions;
use App\Enums\ConfirmOptions;
use App\Services\OfferService;
use App\Mail\SendOfferEmailOptions;
use App\Mail\SendGeneralEmail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OffersExport;
use App\Models\Color;
use App\Models\Dashboard;
use App\Models\Region;
use App\Services\GraphService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use DOMPDF;


class OfferController extends Controller
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
        if ($user->hasPermission('offers.see-all-offers')) {
            $offers = Offer::select('*', 'offers.id as offerId', 'offers.created_at as created_date');
        } else {
            $offers = Offer::select('*', 'offers.id as offerId', 'offers.created_at as created_date');
        }

        $offerStatuses = OfferStatus::get();
        $offerStatuses = Arr::prepend($offerStatuses, 'All', 'all');

        $offerStatusesLevel = OfferStatusLevel::get();
        $offerStatusesLevel = Arr::prepend($offerStatusesLevel, 'All', 'all');
        $roles              = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins             = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');

        $orderByOptions   = OfferOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;
        $statusField      = null;
        $statusLevelField = null;

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('offer.filter')) {
            $request = session('offer.filter');

            if (isset($request['statusField']) && $request['statusField'] != 'all') {
                $statusField = $request['statusField'];

                $offers->where('offer_status', $statusField);

                $filterData = Arr::add($filterData, 'statusField', 'Status: ' . $statusField);
            }

            if (isset($request['statusLevelField']) && $request['statusLevelField'] != 'all') {
                $statusLevelField = $request['statusLevelField'];
                $statusLevel      = $request['statusLevelField'];

                $offers->where('status_level', $statusLevelField);

                if ($statusLevelField === 'Forapproval') {
                    $statusLevel = 'For approval';
                }

                if ($statusLevelField === 'Tosearch') {
                    $statusLevel = 'To search';
                }

                $filterData = Arr::add($filterData, 'statusLevelField', 'Status Level: ' . $statusLevel);
            }

            if (isset($request['filter_request_number'])) {
                $offers->where('offer_number', $request['filter_request_number']);

                $filterData = Arr::add($filterData, 'filter_request_number', 'Offer No: ' . $request['filter_request_number']);
            }

            if (isset($request['filter_price_type'])) {
                $offers->where('sale_price_type', $request['filter_price_type']);

                $filterData = Arr::add($filterData, 'filter_price_type', 'Price type: ' . $request['filter_price_type']);
            }

            if (isset($request['filter_to_remind'])) {
                if ($request['filter_to_remind'] === 'yes') {
                    $offers->whereNotNull('next_reminder_at');
                } else {
                    $offers->whereNull('next_reminder_at');
                }

                $filterData = Arr::add($filterData, 'filter_to_remind', 'To remind: ' . $request['filter_to_remind']);
            }

            if (isset($request['filter_animal_id'])) {
                $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                $offers->whereHas('offer_species.oursurplus', function ($query) use ($filterAnimal) {
                    $query->where('our_surplus.animal_id', $filterAnimal->id);
                });

                $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
            }

            if (isset($request['filter_client_id'])) {
                $filterClient             = Contact::where('id', $request['filter_client_id'])->first();
                $filterClientOrganisation = Organisation::where('id', $request['filter_client_id'])->first();

                if (!empty($filterClient)) {
                    $offers->where('client_id', $filterClient->id);

                    if ($offers->count() > 0) {
                        $filterData = Arr::add($filterData, 'filter_client_id', 'Client: ' . $filterClient->full_name);
                    }
                }
                if (!empty($filterClientOrganisation)) {
                    $offers->orWhere('institution_id', $filterClientOrganisation->id)->whereNull('client_id');

                    if ($offers->count() > 0) {
                        $filterData = Arr::add($filterData, 'filter_client_id', 'Client: ' . $filterClientOrganisation->name);
                    }
                }
            }

            if (isset($request['filter_supplier_id'])) {
                $filterSupplier = Contact::where('id', $request['filter_supplier_id'])->first();

                $offers->where('supplier_id', $filterSupplier->id);

                $filterData = Arr::add($filterData, 'filter_supplier_id', 'Supplier: ' . $filterSupplier->full_name);
            }

            if (isset($request['filter_start_date'])) {
                $offers->where('created_at', '>=', $request['filter_start_date']);

                $filterData = Arr::add($filterData, 'filter_start_date', 'Created start at: ' . $request['filter_start_date']);
            }

            if (isset($request['filter_end_date'])) {
                $offers->where('created_at', '<=', $request['filter_end_date']);

                $filterData = Arr::add($filterData, 'filter_end_date', 'Created end at: ' . $request['filter_end_date']);
            }

            if (isset($request['filter_intern_remarks'])) {
                $offers->where('remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_intern_remarks', 'Remarks: ' . $request['filter_intern_remarks']);
            }

            if (isset($request['filter_manager_id'])) {
                $user     = User::find($request['filter_manager_id']);
                $fullName = $user->name . ' ' . $user->last_name;

                $offers->where('manager_id', $request['filter_manager_id']);

                $filterData = Arr::add($filterData, 'filter_manager_id', 'User: ' . $fullName);
            }

            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                $offers->orderBy($orderByField, $orderByDirection);
            } else {
                $offers->orderByRaw('next_reminder_at is null')
                    ->orderBy('next_reminder_at')
                    ->orderByDesc(DB::raw('YEAR(created_at)'))
                    ->orderByDesc('offer_number');
            }

            if (isset($request['filter_inquiries'])) {
                if($request['filter_inquiries'] == "yes"){
                    $offers->where('creator', 'Client');
                }else{
                    $offers->where('creator', 'IZS');
                }

                $filterData = Arr::add($filterData, 'filter_inquiries', 'Inquiries from website: ' . $request['filter_inquiries']);
            }
        }

        $offers = $offers->paginate(50);

        $currencies      = Currency::get();
        $price_type      = ShipmentTerms::get();
        $roles           = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins          = User::whereRoleIs(Arr::pluck($roles, 'name'))->orderBy('name')->pluck('name', 'id');
        $confirm_options = ConfirmOptions::get();

        /*$query = session('offer.filter');
        $query['offerTab'] = '#animalsTab';
        session(['offer.filter' => $query]);*/

        return view('offers.index', compact(
            'offers',
            'offerStatuses',
            'offerStatusesLevel',
            'statusField',
            'statusLevelField',
            'orderByOptions',
            'orderByDirection',
            'orderByField',
            'filterData',
            'currencies',
            'price_type',
            'confirm_options',
            'admins'
        ));
    }

    /**
     * getTodayOfferAction
     *
     * @return void
     */
    public static function getTodayOfferAction()
    {
        $offerActions = OfferAction::whereDate('created_at', Carbon::today()->format('Y-m-d'))->get();

        return $offerActions;
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('offer.filter');

        return redirect(route('offers.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //This is not used.
        //$customers = Contact::pluck('first_name', 'id');

        //This is not used, and makes the insert page very slow.
        /*$customers = Contact::orderBy('first_name')->get()->mapWithKeys(function ($contact) {
            return [$contact->id => $contact->first_name .' '. $contact->last_name . ' ('. ($contact->organisation == null ? '-' : $contact->organisation->name) .')'];
        });*/

        $countries = Country::orderBy('name')->pluck('name', 'id');
        $cities    = Airport::orderBy('name')->orderBy('city')->pluck('city', 'id');

        $currencies = Currency::get();
        $status     = OfferStatus::get();
        $price_type = ShipmentTerms::get();
        $roles      = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins     = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');

        return view('offers.create', compact(
            'countries',
            'cities',
            'currencies',
            'status',
            'price_type',
            'admins'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OfferCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OfferCreateRequest $request)
    {
        $request['creator']      = 'IZS';
        $offerNumber             = Offer::ofCurrentYear()->max('offer_number');
        $request['offer_number'] = ($offerNumber) ? $offerNumber + 1 : 1;

        if ($request->client_id == null) {
            $request['client_id'] = $request->contact_client_id;
        }

        if ($request->institution_id == null) {
            $request['institution_id'] = $request->institution_client_id;
        }

        if ($request->supplier_id != null) {
            $request['supplier_id'] = $request->supplier_id;
        } elseif ($request->contact_supplier_id != null) {
            $request['supplier_id'] = $request->contact_supplier_id;
        } else {
            $izsContact = Contact::where('email', 'izs@zoo-services.com')->first();

            $request['supplier_id'] = ($izsContact) ? $izsContact->id : null;
        }

        $request['times_reminded']    = 0;
        $request['new_offer_inquiry'] = 1;
        $offer                        = Offer::create($request->all());

        $additionalCosts = AdditionalCost::get();

        foreach ($additionalCosts as $ac) {
            if ($ac->is_test === 1) {
                $quantity = 0;
            } else {
                $quantity = 1;
            }
            $newOfferAdditionalCost            = new OfferAdditionalCost();
            $newOfferAdditionalCost->offer_id  = $offer->id;
            $newOfferAdditionalCost->name      = $ac->name;
            $newOfferAdditionalCost->quantity  = $quantity;
            $newOfferAdditionalCost->currency  = $request->offer_currency;
            $newOfferAdditionalCost->costPrice = ($request->offer_currency == 'USD') ? $ac->usdCostPrice : $ac->eurCostPrice;
            $newOfferAdditionalCost->salePrice = ($request->offer_currency == 'USD') ? $ac->usdSalePrice : $ac->eurSalePrice;
            $newOfferAdditionalCost->is_test   = $ac->is_test;
            $newOfferAdditionalCost->save();
        }

        $actions = Action::where(function ($query) {
            $query->where('belongs_to', 'Offer')
                ->orWhere('belongs_to', 'Offer_Order');
        })->get();
        foreach ($actions as $action) {
            $offerAction             = new OfferAction();
            $offerAction->offer_id   = $offer->id;
            $offerAction->action_id  = $action->id;
            $offerAction->toBeDoneBy = $action->toBeDoneBy;
            $offerAction->save();
        }

        $query             = session('offer.filter');
        $query['offerTab'] = '#animalsTab';
        session(['offer.filter' => $query]);

        return redirect(route('offers.show', $offer->id));
    }

    /**
     * Add species to an offer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addOfferSpecies(Request $request)
    {
        foreach ($request->items as $os) {
            $ourSurplus = OurSurplus::where('id', $os[0])->first();

            $offer = Offer::find($request->offerId);

            $newOfferSpecies                  = new OfferSpecies();
            $newOfferSpecies->offer_id        = $request->offerId;
            $newOfferSpecies->oursurplus_id   = $os[0];
            $newOfferSpecies->offerQuantityM  = $os[1];
            $newOfferSpecies->offerQuantityF  = $os[2];
            $newOfferSpecies->offerQuantityU  = $os[3];
            $newOfferSpecies->offerQuantityP  = $os[4];
            $newOfferSpecies->offerCostPriceM = $ourSurplus->salePriceM * 0.80;
            $newOfferSpecies->offerCostPriceF = $ourSurplus->salePriceF * 0.80;
            $newOfferSpecies->offerCostPriceU = $ourSurplus->salePriceU * 0.80;
            $newOfferSpecies->offerCostPriceP = $ourSurplus->salePriceP * 0.80;
            $newOfferSpecies->offerSalePriceM = ($os[1] > 0) ? $ourSurplus->salePriceM : 0;
            $newOfferSpecies->offerSalePriceF = ($os[2] > 0) ? $ourSurplus->salePriceF : 0;
            $newOfferSpecies->offerSalePriceU = ($os[3] > 0) ? $ourSurplus->salePriceU : 0;
            $newOfferSpecies->offerSalePriceP = ($os[1] > 0 && $os[1] == $os[2] && $ourSurplus->salePriceP > 0) ? $ourSurplus->salePriceP : 0;
            $newOfferSpecies->origin          = $os[5];
            $newOfferSpecies->region_id       = $os[6];
            $newOfferSpecies->save();

            $newOfferSpeciesCrate                   = new OfferSpeciesCrate();
            $newOfferSpeciesCrate->offer_species_id = $newOfferSpecies->id;
            $newOfferSpeciesCrate->quantity_males   = $newOfferSpecies->offerQuantityM;
            $newOfferSpeciesCrate->quantity_females = $newOfferSpecies->offerQuantityF;
            $newOfferSpeciesCrate->quantity_unsexed = $newOfferSpecies->offerQuantityU;
            $newOfferSpeciesCrate->quantity_pairs   = $newOfferSpecies->offerQuantityP;
            $newOfferSpeciesCrate->save();

            if (count($newOfferSpecies->species_crates) > 0) {
                $firstSpeciesCrate = $newOfferSpecies->species_crates[0];
                $newOfferSpeciesCrate->update(['crate_id' => $firstSpeciesCrate->id, 'length' => $firstSpeciesCrate->length, 'wide' => $firstSpeciesCrate->wide, 'height' => $firstSpeciesCrate->height, 'cost_price' => $firstSpeciesCrate->cost_price ?? 0, 'sale_price' => $firstSpeciesCrate->sale_price ?? 0]);
            }

            if (!empty($offer->delivery_country)) {
                $departure_continent = $os[6];
                $arrival_continent   = $offer->delivery_country->region_id;
                if ($offer->airfreight_type == 'pallets') {
                    $isPallet = true;
                } else {
                    $isPallet = false;
                }

                $airfreight = $this->getAirfreightsByCountriesAndAirports($departure_continent, $arrival_continent, $isPallet);
                if (!empty($airfreight)) {
                    $airfreight_cost_volKg = $airfreight[0]['volKg_weight_cost'];
                    $airfreight_sale_volKg = $airfreight_cost_volKg * 1.12;
                    $airfreight_id         = $airfreight[0]['id'];
                } else {
                    $airfreight_id         = null;
                    $airfreight_cost_volKg = 0;
                    $airfreight_sale_volKg = 0;
                }
            } else {
                $airfreight_id         = null;
                $airfreight_cost_volKg = 0;
                $airfreight_sale_volKg = 0;
            }

            $newOfferSpeciesAirfreight                   = new OfferSpeciesAirfreight();
            $newOfferSpeciesAirfreight->offer_species_id = $newOfferSpecies->id;
            $newOfferSpeciesAirfreight->airfreight_id    = $airfreight_id;
            $newOfferSpeciesAirfreight->cost_volKg       = $airfreight_cost_volKg;
            $newOfferSpeciesAirfreight->sale_volKg       = $airfreight_sale_volKg;
            $newOfferSpeciesAirfreight->save();
        }

        return response()->json();
        //return redirect(route('offers.show', $request->offer_id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $origin            = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup          = AgeGroup::get();
        $sizes             = Size::get();
        $price_type        = ShipmentTerms::get();
        $task_actions      = TaskActions::get();
        $action_categories = ActionOfferCategory::get();
        $regionsNames      = Region::pluck('name', 'id');
        $dashboards        = Dashboard::where('main', 1)->orderBy('order', 'ASC')->get();
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $cities    = Airport::orderBy('name')->pluck('city', 'id');
        $currencies = Currency::get();
        $status     = OfferStatus::get();
        $price_type = ShipmentTerms::get();
        $offerStatusesLevel = OfferStatusLevel::get();

        $html = '<div class="custom-dd dd" id="nestable_list_1">
                    <ol class="dd-list">
                        ';
        $html = $this->getHtmlDashboarSon($dashboards, $html);
        $html .= '</div>
        </ol>
            ';

        $dashboards = $html;

        $offer = $this->offerService->calculate_offer_totals($id);

        $user = Auth::user();
        if (!empty($user->id) && $user->id === 2) {
            $offer_update    = Offer::find($id);
            $date            = Carbon::now();
            $date            = $date->subDay(7);
            $offer_seven_day = Offer::where('offer_send_out', 1)
                ->where('id', $id)
                ->whereDate('next_reminder_at', '<=', $date->format('Y-m-d'))
                ->first();
            if (!empty($offer_seven_day)) {
                $offer_seven_day['offer_send_out'] = 0;
                $offer_seven_day->save();
            }
            $offer_update['new_offer_send']        = 0;
            $offer_update['new_offer_forapproval'] = 0;
            $offer_update->save();
        }

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $areas        = AreaRegion::orderBy('name')->pluck('name', 'id');
        $regions      = Region::orderBy('name')->pluck('name', 'id');
        $from_country = Country::orderBy('name')->pluck('name', 'id');
        $to_country   = Country::orderBy('name')->pluck('name', 'id');

        $roles = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $users = User::whereRoleIs(Arr::pluck($roles, 'name'))->orderBy('name')->pluck('name', 'id');
        $admins = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');

        $generalActions = OfferAction::where('offer_id', $offer->id)->whereHas('action', function ($query) {
            $query->where('category', 'offer');
        })->get();
        $veterinaryActions = OfferAction::where('offer_id', $offer->id)->whereHas('action', function ($query) {
            $query->where('category', 'veterinary');
        })->get();
        $crateActions = OfferAction::where('offer_id', $offer->id)->whereHas('action', function ($query) {
            $query->where('category', 'crate');
        })->get();
        $transportActions = OfferAction::where('offer_id', $offer->id)->whereHas('action', function ($query) {
            $query->where('category', 'transport');
        })->get();

        $selectedOfferTab = '#animalsTab';
        if (session()->has('offer.filter')) {
            $sessionData = session('offer.filter');

            if (isset($sessionData['offerTab'])) {
                $selectedOfferTab = $sessionData['offerTab'];
            }
        }
        if (!empty($offer) && $offer->client) {
            $client = $offer->client;
        } else {
            $client = $offer->organisation;
        }

        $emails_received = Email::where("offer_id", $id)->where("is_send", 0);
        $emails = Email::where("offer_id", $id)->where("is_send", 1);

        if (session()->has('offer.filterShow')) {
            $sessionData = session('offer.filterShow');
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

        $filter = 'offers.filterEmailsOffer';

        return view('offers.show', compact(
            'offer',
            'selectedOfferTab',
            'origin',
            'ageGroup',
            'sizes',
            'price_type',
            'general_rate_usd',
            'general_rate_eur',
            'areas',
            'regions',
            'from_country',
            'to_country',
            'task_actions',
            'users',
            'action_categories',
            'generalActions',
            'veterinaryActions',
            'crateActions',
            'transportActions',
            'regionsNames',
            'emails_received',
            'emails',
            'dashboards',
            'countries',
            'cities',
            'currencies',
            'status',
            'price_type',
            'offerStatusesLevel',
            'admins',
            'sortselected',
            'filter',
        ));
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
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function edit(Offer $offer)
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $cities    = Airport::orderBy('name')->pluck('city', 'id');

        $currencies = Currency::get();
        $status     = OfferStatus::get();
        $price_type = ShipmentTerms::get();

        $offerStatusesLevel = OfferStatusLevel::get();
        //$offerStatusesLevel = Arr::prepend($offerStatusesLevel, 'All', 'all');
        $roles  = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');

        return view('offers.edit', compact(
            'offer',
            'offerStatusesLevel',
            'countries',
            'cities',
            'currencies',
            'status',
            'price_type',
            'admins'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OfferUpdateRequest  $request
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function update(OfferUpdateRequest $request, Offer $offer)
    {
        if ($request->client_id != null) {
            if ($offer->client_id != $request->client_id) {
                $request['client_id'] = $request->client_id;
            }
        } elseif ($request->contact_client_id != null) {
            if ($offer->client_id != $request->contact_client_id) {
                $request['client_id'] = $request->contact_client_id;
            }
        } else {
            $request['client_id'] = $offer->client_id;
        }

        if ($request->supplier_id != null) {
            if ($offer->supplier_id != $request->supplier_id) {
                $request['supplier_id'] = $request->supplier_id;
            }
        } elseif ($request->contact_supplier_id != null) {
            if ($offer->supplier_id != $request->contact_supplier_id) {
                $request['supplier_id'] = $request->contact_supplier_id;
            }
        } else {
            $request['supplier_id'] = $offer->supplier_id;
        }

        if (isset($offer->offer_currency) && $offer->offer_currency != $request->offer_currency) {
            $offer->additional_costs()->update(['currency' => $request->offer_currency]);
        }

        if (isset($offer->offer_status)) {
            if ($offer->offer_status === 'Pending' && $offer->offer_status != $request->offer_status) {
                $offer->update(['next_reminder_at' => null, 'times_reminded' => 0]);
            } elseif ($offer->offer_status !== 'Pending' && $request->offer_status === 'Pending') {
                $offer->update(['next_reminder_at' => Carbon::now()->addDays(5), 'times_reminded' => 0]);
            }

            if ($request->status_level === 'Sendoffer') {
                $offer->update(['status_level' => 'Sendoffer']);
                $offer['new_offer_forapproval'] = 0;
                $offer['new_offer_send']        = 1;
                $offer['offer_send_out']        = 1;
                $offer->save();
            }

            if ($request->status_level === 'Forapproval') {
                $offer->update(['status_level' => 'Forapproval']);
                $offer['new_offer_forapproval'] = 1;
                $offer['new_offer_send']        = 0;
                $offer['offer_send_out']        = 0;
                $request['new_offer_inquiry']   = 0;
                $offer->save();
            }

            if ($request->status_level === 'Inquiry') {
                $offer->update(['status_level' => 'Inquiry']);
                $offer['new_offer_forapproval'] = 0;
                $request['new_offer_inquiry']   = 1;
                $offer['new_offer_send']        = 0;
                $offer['offer_send_out']        = 0;
                $offer->save();
            }

            if ($offer->offer_status != $request->offer_status && $request->offer_status == 'Ordered' && $offer->order == null) {
                $orderData = [];

                $year                      = Carbon::now()->format('Y');
                $orderNumber               = Order::whereYear('created_at', $year)->max('order_number');
                $orderData['order_number'] = ($orderNumber) ? $orderNumber + 1 : 1;

                $orderData['offer_id']            = $offer->id;
                $orderData['manager_id']          = auth()->user()->id;
                $orderData['client_id']           = $request->client_id;
                $orderData['supplier_id']         = $request->supplier_id;
                $orderData['airfreight_agent_id'] = $request->airfreight_agent_id;
                $orderData['delivery_country_id'] = $offer->delivery_country->id;
                $orderData['delivery_airport_id'] = $offer->delivery_airport->id;
                $orderData['cost_currency']       = $offer->offer_currency;
                $orderData['sale_currency']       = $offer->offer_currency;
                $orderData['company']             = 'IZS_BV';
                $orderData['bank_account_id']     = ($offer->offer_currency == 'USD') ? 1 : 2;
                $orderData['order_status']        = 'Pending';
                $orderData['order_remarks']       = $offer->remarks;
                $orderData['cost_price_type']     = 'ExZoo';
                $orderData['sale_price_type']     = $offer->sale_price_type;
                $orderData['cost_price_status']   = $offer->cost_price_status;

                $newOrder = Order::create($orderData);

                $orderActions = Action::where(function ($query) {
                    $query->where('belongs_to', 'Order')
                        ->orWhere('belongs_to', 'Offer_Order');
                })->get();
                foreach ($orderActions as $orderAction) {
                    $newOrderAction             = new OrderAction();
                    $newOrderAction->order_id   = $newOrder->id;
                    $newOrderAction->action_id  = $orderAction->id;
                    $newOrderAction->toBeDoneBy = $orderAction->toBeDoneBy;
                    $newOrderAction->save();
                }
            } elseif ($offer->offer_status == $request->offer_status && $request->offer_status == 'Ordered') {
                if ($request->client_id != null) {
                    if ($offer->client_id != $request->client_id) {
                        $offer->order->update(['client_id' => $request->client_id]);
                    }
                } elseif ($request->contact_client_id != null) {
                    if ($offer->client_id != $request->contact_client_id) {
                        $offer->order->update(['client_id' => $request->contact_client_id]);
                    }
                }

                if ($request->supplier_id != null) {
                    if ($offer->supplier_id != $request->supplier_id) {
                        $offer->order->update(['supplier_id' => $request->supplier_id]);
                    }
                } elseif ($request->contact_supplier_id != null) {
                    if ($offer->supplier_id != $request->contact_supplier_id) {
                        $offer->order->update(['supplier_id' => $request->contact_supplier_id]);
                    }
                }

                if ($request->airfreight_agent_id != null && $offer->airfreight_agent_id != $request->airfreight_agent_id) {
                    $offer->order->update(['airfreight_agent_id' => $request->airfreight_agent_id]);
                }

                if ($request->cost_price_status != null && $offer->cost_price_status != $request->cost_price_status) {
                    $offer->order->update(['cost_price_status' => $request->cost_price_status]);
                }
            } elseif ($offer->offer_status == 'Ordered' && $request->offer_status != 'Ordered') {
                $offer->order->delete();
            }
        }

        $offer->update($request->all());

        return redirect(route('offers.show', $offer->id));
    }

    public function updateRemark(Request $request)
    {
        $offer = Offer::find($request->id);
        if (!empty($offer)) {
            $offer['remarks'] = $request->remarks ?? '';
            $offer->save();

            return response()->json(['error' => false, 'message' => 'The remark was updated successfully', 'remark' => $offer['remarks']]);
        } else {
            return response()->json(['error' => true, 'message' => 'Offer not found']);
        }
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editSelectedRecords(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $offer = Offer::findOrFail($id);

                if (isset($request->offer_status)) {
                    if ($offer->offer_status === 'Pending' && $offer->offer_status != $request->offer_status) {
                        $offer->update(['next_reminder_at' => null, 'times_reminded' => 0]);
                    } elseif ($offer->offer_status !== 'Pending' && $request->offer_status === 'Pending') {
                        $offer->update(['next_reminder_at' => Carbon::now()->addDays(5), 'times_reminded' => 0]);
                    }

                    if ($offer->offer_status != $request->offer_status && $request->offer_status == 'Ordered' && $offer->order == null) {
                        $orderData = [];

                        $year                      = Carbon::now()->format('Y');
                        $orderNumber               = Order::whereYear('created_at', $year)->max('order_number');
                        $orderData['order_number'] = ($orderNumber) ? $orderNumber + 1 : 1;

                        $orderData['offer_id']            = $offer->id;
                        $orderData['manager_id']          = auth()->user()->id;
                        $orderData['client_id']           = $offer->client->id;
                        $orderData['supplier_id']         = ($offer->supplier) ? $offer->supplier->id : null;
                        $orderData['airfreight_agent_id'] = ($offer->airfreight_agent) ? $offer->airfreight_agent->id : null;
                        $orderData['delivery_country_id'] = $offer->delivery_country->id;
                        $orderData['delivery_airport_id'] = $offer->delivery_airport->id;
                        $orderData['cost_currency']       = $offer->offer_currency;
                        $orderData['sale_currency']       = $offer->offer_currency;
                        $orderData['company']             = 'IZS_BV';
                        $orderData['bank_account_id']     = ($offer->offer_currency == 'USD') ? 1 : 2;
                        $orderData['order_status']        = 'Pending';
                        $orderData['order_remarks']       = $offer->remarks;
                        $orderData['cost_price_type']     = 'ExZoo';
                        $orderData['sale_price_type']     = $offer->sale_price_type;
                        $orderData['cost_price_status']   = $offer->cost_price_status;

                        $newOrder = Order::create($orderData);

                        $orderActions = Action::where(function ($query) {
                            $query->where('belongs_to', 'Order')
                                ->orWhere('belongs_to', 'Offer_Order');
                        })->get();
                        foreach ($orderActions as $orderAction) {
                            $newOrderAction             = new OrderAction();
                            $newOrderAction->order_id   = $newOrder->id;
                            $newOrderAction->action_id  = $orderAction->id;
                            $newOrderAction->toBeDoneBy = $orderAction->toBeDoneBy;
                            $newOrderAction->save();
                        }
                    } elseif ($offer->offer_status == 'Ordered' && $request->offer_status != 'Ordered') {
                        $offer->order->delete();
                    }

                    $offer->update(['offer_status' => $request->offer_status]);
                }

                if (isset($request->status_level)) {
                    $offer->update(['status_level' => $request->status_level]);
                }

                if (isset($request->offer_currency)) {
                    if ($offer->order != null) {
                        $offer->order->update(['cost_currency' => $request->offer_currency, 'sale_currency' => $request->offer_currency]);
                    }
                    $offer->update(['offer_currency' => $request->offer_currency]);
                }

                if (isset($request->offer_type)) {
                    if ($offer->order != null) {
                        $offer->order->update(['sale_price_type' => $request->offer_type]);
                    }
                    $offer->update(['sale_price_type' => $request->offer_type]);
                }

                if (isset($request->client_id)) {
                    if ($offer->order != null) {
                        $offer->order->update(['client_id' => $request->client_id]);
                    }
                    $offer->update(['client_id' => $request->client_id]);
                }

                if (isset($request->supplier_id)) {
                    if ($offer->order != null) {
                        $offer->order->update(['supplier_id' => $request->supplier_id]);
                    }
                    $offer->update(['supplier_id' => $request->supplier_id]);
                }

                if (isset($request->manager_id)) {
                    if ($offer->order != null) {
                        $offer->order->update(['manager_id' => $request->manager_id]);
                    }
                    $offer->update(['manager_id' => $request->manager_id]);
                }
            }
        }

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Offer  $offer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        if ($offer->order != null) {
            return redirect()->back()->with('error_msg', 'This offer is related with an order. You cannot delete it.');
        } else {
            Storage::deleteDirectory('public/offers_docs/' . $offer->full_number);
            $offer->delete();

            return redirect(route('offers.index'));
        }
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        $offerRelatedWithOrder = false;

        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $offerDelete = Offer::findOrFail($id);

                if ($offerDelete->order == null) {
                    Storage::deleteDirectory('public/offers_docs/' . $offerDelete->full_number);
                    $offerDelete->delete();
                } else {
                    $offerRelatedWithOrder = true;
                }
            }
        }

        if ($offerRelatedWithOrder) {
            return response()->json(['success' => false, 'warning_msg' => 'There are offers related with orders that were not deleted.']);
        } else {
            return response()->json(['success' => true]);
        }
    }

    /**
     * Remove the selected species.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_species(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $offerSpeciesDelete = OfferSpecies::findOrFail($id);
                $offerSpeciesDelete->delete();
            }
        }

        return response()->json();
    }

    /**
     * Upload files related with the offer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload_file(Request $request)
    {
        $filesize = ini_get('upload_max_filesize');
        $filesize = str_replace('M', '', $filesize);
        if (is_numeric($filesize)) {
            $max = $filesize * 1024;
        } else {
            $max = 10240;
        }

        $request->validate(['file' => 'required|file|max:' . $max]);
        if ($request->hasFile('file')) {
            $offer = Offer::findOrFail($request->offerId);

            $docCategory = $request->docCategory;

            $folderName = $offer->full_number;

            $file = $request->file('file');

            //File Name
            $file_name = $file->getClientOriginalName();

            switch ($docCategory) {
                case 'airfreight':
                    $path = Storage::putFileAs('public/offers_docs/' . $folderName . '/airfreight/', $file, $file_name);

                    if ($offer->airfreight_type === 'pallets' && $offer->airfreight_pallet != null && $offer->airfreight_pallet->airfreight_id != null) {
                        Storage::copy('public/offers_docs/' . $folderName . '/airfreight/' . $file_name, 'public/airfreights_docs/' . $offer->airfreight_pallet->airfreight_id . '/' . $file_name);
                    }
                    break;
                case 'crates':
                    $path = Storage::putFileAs('public/offers_docs/' . $folderName . '/crates/', $file, $file_name);
                    break;
                case 'cites_docs':
                    $path = Storage::putFileAs('public/offers_docs/' . $folderName . '/cites_docs/', $file, $file_name);
                    break;
                case 'veterinary_docs':
                    $path = Storage::putFileAs('public/offers_docs/' . $folderName . '/veterinary_docs/', $file, $file_name);
                    break;
                case 'documents':
                    $path = Storage::putFileAs('public/offers_docs/' . $folderName . '/documents/', $file, $file_name);
                    break;
                case 'suppliers_offers':
                    $path = Storage::putFileAs('public/offers_docs/' . $folderName . '/suppliers_offers/', $file, $file_name);
                    break;
                default:
                    $path = Storage::putFileAs('public/offers_docs/' . $folderName, $file, $file_name);
                    break;
            }
        }

        //return redirect()->back()->with('status', 'Successfully uploaded file');
        return response()->json(['success' => true], 200);
    }

    /**
     * Upload files related with the offer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadOfferActionDocument(Request $request)
    {
        $offerAction = OfferAction::where('id', $request->id)->first();

        if ($request->hasFile('file_to_upload')) {
            $file = $request->file('file_to_upload');

            //File Name
            $file_name = $file->getClientOriginalName();

            switch ($offerAction->action->category) {
                case 'veterinary':
                    Storage::delete('public/offers_docs/' . $offerAction->offer->full_number . '/veterinary_docs/' . $offerAction->action_document);
                    $path = Storage::putFileAs('public/offers_docs/' . $offerAction->offer->full_number . '/veterinary_docs/', $file, $file_name);
                    break;
                case 'crate':
                    Storage::delete('public/offers_docs/' . $offerAction->offer->full_number . '/crates/' . $offerAction->action_document);
                    $path = Storage::putFileAs('public/offers_docs/' . $offerAction->offer->full_number . '/crates/', $file, $file_name);
                    break;
                case 'transport':
                    Storage::delete('public/offers_docs/' . $offerAction->offer->full_number . '/airfreight/' . $offerAction->action_document);
                    $path = Storage::putFileAs('public/offers_docs/' . $offerAction->offer->full_number . '/airfreight/', $file, $file_name);
                    break;
                default:
                    Storage::delete('public/offers_docs/' . $offerAction->offer->full_number . '/' . $offerAction->action_document);
                    $path = Storage::putFileAs('public/offers_docs/' . $offerAction->offer->full_number, $file, $file_name);
                    break;
            }

            $offerAction->update(['action_document' => $file_name]);
        }

        return redirect()->back();
    }

    /**
     * Delete offer file.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_file($offer_id, $file_name, $folder = null)
    {
        $offer = Offer::findOrFail($offer_id);

        $parentFolderName = $offer->full_number;

        if ($folder != null) {
            Storage::delete('public/offers_docs/' . $parentFolderName . '/' . $folder . '/' . $file_name);
        } else {
            Storage::delete('public/offers_docs/' . $parentFolderName . '/' . $file_name);
        }

        $offerAction = $offer->offer_actions()->where('action_document', $file_name)->first();
        if ($offerAction != null) {
            $offerAction->update(['action_document' => null]);
        }

        return redirect(route('offers.show', $offer_id));
    }

    /**
     * Filter offers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterOffers(Request $request)
    {
        // Set session offer filter
        session(['offer.filter' => $request->query()]);

        return redirect(route('offers.index'));
    }

    /**
     * Filter emails of 1 offer to be able to order by date or containing attachments
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterEmailsOffer(Request $request)
    {
        // Set session offer filter
        session(['offer.filterShow' => $request->query()]);

        return redirect(route('offers.show', $request->id));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('offer.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['offer.filter' => $query]);

        return redirect(route('offers.index'));
    }

    /**
     * Quick offer status selection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function offersWithStatus(Request $request)
    {
        $query                = session('offer.filter');
        $query['statusField'] = $request->statusField;
        session(['offer.filter' => $query]);

        return redirect(route('offers.index'));
    }

    /**
     * Quick offer status selection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function offersWithStatusLevel(Request $request)
    {
        $query                     = session('offer.filter');
        $query['statusLevelField'] = $request->statusLevelField;
        session(['offer.filter' => $query]);

        return redirect(route('offers.index'));
    }

    /**
     * Save offer selected tab in session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectedOfferTab(Request $request)
    {
        $query             = session('offer.filter');
        $query['offerTab'] = $request->offerTab;
        session(['offer.filter' => $query]);

        return response()->json();
    }

    /**
     * Remove from offer session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromOfferSession($key)
    {
        $query = session('offer.filter');
        Arr::forget($query, $key);
        session(['offer.filter' => $query]);

        return redirect(route('offers.index'));
    }

    /**
     * Create offer pdf.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function create_offer_pdf($id, $x_quantity = false, $parent_view = 'details')
    {
        $offer = Offer::where('id', $id)->first();

        $number_email = '#OF-' . $offer->full_number;

        $speciesWithoutPrice = $offer->offer_species()->with(['oursurplus'])
            ->where([
                ['offerSalePriceM', '=', '0'],
                ['offerSalePriceF', '=', '0'],
                ['offerSalePriceU', '=', '0'],
                ['offerSalePriceP', '=', '0'],
            ])
            ->get();

        if ($speciesWithoutPrice->count() > 0) {
            $surpluses_info = collect();

            foreach ($speciesWithoutPrice as $species) {
                $relatedSurpluses = OurSurplus::with(['animal'])
                    ->where('animal_id', $species->oursurplus->animal_id)
                    ->get();

                $surpluses_info->put($species->oursurplus->animal->common_name . '(' . $species->oursurplus->animal->scientific_name . ')', $species->oursurplus);
            }

            $email_body = view('emails.offer-species-without-sales-prices', compact('offer', 'surpluses_info'))->render();

            Mail::to('johnrens@zoo-services.com')->send(new SendGeneralEmail('info@zoo-services.com', 'Trying to generate an offer with species without sales prices.' . $number_email, $email_body));

            return redirect()->back()->with('error_msg', 'There are species without sales prices. You cannot generate an offer.');
        } else {
            $document_info = $this->offerService->create_offer_pdf($id, $x_quantity);

            $is_calculation = false;

            return view('offers.document_preview', compact(
                'offer',
                'document_info',
                'is_calculation',
                'x_quantity',
                'parent_view'
            ));
        }
    }

    /**
     * Create calculation details pdf.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function create_offer_calculation_pdf($id, $parent_view = 'offers_main')
    {
        $offer = $this->offerService->calculate_offer_totals($id);

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $dateOfToday = Carbon::now()->format('F j, Y');
        //dd($offer->species_ordered[0]->species_airfreights[0]->airfreight->from_continent->name);
        $document_info = view('pdf_documents.offer_calculation_pdf', compact('offer', 'dateOfToday', 'general_rate_usd', 'general_rate_eur', 'parent_view'))->render();

        $is_calculation = true;

        return view('offers.document_preview', compact('offer', 'document_info', 'is_calculation', 'parent_view'));
    }

    /**
     * Export offer or calculation details pdf.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export_offer_or_calculation_pdf(Request $request)
    {
        $offer = Offer::findOrFail($request->offer_id);

        $offer->update(['quantity_x' => ($request->x_quantity) ? true : false]);

        $folderName = $offer->full_number;

        $fileName = ($request->is_calculation) ? 'Calculation details ' . $folderName . '.pdf' : 'Offer ' . $folderName . '.pdf';

        $html = str_replace('http://127.0.0.1:8000', base_path() . '/public', $request->document_info);

        $pdf = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

        Storage::put('public/offers_docs/' . $folderName . '/' . $fileName, $pdf->output());

        if ($offer->status_level == 'Inquiry' && Storage::exists('public/offers_docs/' . $folderName . '/' . 'Offer ' . $folderName . '.pdf')) {
            $offer->update(['status_level' => 'Forapproval']);
        }

        $offer['new_offer_forapproval'] = 1;
        $offer['new_offer_inquiry']     = 0;
        $offer->save();

        //return $pdf->stream($fileName . '.pdf');

        if ($request->parent_view === 'offer_details') {
            return redirect(route('offers.show', $offer->id));
        } elseif ($request->parent_view === 'order_details') {
            return redirect(route('orders.show', $offer->order->id));
        } else {
            return redirect(route('offers.show', $offer->id));
        }
    }

    /**
     * Save offer species values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSpeciesValues(Request $request)
    {
        $species = OfferSpecies::findOrFail($request->idOfferSpecies);
        $species->update([$request->column => $request->value]);
        $origin       = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $regionsNames = Region::pluck('name', 'id');

        $species->currency_rate = ($species->oursurplus->sale_currency != 'USD') ? number_format(CurrencyRate::latest()->value($species->oursurplus->sale_currency . '_USD'), 2, '.', '') : 1;

        $species->total_cost_price_usd = $species->offerQuantityM  * $species->offerCostPriceM  * $species->currency_rate;
        $species->total_cost_price_usd += $species->offerQuantityF * $species->offerCostPriceF * $species->currency_rate;
        $species->total_cost_price_usd += $species->offerQuantityU * $species->offerCostPriceU * $species->currency_rate;
        $species->total_cost_price_usd += $species->offerQuantityP * $species->offerCostPriceP * $species->currency_rate;
        /*if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerCostPriceP > 0)
            $species->total_cost_price_usd += $species->offerQuantityM * $species->offerCostPriceP * $species->currency_rate;*/

        $species->total_sale_price_usd = $species->offerQuantityM  * $species->offerSalePriceM  * $species->currency_rate;
        $species->total_sale_price_usd += $species->offerQuantityF * $species->offerSalePriceF * $species->currency_rate;
        $species->total_sale_price_usd += $species->offerQuantityU * $species->offerSalePriceU * $species->currency_rate;
        $species->total_sale_price_usd += $species->offerQuantityP * $species->offerSalePriceP * $species->currency_rate;
        /*if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerSalePriceP > 0)
            $species->total_sale_price_usd += $species->offerQuantityM * $species->offerSalePriceP * $species->currency_rate;*/

        $speciesCrate = OfferSpeciesCrate::where('offer_species_id', $species->id)->first();
        switch ($request->column) {
            case 'offerQuantityM':
                $speciesCrate->update(['quantity_males' => $request->value]);
                break;
            case 'offerQuantityF':
                $speciesCrate->update(['quantity_females' => $request->value]);
                break;
            case 'offerQuantityU':
                $speciesCrate->update(['quantity_unsexed' => $request->value]);
                break;
            case 'offerQuantityP':
                $speciesCrate->update(['quantity_pairs' => $request->value]);
                break;
        }

        $offer = $this->offerService->calculate_offer_totals($species->offer->id);

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer', 'origin'))->render();
        }

        $html = view('offers.offer_species_table', compact('offer', 'origin', 'regionsNames'))->render();

        $speciesAirfreightsHtml = view('offers.offer_species_airfreights_table', compact('offer', 'origin'))->render();
        $speciesCratesHtml      = view('offers.offer_species_crates_table', compact('offer', 'origin'))->render();

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer', 'origin'))->render();

        return response()->json([
            'success'                => true,
            'html'                   => $html,
            'speciesAirfreightsHtml' => $speciesAirfreightsHtml,
            'speciesCratesHtml'      => $speciesCratesHtml,
            'totalAndProfitHtml'     => $totalAndProfitHtml,
            'totalsOffer'            => $totalsOffer,
            'origin'                 => $origin,
        ]);
    }

    /**
     * Save offer species values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSpeciesValues(Request $request)
    {
        $species      = OfferSpecies::findOrFail($request->idOfferSpecies);
        $origin       = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $regionsNames = Region::pluck('name', 'id');

        $species->currency_rate = ($species->oursurplus->sale_currency != 'USD') ? number_format(CurrencyRate::latest()->value($species->oursurplus->sale_currency . '_USD'), 2, '.', '') : 1;

        $species->total_cost_price_usd = $species->offerQuantityM  * $species->offerCostPriceM  * $species->currency_rate;
        $species->total_cost_price_usd += $species->offerQuantityF * $species->offerCostPriceF * $species->currency_rate;
        $species->total_cost_price_usd += $species->offerQuantityU * $species->offerCostPriceU * $species->currency_rate;
        if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerCostPriceP > 0) {
            $species->total_cost_price_usd += $species->offerQuantityM * $species->offerCostPriceP * $species->currency_rate;
        }

        $species->total_sale_price_usd = $species->offerQuantityM  * $species->offerSalePriceM  * $species->currency_rate;
        $species->total_sale_price_usd += $species->offerQuantityF * $species->offerSalePriceF * $species->currency_rate;
        $species->total_sale_price_usd += $species->offerQuantityU * $species->offerSalePriceU * $species->currency_rate;
        if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerSalePriceP > 0) {
            $species->total_sale_price_usd += $species->offerQuantityM * $species->offerSalePriceP * $species->currency_rate;
        }

        $offer = $this->offerService->calculate_offer_totals($species->offer->id);

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer', 'origin'))->render();
        }

        $html = view('offers.offer_species_table', compact('offer', 'origin', 'regionsNames'))->render();

        $speciesAirfreightsHtml = view('offers.offer_species_airfreights_table', compact('offer', 'origin'))->render();
        $speciesCratesHtml      = view('offers.offer_species_crates_table', compact('offer', 'origin'))->render();

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer', 'origin'))->render();

        return response()->json([
            'success'                => true,
            'html'                   => $html,
            'speciesAirfreightsHtml' => $speciesAirfreightsHtml,
            'speciesCratesHtml'      => $speciesCratesHtml,
            'totalAndProfitHtml'     => $totalAndProfitHtml,
            'totalsOffer'            => $totalsOffer,
            'origin'                 => $origin,
        ]);
    }

    /**
     * Update selected offer species crate by crate selected.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOfferSpeciesCrateByCrateSelected(Request $request)
    {
        $crate = Crate::where('id', $request->idCrate)->first();

        $speciesCrate = OfferSpeciesCrate::findOrFail($request->idOfferSpeciesCrate);

        if ($crate != null) {
            $speciesCrate->update(['crate_id' => $crate->id, 'length' => $crate->length, 'wide' => $crate->wide, 'height' => $crate->height, 'cost_price' => $crate->cost_price, 'sale_price' => $crate->sale_price]);
        } else {
            $speciesCrate->update(['crate_id' => null, 'length' => 0, 'wide' => 0, 'height' => 0, 'cost_price' => 0, 'sale_price' => 0]);
        }

        $species                = OfferSpecies::findOrFail($speciesCrate->offer_species_id);
        $species->currency_rate = ($species->oursurplus->sale_currency != 'USD') ? number_format(CurrencyRate::latest()->value($species->oursurplus->sale_currency . '_USD'), 2, '.', '') : 1;

        $species->species_crate->m_volKg = number_format(($species->species_crate->quantity_males   * $species->species_crate->length   * $species->species_crate->wide   * $species->species_crate->height) / 6000, 2, '.', '');
        $species->species_crate->f_volKg = number_format(($species->species_crate->quantity_females * $species->species_crate->length * $species->species_crate->wide * $species->species_crate->height)     / 6000, 2, '.', '');
        $species->species_crate->u_volKg = number_format(($species->species_crate->quantity_unsexed * $species->species_crate->length * $species->species_crate->wide * $species->species_crate->height)     / 6000, 2, '.', '');
        $species->species_crate->p_volKg = number_format(($species->species_crate->quantity_pairs   * $species->species_crate->length   * $species->species_crate->wide   * $species->species_crate->height) / 6000, 2, '.', '');

        $species->total_volKg = $species->species_crate->m_volKg + $species->species_crate->f_volKg + $species->species_crate->u_volKg + $species->species_crate->p_volKg;

        $species->species_crate->total_cost_price_usd = $species->species_crate->quantity_males    * $species->species_crate->cost_price    * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_females * $species->species_crate->cost_price * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_unsexed * $species->species_crate->cost_price * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_pairs   * $species->species_crate->cost_price   * $species->currency_rate;

        $species->species_crate->total_sale_price_usd = $species->species_crate->quantity_males    * $species->species_crate->sale_price    * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_females * $species->species_crate->sale_price * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_unsexed * $species->species_crate->sale_price * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_pairs   * $species->species_crate->sale_price   * $species->currency_rate;

        $offer = $this->offerService->calculate_offer_totals($species->offer->id);

        $html = view('offers.offer_species_crates_table', compact('offer'))->render();

        $speciesAirfreightsHtml = view('offers.offer_species_airfreights_table', compact('offer'))->render();

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $offerSpeciesPallets = view('offers.offer_airfreight_pallet_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'                => true,
            'html'                   => $html,
            'speciesAirfreightsHtml' => $speciesAirfreightsHtml,
            'offerSpeciesPallets'    => $offerSpeciesPallets,
            'totalAndProfitHtml'     => $totalAndProfitHtml,
            'totalsOffer'            => $totalsOffer,
        ]);
    }

    /**
     * Save offer species crate values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSpeciesCrateValues(Request $request)
    {
        if (!empty($request->idOfferSpeciesCrate)) {
            $speciesCrate = OfferSpeciesCrate::findOrFail($request->idOfferSpeciesCrate);
            $species      = OfferSpecies::findOrFail($speciesCrate->offer_species_id);
            if (count($species->species_crates) > 0) {
                $firstSpeciesCrate = $species->species_crates[0];
                $speciesCrate->update(['cost_price' => $firstSpeciesCrate->cost_price ?? 0, 'sale_price' => round($firstSpeciesCrate->cost_price * 1.12) ?? 0]);
            }
        }
        $speciesCrate = OfferSpeciesCrate::findOrFail($request->idOfferSpeciesCrate);
        if (!empty($request->column)) {
            if ($request->column === 'cost_price' && $request->value != $speciesCrate->cost_price) {
                $speciesCrate->update(['sale_price' => round($request->value * 1.12)]);
            }
            $speciesCrate->update([$request->column => $request->value]);
        }

        $species                = OfferSpecies::findOrFail($speciesCrate->offer_species_id);
        $species->currency_rate = ($species->oursurplus->sale_currency != 'USD') ? number_format(CurrencyRate::latest()->value($species->oursurplus->sale_currency . '_USD'), 2, '.', '') : 1;

        $species->species_crate->m_volKg = number_format(($species->species_crate->quantity_males   * $species->species_crate->length   * $species->species_crate->wide   * $species->species_crate->height) / 6000, 2, '.', '');
        $species->species_crate->f_volKg = number_format(($species->species_crate->quantity_females * $species->species_crate->length * $species->species_crate->wide * $species->species_crate->height)     / 6000, 2, '.', '');
        $species->species_crate->u_volKg = number_format(($species->species_crate->quantity_unsexed * $species->species_crate->length * $species->species_crate->wide * $species->species_crate->height)     / 6000, 2, '.', '');
        $species->species_crate->p_volKg = number_format(($species->species_crate->quantity_pairs   * $species->species_crate->length   * $species->species_crate->wide   * $species->species_crate->height) / 6000, 2, '.', '');

        $species->total_volKg = $species->species_crate->m_volKg + $species->species_crate->f_volKg + $species->species_crate->u_volKg + $species->species_crate->p_volKg;

        $species->species_crate->total_cost_price_usd = $species->species_crate->quantity_males    * $species->species_crate->cost_price    * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_females * $species->species_crate->cost_price * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_unsexed * $species->species_crate->cost_price * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_pairs   * $species->species_crate->cost_price   * $species->currency_rate;

        $species->species_crate->total_sale_price_usd = $species->species_crate->quantity_males    * $species->species_crate->sale_price    * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_females * $species->species_crate->sale_price * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_unsexed * $species->species_crate->sale_price * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_pairs   * $species->species_crate->sale_price   * $species->currency_rate;

        foreach ($species->species_airfreights as $species_airfreight) {
            $species->total_cost_volKg_value += $species_airfreight->cost_volKg;
            $species->total_sale_volKg_value += $species_airfreight->sale_volKg;

            $species->total_airfreight_cost_price += $species->total_volKg * $species_airfreight->cost_volKg * $species->currency_rate;

            $species->total_airfreight_sale_price += $species->total_volKg * $species_airfreight->sale_volKg * $species->currency_rate;
        }

        $offer = $this->offerService->calculate_offer_totals($species->offer->id);

        $html = view('offers.offer_species_crates_table', compact('offer'))->render();

        $speciesAirfreightsHtml = view('offers.offer_species_airfreights_table', compact('offer'))->render();

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $offerSpeciesPallets = view('offers.offer_airfreight_pallet_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'                => true,
            'speciesId'              => $species->id,
            'html'                   => $html,
            'speciesAirfreightsHtml' => $speciesAirfreightsHtml,
            'offerSpeciesPallets'    => $offerSpeciesPallets,
            'totalAndProfitHtml'     => $totalAndProfitHtml,
            'totalsOffer'            => $totalsOffer,
        ]);
    }

    /**
     * Save offer species crate values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSpeciesCrateValues(Request $request)
    {
        if (!empty($request->idOfferSpeciesCrate)) {
            $speciesCrate = OfferSpeciesCrate::findOrFail($request->idOfferSpeciesCrate);
            $species      = OfferSpecies::findOrFail($speciesCrate->offer_species_id);
        }
        $speciesCrate = OfferSpeciesCrate::findOrFail($request->idOfferSpeciesCrate);

        $species                = OfferSpecies::findOrFail($speciesCrate->offer_species_id);
        $species->currency_rate = ($species->oursurplus->sale_currency != 'USD') ? number_format(CurrencyRate::latest()->value($species->oursurplus->sale_currency . '_USD'), 2, '.', '') : 1;

        $species->species_crate->m_volKg = number_format(($species->species_crate->quantity_males   * $species->species_crate->length   * $species->species_crate->wide   * $species->species_crate->height) / 6000, 2, '.', '');
        $species->species_crate->f_volKg = number_format(($species->species_crate->quantity_females * $species->species_crate->length * $species->species_crate->wide * $species->species_crate->height)     / 6000, 2, '.', '');
        $species->species_crate->u_volKg = number_format(($species->species_crate->quantity_unsexed * $species->species_crate->length * $species->species_crate->wide * $species->species_crate->height)     / 6000, 2, '.', '');
        $species->species_crate->p_volKg = number_format(($species->species_crate->quantity_pairs   * $species->species_crate->length   * $species->species_crate->wide   * $species->species_crate->height) / 6000, 2, '.', '');

        $species->total_volKg = $species->species_crate->m_volKg + $species->species_crate->f_volKg + $species->species_crate->u_volKg + $species->species_crate->p_volKg;

        $species->species_crate->total_cost_price_usd = $species->species_crate->quantity_males    * $species->species_crate->cost_price    * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_females * $species->species_crate->cost_price * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_unsexed * $species->species_crate->cost_price * $species->currency_rate;
        $species->species_crate->total_cost_price_usd += $species->species_crate->quantity_pairs   * $species->species_crate->cost_price   * $species->currency_rate;

        $species->species_crate->total_sale_price_usd = $species->species_crate->quantity_males    * $species->species_crate->sale_price    * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_females * $species->species_crate->sale_price * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_unsexed * $species->species_crate->sale_price * $species->currency_rate;
        $species->species_crate->total_sale_price_usd += $species->species_crate->quantity_pairs   * $species->species_crate->sale_price   * $species->currency_rate;

        foreach ($species->species_airfreights as $species_airfreight) {
            $species->total_cost_volKg_value += $species_airfreight->cost_volKg;
            $species->total_sale_volKg_value += $species_airfreight->sale_volKg;

            $species->total_airfreight_cost_price += $species->total_volKg * $species_airfreight->cost_volKg * $species->currency_rate;

            $species->total_airfreight_sale_price += $species->total_volKg * $species_airfreight->sale_volKg * $species->currency_rate;
        }

        $offer = $this->offerService->calculate_offer_totals($species->offer->id);

        $html = view('offers.offer_species_crates_table', compact('offer'))->render();

        $speciesAirfreightsHtml = view('offers.offer_species_airfreights_table', compact('offer'))->render();

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $offerSpeciesPallets = view('offers.offer_airfreight_pallet_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'                => true,
            'speciesId'              => $species->id,
            'html'                   => $html,
            'speciesAirfreightsHtml' => $speciesAirfreightsHtml,
            'offerSpeciesPallets'    => $offerSpeciesPallets,
            'totalAndProfitHtml'     => $totalAndProfitHtml,
            'totalsOffer'            => $totalsOffer,
        ]);
    }

    /**
     * Get offer species airfreights.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getOfferSpeciesAirfreights(Request $request)
    {
        $airfreights = [];

        if ($request->has('offerSpeciesId')) {
            $offerSpeciesAirfreights = OfferSpeciesAirfreight::where('offer_species_id', $request->offerSpeciesId)->get();

            foreach ($offerSpeciesAirfreights as $offerSpeciesAirfreight) {
                if ($offerSpeciesAirfreight->airfreight != null) {
                    $airfreight                           = $offerSpeciesAirfreight->airfreight;
                    $airfreight->offerSpeciesAirfreightId = $offerSpeciesAirfreight->id;
                    $airfreight->departureContinent       = $airfreight->from_continent->name;
                    $airfreight->arrivalContinent         = $airfreight->to_continent->name;
                    array_push($airfreights, $airfreight);
                }
            }
        }

        return response()->json(['success' => true, 'airfreights' => $airfreights]);
    }

    /**
     * Save selected airfreights related with the offer species.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveOfferSpeciesAirfreights(Request $request)
    {
        if (count($request->items) > 0) {
            $offerSpecies = OfferSpecies::findOrFail($request->offerSpeciesId);

            OfferSpeciesAirfreight::where('offer_species_id', $offerSpecies->id)->whereNull('airfreight_id')->delete();

            foreach ($request->items as $airfreightId) {
                $airfreight = Airfreight::findOrFail($airfreightId);

                if ($offerSpecies->species_airfreights()->where('airfreight_id', $airfreightId)->first() == null) {
                    $newOfferSpeciesAirfreight                   = new OfferSpeciesAirfreight();
                    $newOfferSpeciesAirfreight->offer_species_id = $request->offerSpeciesId;
                    $newOfferSpeciesAirfreight->airfreight_id    = $airfreightId;
                    $newOfferSpeciesAirfreight->cost_volKg       = $airfreight->volKg_weight_cost;
                    $newOfferSpeciesAirfreight->sale_volKg       = $airfreight->volKg_weight_cost * 1.12;
                    $newOfferSpeciesAirfreight->save();
                }
            }

            $offerSpecies->currency_rate = ($offerSpecies->oursurplus->sale_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offerSpecies->oursurplus->sale_currency . '_USD'), 2, '.', '') : 1;

            if ($offerSpecies->species_crate != null) {
                $offerSpecies->species_crate->m_volKg = number_format(($offerSpecies->species_crate->quantity_males   * $offerSpecies->species_crate->length   * $offerSpecies->species_crate->wide   * $offerSpecies->species_crate->height) / 6000, 2, '.', '');
                $offerSpecies->species_crate->f_volKg = number_format(($offerSpecies->species_crate->quantity_females * $offerSpecies->species_crate->length * $offerSpecies->species_crate->wide * $offerSpecies->species_crate->height)     / 6000, 2, '.', '');
                $offerSpecies->species_crate->u_volKg = number_format(($offerSpecies->species_crate->quantity_unsexed * $offerSpecies->species_crate->length * $offerSpecies->species_crate->wide * $offerSpecies->species_crate->height)     / 6000, 2, '.', '');
                $offerSpecies->species_crate->p_volKg = number_format(($offerSpecies->species_crate->quantity_pairs   * $offerSpecies->species_crate->length   * $offerSpecies->species_crate->wide   * $offerSpecies->species_crate->height) / 6000, 2, '.', '');

                $offerSpecies->total_volKg = $offerSpecies->species_crate->m_volKg + $offerSpecies->species_crate->f_volKg + $offerSpecies->species_crate->u_volKg + $offerSpecies->species_crate->p_volKg;
            }

            foreach ($offerSpecies->species_airfreights as $species_airfreight) {
                $offerSpecies->total_cost_volKg_value += $species_airfreight->cost_volKg;
                $offerSpecies->total_sale_volKg_value += $species_airfreight->sale_volKg;

                $offerSpecies->total_airfreight_cost_price += $offerSpecies->total_volKg * $species_airfreight->cost_volKg * $offerSpecies->currency_rate;

                $offerSpecies->total_airfreight_sale_price += $offerSpecies->total_volKg * $species_airfreight->sale_volKg * $offerSpecies->currency_rate;
            }

            $offer = $this->offerService->calculate_offer_totals($offerSpecies->offer->id);

            $html = view('offers.offer_species_airfreights_table', compact('offer'))->render();

            $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

            return response()->json(['success' => true, 'html' => $html, 'totalAndProfitHtml' => $totalAndProfitHtml]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Remove airfreight related with the offer species.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeOfferSpeciesAirfreight(Request $request)
    {
        $offerSpeciesAirfreight = OfferSpeciesAirfreight::findOrFail($request->offerSpeciesAirfreightId);
        $offerSpecies           = OfferSpecies::findOrFail($offerSpeciesAirfreight->offer_species_id);
        $offerSpeciesAirfreight->delete();

        $offer = $this->offerService->calculate_offer_totals($offerSpecies->offer->id);

        $html = view('offers.offer_species_airfreights_table', compact('offer'))->render();

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        return response()->json(['success' => true, 'html' => $html, 'totalAndProfitHtml' => $totalAndProfitHtml]);
    }

    /**
     * Save offer airfreight type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveOfferAirfreightType(Request $request)
    {
        $offer = Offer::findOrFail($request->idOffer);
        $offer->update(['airfreight_type' => $request->value]);

        return response()->json(['success' => true]);
    }

    /**
     * Set cost and sales values to selected species airfreights.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSelectedSpeciesAirfreightsValues(Request $request)
    {
        if (count($request->items) > 0 && ($request->costValue > 0 || $request->salesValue > 0)) {
            foreach ($request->items as $id) {
                $offerSpeciesAirfreights = OfferSpeciesAirfreight::where('offer_species_id', $id)->get();

                if (count($offerSpeciesAirfreights) > 0) {
                    foreach ($offerSpeciesAirfreights as $offerSpeciesAirfreight) {
                        if ($request->costValue > 0) {
                            $offerSpeciesAirfreight->update(['cost_volKg' => $request->costValue]);
                        }
                        if ($request->salesValue > 0) {
                            $offerSpeciesAirfreight->update(['sale_volKg' => $request->salesValue]);
                        }
                    }
                } else {
                    $newOfferSpeciesAirfreight                   = new OfferSpeciesAirfreight();
                    $newOfferSpeciesAirfreight->offer_species_id = $id;
                    $newOfferSpeciesAirfreight->cost_volKg       = $request->costValue;
                    $newOfferSpeciesAirfreight->sale_volKg       = $request->salesValue;
                    $newOfferSpeciesAirfreight->save();
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Save offer airfreight vol.kg rate values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSpeciesAirfreightVolKgRateValues(Request $request)
    {
        $speciesAirfreight = OfferSpeciesAirfreight::findOrFail($request->idOfferSpeciesAirfreight);
        if ($request->column === 'cost_volKg' && $request->value != $speciesAirfreight->cost_volKg) {
            $speciesAirfreight->update(['sale_volKg' => round($request->value * 1.12)]);
        }
        $speciesAirfreight->update([$request->column => $request->value]);

        $species                = OfferSpecies::findOrFail($speciesAirfreight->offer_species_id);
        $species->currency_rate = ($species->oursurplus->sale_currency != 'USD') ? number_format(CurrencyRate::latest()->value($species->oursurplus->sale_currency . '_USD'), 2, '.', '') : 1;

        foreach ($species->species_airfreights as $species_airfreight) {
            $species->total_cost_volKg_value += $species_airfreight->cost_volKg;
            $species->total_sale_volKg_value += $species_airfreight->sale_volKg;

            $species->total_airfreight_cost_price += $species->total_volKg * $species_airfreight->cost_volKg * $species->currency_rate;

            $species->total_airfreight_sale_price += $species->total_volKg * $species_airfreight->sale_volKg * $species->currency_rate;
        }

        $offer = $this->offerService->calculate_offer_totals($species->offer->id);

        $html = view('offers.offer_species_airfreights_table', compact('offer'))->render();

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'            => true,
            'html'               => $html,
            'totalAndProfitHtml' => $totalAndProfitHtml,
            'totalsOffer'        => $totalsOffer,
        ]);
    }

    /**
     * Save selected airfreight pallet related with the offer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveOfferAirfreightPallet(Request $request)
    {
        $offer = Offer::findOrFail($request->offerId);

        $airfreight = Airfreight::findOrFail($request->selectedPallet);

        if ($offer->airfreight_pallet) {
            $offer->airfreight_pallet->airfreight_id       = $airfreight->id;
            $offer->airfreight_pallet->departure_continent = $airfreight->departure_continent;
            $offer->airfreight_pallet->arrival_continent   = $airfreight->arrival_continent;
            $offer->airfreight_pallet->pallet_cost_value   = ($airfreight->type == 'lowerdeck') ? $airfreight->lowerdeck_cost : $airfreight->maindeck_cost;
            $offer->airfreight_pallet->pallet_sale_value   = $offer->airfreight_pallet->pallet_cost_value * 1.12;
            $offer->airfreight_pallet->update();
        } else {
            $newOfferAirfreightPallet                      = new OfferAirfreightPallet();
            $newOfferAirfreightPallet->offer_id            = $offer->id;
            $newOfferAirfreightPallet->airfreight_id       = $airfreight->id;
            $newOfferAirfreightPallet->pallet_quantity     = 1;
            $newOfferAirfreightPallet->departure_continent = $request->departure_continent;
            $newOfferAirfreightPallet->arrival_continent   = $request->arrival_continent;
            $newOfferAirfreightPallet->pallet_cost_value   = ($airfreight->type == 'lowerdeck') ? $airfreight->lowerdeck_cost : $airfreight->maindeck_cost;
            $newOfferAirfreightPallet->pallet_sale_value   = $newOfferAirfreightPallet->pallet_cost_value * 1.12;
            $newOfferAirfreightPallet->save();
        }

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $offer              = $this->offerService->calculate_offer_totals($offer->id);
        $html               = view('offers.offer_airfreight_pallet_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();
        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        return response()->json(['success' => true, 'html' => $html, 'totalAndProfitHtml' => $totalAndProfitHtml]);
    }

    /**
     * Save offer airfreight pallet values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveOfferAirfreightPalletValues(Request $request)
    {
        $offerAirfreightPallet = OfferAirfreightPallet::findOrFail($request->idOfferAirfreightPallet);
        $offerAirfreightPallet->update([$request->column => $request->value]);

        $offer = Offer::findOrFail($offerAirfreightPallet->offer_id);

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $offer->offerTotalAirfreightPalletCostPrice = $offer->airfreight_pallet->pallet_quantity * $offer->airfreight_pallet->pallet_cost_value * $general_rate_usd;
        $offer->offerTotalAirfreightPalletSalePrice = $offer->airfreight_pallet->pallet_quantity * $offer->airfreight_pallet->pallet_sale_value * $general_rate_usd;

        $html = view('offers.offer_airfreight_pallet_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();

        $offer = $this->offerService->calculate_offer_totals($offer->id);

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'            => true,
            'html'               => $html,
            'totalAndProfitHtml' => $totalAndProfitHtml,
            'totalsOffer'        => $totalsOffer,
        ]);
    }

    /**
     * Save offer transport truck.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveOfferTransportTruck(Request $request)
    {
        $offer = Offer::findOrFail($request->offerId);

        if ($offer->transport_truck) {
            $offer->transport_truck->from_country = $request->from_country;
            $offer->transport_truck->to_country   = $request->to_country;
            $offer->transport_truck->update();
        } else {
            $newOfferTransportTruck               = new OfferTransportTruck();
            $newOfferTransportTruck->offer_id     = $offer->id;
            $newOfferTransportTruck->from_country = $request->from_country;
            $newOfferTransportTruck->to_country   = $request->to_country;
            $newOfferTransportTruck->save();
        }

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $offer              = $this->offerService->calculate_offer_totals($offer->id);
        $html               = view('offers.offer_transport_truck_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();
        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        return response()->json(['success' => true, 'html' => $html, 'totalAndProfitHtml' => $totalAndProfitHtml]);
    }

    /**
     * Save offer tranport truck values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveOfferTransportTruckValues(Request $request)
    {
        $offerTransportTruck = OfferTransportTruck::findOrFail($request->idOfferTransportTruck);
        $offerTransportTruck->update([$request->column => $request->value]);

        $offer = Offer::findOrFail($offerTransportTruck->offer_id);

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        $offer->offerTotalTransportTruckCostPrice = $offerTransportTruck->total_km * $offerTransportTruck->cost_rate_per_km * $general_rate_usd;
        $offer->offerTotalTransportTruckSalePrice = $offerTransportTruck->total_km * $offerTransportTruck->sale_rate_per_km * $general_rate_usd;

        $html = view('offers.offer_transport_truck_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();

        $offer = $this->offerService->calculate_offer_totals($offer->id);

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'            => true,
            'html'               => $html,
            'totalAndProfitHtml' => $totalAndProfitHtml,
            'totalsOffer'        => $totalsOffer,
        ]);
    }

    /**
     * Add basic additional cost.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addAdditionalCost(Request $request)
    {
        $offer = Offer::findOrFail($request->offerId);

        $newOfferAdditionalCost            = new OfferAdditionalCost();
        $newOfferAdditionalCost->offer_id  = $offer->id;
        $newOfferAdditionalCost->name      = $request->name;
        $newOfferAdditionalCost->quantity  = 1;
        $newOfferAdditionalCost->currency  = $offer->offer_currency;
        $newOfferAdditionalCost->costPrice = 0;
        $newOfferAdditionalCost->salePrice = 0;
        $newOfferAdditionalCost->is_test   = $request->is_test;
        $newOfferAdditionalCost->save();

        return response()->json(['success' => true]);
    }

    /**
     * Delete test additional cost.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAdditionalCost(Offer $offer, int $id)
    {
        $offerAdditionalCost = OfferAdditionalCost::findOrFail($id);
        $offerAdditionalCost->delete();

        return redirect()->back();
    }

    /**
     * Delete test additional cost.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCost(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $offerAdditionalCost = OfferAdditionalCost::findOrFail($id);
                $offerAdditionalCost->delete();
            }
        }

        return redirect()->back();
    }

    /**
     * Save offer additional cost values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAdditionalCostsValues(Request $request)
    {
        $additionalCost = OfferAdditionalCost::findOrFail($request->idAdditionalCost);
        $additionalCost->update([$request->column => $request->value]);
        if(!empty($request->column) && $request->column === 'costPrice'){
            $salePrice = $request->value * 1.12;
            $additionalCost->update(["salePrice" => $salePrice]);
        }

        $offer = $this->offerService->calculate_offer_totals($request->idOffer);

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;

        $general_rate_eur = ($offer->offer_currency != 'EUR') ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;

        if ($request->isTest == '1') {
            $html = view('offers.additional_tests_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();
        } else {
            $html = view('offers.additional_costs_table', compact('offer', 'general_rate_usd', 'general_rate_eur'))->render();
        }

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'            => true,
            'html'               => $html,
            'totalAndProfitHtml' => $totalAndProfitHtml,
            'totalsOffer'        => $totalsOffer,
        ]);
    }

    /**
     * Set extra fee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setExtraFee(Request $request)
    {
        $offer = Offer::findOrFail($request->idOffer);
        $offer->update([$request->column => $request->value]);

        $offer = $this->offerService->calculate_offer_totals($request->idOffer);

        $totalAndProfitHtml = view('offers.total_and_profit_table', compact('offer'))->render();

        $totalsOffer = '';
        if ($offer->order) {
            $totalsOffer = view('offers.totals_offer_table', compact('offer'))->render();
        }

        return response()->json([
            'success'            => true,
            'totalAndProfitHtml' => $totalAndProfitHtml,
            'totalsOffer'        => $totalsOffer,
        ]);
    }

    //Export excel document with offers info.
    public function export(Request $request)
    {
        $file_name = 'Offers list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $offersByYear = Offer::select('*', DB::raw('YEAR(created_at) as year, MONTH(created_at) as month'))->whereIn('id', explode(',', $request->items))->get()->groupBy(['year', 'month']);

        $export = new OffersExport($offersByYear);

        return Excel::download($export, $file_name);
    }

    /**
     * Email option.
     *
     * @param  int id
     * @param  string email_code
     * @return \Illuminate\Http\Response
     */
    public function sendEmailOption($id, $email_code, $parent_view = 'details', $is_action = false, Request $request = null)
    {
        if (!$is_action) {
            $offer = Offer::findOrFail($id);
        } else {
            $offerAction = OfferAction::where('id', $id)->first();
            $offer       = $offerAction->offer;
        }

        $speciesWithoutPrice = $offer->offer_species()
            ->where([
                ['offerSalePriceM', '=', '0'],
                ['offerSalePriceF', '=', '0'],
                ['offerSalePriceU', '=', '0'],
                ['offerSalePriceP', '=', '0'],
            ])
            ->get();

        if ($email_code === 'send_offer' || $email_code === 'remind_1') {
            if ($speciesWithoutPrice->count() > 0) {
                return redirect()->back()->with('error', 'There are species without sales prices. You cannot send the offer.');
            }
        }

        $offer = $this->offerService->calculate_offer_totals($offer->id);

        $email_from = 'info@zoo-services.com';
        if (!empty($offer->client)) {
            if(!empty($offer->client->email)){
                $email_to = $offer->client->email;
            }else{
                $email_to = $offer->client->organisation->email;
            }
        } else {
            $email_to = $offer->organisation->email ?? "";
        }
        $email_attachments = [];

        if ($offer->sale_price_type === 'CF' || $offer->sale_price_type === 'CIF') {
            $offer->sale_remark = 'Including crates and transport till your airport';
        } else {
            $offer->sale_remark = 'Not including transport';
        }

        $rates = CurrencyRate::orderBy('date', 'desc')->take(10)->get();
        $number_email = "#OF-" . $offer->full_number;
        $active_subject = false;

        switch ($email_code) {
            case 'send_offer':
                $in_surplus = [];

                if (!empty($offer->client)) {
                    $client           = $offer->client;
                    $client_surpluses = Surplus::where('contact_id', $client->id)->pluck('animal_id');
                } else {
                    if (!empty($offer->organisation)) {
                        $client           = $offer;
                        $client_surpluses = Surplus::where('organisation_id', $client->id)->pluck('animal_id');
                    } else {
                        $client = [];
                    }
                }
                if (!empty($client)) {
                    if (empty($client->email) && empty($email_to)) {
                        return redirect()->back()->with('error', 'Offer ' . $offer->full_number . ' has client does not have an email');
                    } else {
                        $organizations_surpluses = [];
                        if ($client->organisation != null) {
                            $organizations_surpluses = Surplus::where('organisation_id', $client->organisation->id)->pluck('animal_id');
                            $organization_country    = Country::findOrFail($client->organisation->country_id);
                        }

                        if (count($client_surpluses) > 0) {
                            foreach ($offer->species_ordered as $species) {
                                if ($client_surpluses->contains($species->oursurplus->animal->id)) {
                                    array_push($in_surplus, $species->oursurplus->animal->common_name);
                                }
                            }
                        }

                        if (count($organizations_surpluses) > 0) {
                            foreach ($offer->species_ordered as $species) {
                                if ($organizations_surpluses->contains($species->oursurplus->animal->id)) {
                                    array_push($in_surplus, $species->oursurplus->animal->common_name);
                                }
                            }
                        }
                    }
                }
                $email_subject = 'Hereby the offer ' . $number_email;
                $email_body = view('emails.send-offer', compact('offer', 'in_surplus', 'rates', 'organization_country'))->render();
                if (Storage::exists('public/offers_docs/' . $offer->full_number . '/' . 'Offer ' . $offer->full_number . '.pdf')) {
                    $email_attachments = ['Offer ' . $offer->full_number . '.pdf'];
                }
                break;
            case 'remind_1':
                $email_subject = 'Offer reminder' . $number_email ;
                $email_body = view('emails.send-offer-reminder1', compact('offer', 'rates'))->render();
                if (Storage::exists('public/offers_docs/' . $offer->full_number . '/' . 'Offer ' . $offer->full_number . '.pdf')) {
                    $email_attachments = ['Offer ' . $offer->full_number . '.pdf'];
                }
                break;
            case 'remind_2':
                $email_subject = 'Offer reminder ' . $number_email;
                $email_body = view('emails.send-offer-reminder2', compact('offer', 'rates'))->render();
                if (Storage::exists('public/offers_docs/' . $offer->full_number . '/' . 'Offer ' . $offer->full_number . '.pdf')) {
                    $email_attachments = ['Offer ' . $offer->full_number . '.pdf'];
                }
                break;
            case 'not_available':
                $email_subject = 'Species not available anymore. ' . $number_email;
                $email_body = view('emails.send-offer-species-not-available', compact('offer'))->render();
                break;
            case 'special_conditions':
                $email_subject = 'About Harpy eagle and Manatee. ' . $number_email;
                $email_body = view('emails.send-offer-special-conditions', compact('offer'))->render();
                break;
            case 'to_approve':
                $email_subject = 'We will send you an offer ex zoo. ' . $number_email;
                $email_body = view('emails.send-offer-to-approve', compact('offer'))->render();
                break;
            case 'apply_pictures_enclosures':
                $email_subject = 'Request of pictures of enclosures. ' . $number_email;
                $email_body = view('emails.apply-pictures-enclosures-new', compact('offer'))->render();
                break;
            case 'apply_pictures_species':
                $email_to = $offer->supplier->email;
                $email_subject = 'Request pictures of species. ' . $number_email;
                $email_body = view('emails.apply-pictures-species-new', compact('offer'))->render();
                break;
            case 'apply_veterinary_client':
                $email_subject = 'Veterinary import requirements ' . $number_email;
                $email_body = view('emails.apply-vet-requirements-client-new', compact('offer'))->render();
                break;
            case 'apply_exterior_dimensions_crates':
                $email_to = $offer->supplier->email;
                $email_subject = 'Exterior dimensions crates ' . $number_email;
                $email_body = view('emails.apply-exterior-dimensions-crates-supplier-new', compact('offer'))->render();
                break;
            case 'transport_quotation':
                $email_to = ($offer->airfreight_agent) ? $offer->airfreight_agent->email : $offer->supplier->email;
                $email_subject = 'Inquiry of airfreight quotation. ' . $number_email;
                $total_offer_specimens = 0;
                foreach ($offer->species_ordered as $species) {
                    $total_offer_specimens += ($species->offerQuantityM + $species->offerQuantityF + $species->offerQuantityU);

                    $species_crates           = $species->oursurplus->animal->crates;
                    $species->crateDimensions = '';
                    if ($species_crates->count() > 0) {
                        $species->crateDimensions = $species_crates[0]->length . ' x ' . $species_crates[0]->wide . ' x ' . $species_crates[0]->height . ' cm';
                    }
                }

                $email_body = view('emails.send-offer-freight-application', compact('offer', 'total_offer_specimens'))->render();
                break;
			case 'to_approve_by_john':
			    $general_rate_usd = ($offer->offer_currency != "USD") ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_USD'), 2, '.', '') : 1;
                $general_rate_eur = ($offer->offer_currency != "EUR") ? number_format(CurrencyRate::latest()->value($offer->offer_currency . '_EUR'), 2, '.', '') : 1;
			    $dateOfToday = Carbon::now()->format('F j, Y');
                $email_from = 'info@zoo-services.com';
				$email_to = 'johnrens@zoo-services.com';
                $email_subject = 'OFFER TO APPROVE: ' . $number_email;
				$email_body = view('pdf_documents.offer_calculation_pdf', compact('offer', 'dateOfToday', 'general_rate_usd', 'general_rate_eur', 'parent_view'))->render();
				break;
            case 'to_email_link':
                $email_subject = $number_email;
                if(!empty($request->email_to)){
                    $email_to = $request->email_to;
                }
                $active_subject = true;
                $email_body = "<br><br>" . view('emails.email-signature')->render();
                break;

            default:
                $email_subject = '';
                $email_body    = '';
                break;
        }

        return view('offers.offer_email_to_client', compact('offer', 'email_code', 'email_from', 'email_to', 'email_subject', 'email_body', 'email_attachments', 'parent_view', 'active_subject'));
    }

    /**
     * Email to client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectedOffersAction(Request $request)
    {
        $message    = null;
        $email_from = 'info@zoo-services.com';

        $rates = CurrencyRate::orderBy('date', 'desc')->take(10)->get();

        if (count($request->items) > 0) {
            $message = null;
            foreach ($request->items as $id) {
                $originalOffer = Offer::where('id', $id)->first();
                $offer         = $this->offerService->calculate_offer_totals($originalOffer->id);

                if (!empty($offer->client)) {
                    $client = $offer->client;
                } else {
                    $client = $offer->organisation;
                }
                if ($offer->sale_price_type === 'CF' || $offer->sale_price_type === 'CIF') {
                    $offer->sale_remark = 'Including crates and transport till your airport';
                } else {
                    $offer->sale_remark = 'Not including transport';
                }

                if (!empty($offer->client)) {
                    if(!empty($offer->client->email)){
                        $email_to = $offer->client->email;
                    }else{
                        $email_to = $offer->client->organisation->email;
                    }
                } else {
                    $email_to = $offer->organisation->email ?? "";
                }

                if ($request->code === 'send_all') {
                    $in_surplus = [];
                    if (!empty($client)) {
                        if (empty($email_to)) {
                            $message .= '- Offer ' . $offer->full_number . ' has client does not have an email';
                        } else {
                            $client_surpluses = Surplus::where('contact_id', $client->id)->pluck('animal_id');

                            $organizations_surpluses = [];
                            if ($client->organisation != null) {
                                $organizations_surpluses = Surplus::where('organisation_id', $client->organisation->id)->pluck('animal_id');
                            }

                            if (count($client_surpluses) > 0) {
                                foreach ($offer->species_ordered as $species) {
                                    if ($client_surpluses->contains($species->oursurplus->animal->id)) {
                                        array_push($in_surplus, $species->oursurplus->animal->common_name);
                                    }
                                }
                            }
                            if (count($organizations_surpluses) > 0) {
                                foreach ($offer->species_ordered as $species) {
                                    if ($organizations_surpluses->contains($species->oursurplus->animal->id)) {
                                        array_push($in_surplus, $species->oursurplus->animal->common_name);
                                    }
                                }
                            }
                            if (count($in_surplus) > 0) {
                                $message .= '- Offer ' . $offer->full_number . " has species that the client is also offering in surplus.\n\n";
                            } else {
                                $number_email  = '#OF-' . $offer->full_number;
                                $email_subject = 'Hereby the offer ' . $number_email;
                                $email_body    = view('emails.send-offer', compact('offer', 'in_surplus', 'rates'))->render();

                                try{
                                    $email_create = $this->createSentEmail($email_subject, $email_from, $email_to, $email_body, $id);
                                    $email_options = new SendOfferEmailOptions($originalOffer, $request->code, $email_from, $email_subject, $email_body, $email_create["id"]);
                                    if (App::environment('production')) {
                                        $email_options->sendEmail($email_to, $email_options->build());
                                    }else{
                                        Mail::to($email_to)->send(new SendOfferEmailOptions($originalOffer, $request->code, $email_from, $email_subject, $email_body, $email_create["id"]));
                                    }
                                } catch (\Throwable $th) {
                                    return response()->json(['message' => "Failed to send mail correctly"]);
                                }

                                $this->createTask($offer, $email_create);

                                if ($offer->status_level === 'Forapproval' || $offer->status_level === 'Inquiry'){
                                    $originalOffer->update(['status_level' => 'Sendoffer', 'next_reminder_at' => Carbon::now()->addDays(5), 'times_reminded' => 0]);
                                    $originalOffer['new_offer_forapproval'] = 0;
                                    $originalOffer['new_offer_send']        = 1;
                                    $originalOffer['offer_send_out']        = 1;
                                    $originalOffer['date_send_offer']       = Carbon::now()->format('Y-m-d H:i:s');
                                    $originalOffer->save();
                                }
                            }
                        }
                    } else {
                        $message .= '- Offer ' . $offer->full_number . " does not have an assigned client.\n\n";
                    }
                } elseif ($request->code === 'remind_all' && $offer->times_reminded === 0) {
                    if (empty($email_to)) {
                        $message .= '- Offer ' . $offer->full_number . ' has client does not have an email';
                    } else {
                        $number_email  = '#OF-' . $offer->full_number;
                        $email_subject = 'Offer reminder. ' . $number_email;

                        if ($offer->times_reminded == null || $offer->times_reminded == 0) {
                            $email_body = view('emails.send-offer-reminder1', compact('offer', 'rates'))->render();
                        }

                        try{
                            $email_create = $this->createSentEmail($email_subject, $email_from, $email_to, $email_body, $id);
                            $email_options = new SendOfferEmailOptions($originalOffer, $request->code, $email_from, $email_subject, $email_body, $email_create["id"]);
                            if (App::environment('production')) {
                                $email_options->sendEmail($email_to, $email_options->build());
                            }else{
                                Mail::to($email_to)->send(new SendOfferEmailOptions($originalOffer, $request->code, $email_from, $email_subject, $email_body, $email_create["id"]));
                            }
                        } catch (\Throwable $th) {
                            return response()->json(['message' => "Failed to send mail correctly"]);
                        }

                        $this->createTask($offer, $email_create);

                        $originalOffer->update(['next_reminder_at' => Carbon::parse($offer->next_reminder_at)->addDays(5), 'times_reminded' => ($offer->times_reminded += 1)]);
                        if ($offer->status_level === 'Forapproval') {
                            $originalOffer->update(['status_level' => 'Sendoffer']);
                            $originalOffer['new_offer_forapproval'] = 0;
                            $originalOffer['new_offer_send']        = 1;
                            $originalOffer['offer_send_out']        = 1;
                            $originalOffer['date_send_offer']       = Carbon::now()->format('Y-m-d H:i:s');
                            $originalOffer->save();
                        }
                    }
                }
            }
        }

        return response()->json(['message' => $message]);
    }

    public function createTask($offer, $email = null, $data = null){
        $user = Auth::user();
        $task = new Task();
        $task["description"] = "Remember to ask the customer if they received the offer and if they have any questions";
        $task["action"] = "reminder";
        if(!empty($data) && !empty($data->due_date) && $data->reminder == "yes"){
            $task["due_date"] = $data->due_date;
        }else{
            $task["due_date"] = Carbon::now()->addDays(5);
        }
        $task["user_id"] = $user->id;
        $task["taskable_id"] = $offer->id;
        $task["taskable_type"] = "offer";
        $task["created_by"] = $user->id;
        $task["status"] = "new";
        $task->save();

        if(!empty($email)){
            $email = Email::find($email->id);
            $email["task_id"] = $task->id;
            $email->save();
        }
    }

    /**
     * Send email option.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function offerSendEmail(Request $request)
    {
        $request->validate(['reminder' => 'required']);
        $offer = Offer::findOrFail($request->offer_id);

        $offer['offer_send_out']        = 1;
        $offer['new_offer_send']        = 1;
        $offer['new_offer_forapproval'] = 0;
        $offer->save();

        $body = $request->email_body;
        $link = '(<a href="' . env('APP_URL') . '/offers/' . $offer['id'] . '">Link to system</a>)';
        $body = str_replace('[link_system]', $link, $body);

        $request->email_body = str_replace('[link_system]', '', $request->email_body);

        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }

        try{
            $email_create = $this->createSentEmail($request->email_subject, $request->email_from, $request->email_to, $request->email_body, $request->offer_id, $request->email_cc, $request->email_option, $request->reminder, $request->due_date);
            $email_options = new SendOfferEmailOptions($offer, $request->email_option, $request->email_from, $request->email_subject, $request->email_body, $email_create["id"]);
            if (App::environment('production')) {
                $email_options->sendEmail($request->email_to, $email_options->build(), $request->email_cc);
            }else{
                Mail::to($request->email_to)->cc($email_cc_array)->send(new SendOfferEmailOptions($offer, $request->email_option, $request->email_from, $request->email_subject, $request->email_body, $email_create["id"]));
            }
        } catch (\Throwable $th) {
            if ($request->parent_view === 'main')
                return redirect(route('offers.index'))->with('error', 'Email not successfully sent.');
            else
                return redirect(route('offers.show', $offer))->with('error', 'Email not successfully sent.');
        }

        $this->createTask($offer, $email_create, $request);

        if ($request->email_option === 'send_offer') {
            $offer->update(['status_level' => 'Sendoffer', 'next_reminder_at' => Carbon::now()->addDays(5), 'times_reminded' => 0]);
            $offer['date_send_offer'] = Carbon::now()->format('Y-m-d H:i:s');
            $offer->save();
        } elseif ($request->email_option === 'remind_1') {
            $offer->update(['next_reminder_at' => Carbon::parse($offer->next_reminder_at)->addDays(5), 'times_reminded' => ($offer->times_reminded += 1)]);
        }

        if ($request->parent_view === 'main') {
            return redirect(route('offers.index'))->with('success', 'Email successfully sent.');
        } else {
            return redirect(route('offers.show', $offer))->with('success', 'Email successfully sent.');
        }
    }

    /**
     * Create new offer task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function offerTask(Request $request)
    {
        if (isset($request->task_id)) {
            $task = Task::findOrFail($request->task_id);

            $task->description = $request->description;
            $task->action      = $request->action;
            $task->user_id     = $request->user_id;

            $task->update();
        } else {
            $offer = Offer::findOrFail($request->id);

            $task              = new Task();
            $task->description = $request->description;
            $task->action      = $request->action;
            $task->user_id     = $request->user_id;
            $task->created_by  = Auth::id();

            $offer->tasks()->save($task);
            $offer->refresh();
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
     * Delete offer task.
     *
     * @param  int id
     * @return \Illuminate\Http\Response
     */
    public function deleteOfferTask($task_id)
    {
        $task = Task::findOrFail($task_id);
        $task->delete();

        return redirect()->back();
    }

    /**
     * Quick change status.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function quickChangeStatus($offer_id, $status)
    {
        $offer = Offer::findOrFail($offer_id);

        $offer->update(['offer_status' => $status]);

        if ($offer->offer_status !== 'Pending') {
            $offer->update(['next_reminder_at' => null, 'times_reminded' => 0]);
        } elseif ($offer->offer_status === 'Pending') {
            $offer->update(['next_reminder_at' => Carbon::now()->addDays(5), 'times_reminded' => 0]);
        }

        if ($offer->offer_status == 'Ordered' && $offer->order == null) {
            $orderData = [];

            $year                      = Carbon::now()->format('Y');
            $orderNumber               = Order::whereYear('created_at', $year)->max('order_number');
            $orderData['order_number'] = ($orderNumber) ? $orderNumber + 1 : 1;

            $orderData['offer_id']            = $offer->id;
            $orderData['manager_id']          = auth()->user()->id;
            $orderData['client_id']           = $offer->client->id;
            $orderData['supplier_id']         = ($offer->supplier) ? $offer->supplier->id : null;
            $orderData['airfreight_agent_id'] = ($offer->airfreight_agent) ? $offer->airfreight_agent->id : null;
            $orderData['delivery_country_id'] = $offer->delivery_country->id;
            $orderData['delivery_airport_id'] = $offer->delivery_airport->id;
            $orderData['cost_currency']       = $offer->offer_currency;
            $orderData['sale_currency']       = $offer->offer_currency;
            $orderData['company']             = 'IZS_BV';
            $orderData['bank_account_id']     = ($offer->offer_currency == 'USD') ? 1 : 2;
            $orderData['order_status']        = 'Pending';
            $orderData['order_remarks']       = $offer->remarks;
            $orderData['cost_price_type']     = 'ExZoo';
            $orderData['sale_price_type']     = $offer->sale_price_type;
            $orderData['cost_price_status']   = $offer->cost_price_status;

            $newOrder = Order::create($orderData);

            $orderActions = Action::where(function ($query) {
                $query->where('belongs_to', 'Order')
                    ->orWhere('belongs_to', 'Offer_Order');
            })->get();
            foreach ($orderActions as $orderAction) {
                $newOrderAction             = new OrderAction();
                $newOrderAction->order_id   = $newOrder->id;
                $newOrderAction->action_id  = $orderAction->id;
                $newOrderAction->toBeDoneBy = $orderAction->toBeDoneBy;
                $newOrderAction->save();
            }
        }

        return redirect(route('offers.show', $offer));
    }

    /**
     * Check in the moment to add a surplus to the offer if the client already have it in his offers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAddingSpeciesRules(Request $request)
    {
        $offer = Offer::where('id', $request->offerId)->first();

        $existInOtherClientOffer = false;
        $existInClientSurpluses  = false;

        if (empty($request->ourSurplusId)) {
            return response()->json(['error' => true, 'message' => 'No species selected']);
        }

        if (!empty($offer->client)) {
            $clientOffers = $offer->client->offers;

            foreach ($clientOffers as $clientOffer) {
                $offerSpecies = $clientOffer->offer_species()->where('oursurplus_id', $request->ourSurplusId)->first();

                if ($offerSpecies != null) {
                    $existInOtherClientOffer = true;
                    break;
                }
            }

            $client_surpluses        = Surplus::where('contact_id', $offer->client->id)->pluck('animal_id');
            $organizations_surpluses = [];
            if ($offer->client->organisation != null) {
                $organizations_surpluses = Surplus::where('organisation_id', $offer->client->organisation->id)->pluck('animal_id');
            }

            foreach ($offer->species_ordered as $species) {
                if (count($client_surpluses) > 0 && $client_surpluses->contains($species->oursurplus->animal->id)) {
                    $existInClientSurpluses = true;
                    break;
                }

                if (count($organizations_surpluses) > 0 && $organizations_surpluses->contains($species->oursurplus->animal->id)) {
                    $existInClientSurpluses = true;
                    break;
                }
            }
        }

        if (!empty($offer->organisation)) {
            $organizations_surpluses = Surplus::where('organisation_id', $offer->organisation->id)->pluck('animal_id');

            foreach ($offer->species_ordered as $species) {
                if (count($organizations_surpluses) > 0 && $organizations_surpluses->contains($species->oursurplus->animal->id)) {
                    $existInClientSurpluses = true;
                    break;
                }
            }
        }

        $ourSurplus = OurSurplus::findOrFail($request->ourSurplusId);

        return response()->json(['existInOtherClientOffer' => $existInOtherClientOffer, 'existInClientSurpluses' => $existInClientSurpluses, 'ourSurplus' => $ourSurplus]);
    }

    /**
     * Add actions to offer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addActionsToOffer(Request $request)
    {
        $offerId = $request->offerId;

        foreach ($request->actions as $actionId) {
            $action      = Action::where('id', $actionId)->first();
            $offerAction = OfferAction::where('action_id', $actionId)->first();
            if ($offerAction == null) {
                $newOfferAction             = new OfferAction();
                $newOfferAction->order_id   = $offerId;
                $newOfferAction->action_id  = $actionId;
                $newOfferAction->toBeDoneBy = $action->toBeDoneBy;
                $newOfferAction->save();
            }
        }

        return response()->json();
    }

    /**
     * Edit the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editSelectedActions(Request $request)
    {
        if ($request->offerActionId == null) {
            foreach ($request->items as $id) {
                $actionToEdit = OfferAction::findOrFail($id);

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
            $actionToEdit = OfferAction::findOrFail($request->offerActionId);
            $actionToEdit->update([
                'action_date'          => $request->actionDate,
                'action_remind_date'   => $request->actionRemindDate,
                'action_received_date' => $request->actionReceivedDate,
                'remark'               => $request->actionRemark,
                'toBeDoneBy'           => $request->toBeDoneBy,
            ]);
        }

        return response()->json();
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteSelectedActions(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $actionToDelete = OfferAction::findOrFail($id);
                $actionToDelete->delete();
            }
        }

        return response()->json();
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setOriginRegion(Request $request)
    {
        $species = OfferSpecies::find($request->idOfferSpecies);

        if (empty($species)) {
            return response()->json(['error' => true, 'message' => 'The species is not found in the offer']);
        }

        if (!empty($request->origin)) {
            $species['origin'] = $request->origin;
            $species->save();
        }

        if (!empty($request->region)) {
            $species['region_id'] = $request->region;
            $species->save();
        }

        return response()->json(['error' => false, 'message' => 'Species data updated successfully']);
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetListEmailOfferSend()
    {
        $offers = Offer::where('new_offer_send', 1)->get();
        if (!empty($offers)) {
            foreach ($offers as $row) {
                $row['new_offer_send'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Offers';

        return view('components.reset_list_email_new', compact('title_dash'));
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createSentEmail($subject, $from, $email, $body, $id = null, $email_cc_array = "", $email_code = null, $reminder = null, $due_date = null)
    {
        $label     = Labels::where('name', 'offer')->first();
        $contact   = Contact::where('email', $email)->first();
        $new_email = new Email();
        if(!empty($contact) && $contact->count() > 0){
            $first_name = $contact["first_name"] ?? "";
            $last_name = $contact["last_name"] ?? "";
            $name = $first_name . " " . $last_name;
            $new_email["contact_id"] = $contact["id"] ?? null;
        }else{
            $organisation =  Organisation::where("email", $email)->first();
            $new_email["organisation_id"] = $organisation["id"] ?? null;
            $name = $organisation["name"] ?? "";
        }
        $new_email["from_email"] = $from;
        $new_email["to_email"] = $email;
        $new_email["body"] = $body;
        $new_email["cc_email"] = $email_cc_array;
        $new_email["guid"] = rand(1,100);
        $new_email["subject"] = $subject;
        $new_email["name"] = $name;
        if(!empty($reminder) && $reminder == "yes"){
            $new_email["is_remind"] = 1;
            $new_email["remind_due_date"] = $due_date;
        }
        if(!empty($id)){
            $new_email["offer_id"] = $id;
        }
        $new_email["is_send"] = 1;
        if(!empty($email_code)){
            $color = Color::where("name", $email_code)->first();
            if(!empty($color)){
                $new_email["color_id"] = $color->id;
            }
        }
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
        if(!empty($email)){
            $userToken = $GraphService->getAllUserToken();
            if(!empty($userToken)){
                foreach ($userToken as $row){
                    $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                    $user_id = $GraphService->getUserByEmail($token, $email["from_email"]);
                    if(!empty($token)){
                        $email_attachment = [];
                        if(!empty($email->attachments)){
                            foreach ($email->attachments as $key => $attachment) {
                                $email_attachment[$key]["name"] = $attachment->name;
                                $email_attachment[$key]["type"] = $attachment->type;
                                $email_attachment[$key]["content"] = file_get_contents(Storage::disk('')->path($attachment->path));
                            }
                        }
                        $email_cc_array = [];
                        if ($email["cc_email"] != null)
                            $email_cc_array = array_map('trim', explode(',', $email["cc_email"]));

                        $email_bcc_array = [];
                        if ($email["bcc_email"] != null)
                            $email_bcc_array = array_map('trim', explode(',', $email["bcc_email"]));
                        $result = $GraphService->saveSentItems($token,  $user_id->getId(), $email["subject"], $email["body"], $email["to_email"],  $email_cc_array,  $email_bcc_array, $email_attachment);
                        $email["guid"] = $result["id"];
                        $email->save();
                        if(!empty($result)){
                            $result = $GraphService->updateIsDraftEmailInbox($token,  $user_id->getId(), $result["id"]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetListEmailOfferInquiry()
    {
        $offers = Offer::where('new_offer_inquiry', 1)->get();
        if (!empty($offers)) {
            foreach ($offers as $row) {
                $row['new_offer_inquiry'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Offers';

        return view('components.reset_list_email_new', compact('title_dash'));
    }

    public function getAirfreightsByCountriesAndAirports($departure_continent, $arrival_continent, $isPallet)
    {
        $data     = collect();
        $freights = null;
        if (!empty($departure_continent) && !empty($arrival_continent)) {
            if ($isPallet == true) {
                $freights = Airfreight::where([
                    ['departure_continent', $departure_continent],
                    ['arrival_continent', $arrival_continent], ])
                    ->where(function ($query) {
                        $query->where('type', 'lowerdeck')
                            ->orWhere('type', 'maindeck');
                    })
                    ->get();
            } else {
                $freights = Airfreight::where([
                    ['departure_continent', $departure_continent],
                    ['arrival_continent', $arrival_continent], ])
                    ->where(function ($query) {
                        $query->where('type', 'volKg')
                            ->orWhere('type', null);
                    })
                    ->get();
            }

            return $freights->toArray();
        }

        return false;
    }

    public function quickChangeStatusLevelForapproval(Request $request)
    {
        $offer = Offer::find($request->id);
        if (!empty($offer)) {
            $offer['status_level'] = 'Forapproval';
            $offer->save();

            return response()->json(['error' => false, 'message' => 'Status Level Forapproval updated successfully']);
        } else {
            return response()->json(['error' => true, 'message' => 'The offer to update cannot be found']);
        }
    }

    public function getItemRelatedSurplus(Request $request)
    {
        if(!empty($request->id)){
            $oursurplus = OurSurplus::findOrFail($request->id);

            if(!empty($oursurplus)){
                if(!is_array($request) && $request->ajax()) {
                    $data["oursurplus"] = $oursurplus;
                    $data["id_data"] = $request->id_data ?? "";
                    $data["type"] = $request->type ?? "";
                    $view = View::make('offers.item_related_surplus', $data);
                    $html['error'] = false;
                    $html['content'] = $view->render();

                    return json_encode($html);
                } else {
                    $html['error']   = true;
                    $html['content'] = '';

                    return json_encode($html);
                }

            }else{
                return [];
            }
        }else{
            return [];
        }
    }

    /**
     * @param  Request  $request
     *
     * @return array|false|string
     */
    public function getSpeciesWithSameContinentAsOrigin(Request $request)
    {
        if (!empty($request->id)) {
            $oursurplus = OurSurplus::findOrFail($request->id);

            if (!empty($oursurplus)) {
                if (!is_array($request) && $request->ajax()) {
                    $data['oursurplus'] = $oursurplus;
                    $data['id_data'] = $request->id_data ?? '';
                    $data['type'] = $request->type ?? '';
                    $view = View::make('offers.item_related_species_same_continent', $data);
                    $html['error'] = false;
                    $html['content'] = $view->render();

                    return json_encode($html);
                } else {
                    $html['error']   = true;
                    $html['content'] = '';

                    return json_encode($html);
                }

            } else {
                return [];
            }
        } else {
            return [];
        }
    }
}
