<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserWebsiteInquiryRequest;
use App\Models\AdditionalCost;
use App\Models\Airfreight;
use App\Models\Contact;
use App\Models\Offer;
use App\Models\OfferAdditionalCost;
use App\Models\OfferSpecies;
use App\Models\OfferSpeciesAirfreight;
use App\Models\OfferSpeciesCrate;
use App\Models\OurSurplus;
use App\Models\User;
use App\Services\OfferService;
use DOMPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestWebsiteController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    /**
     * Create request from website.
     *
     * @param  \App\Http\Requests\UserWebsiteInquiryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function createRequest(UserWebsiteInquiryRequest $request)
    {
        $user_member = User::where('id', $request->input('userId'))->first();

        $admin_user = User::where('email', 'administration@zoo-services.com')->first();

        $newOffer             = new Offer();
        $newOffer->manager_id = ($admin_user) ? $admin_user->id : null;
        $newOffer->creator    = 'Client';

        $offerNumber            = Offer::ofCurrentYear()->max('offer_number');
        $newOffer->offer_number = ($offerNumber) ? $offerNumber + 1 : 1;

        $izsContact = Contact::where('email', 'izs@zoo-services.com')->first();

        if (!empty($user_member->contact)) {
            $newOffer->client_id           = $user_member->contact->id;
            $newOffer->delivery_country_id = ($user_member->contact->organisation && $user_member->contact->organisation->country) ? $user_member->contact->organisation->country->id : null;
            $country_airport               = $user_member->contact->organisation->country->airports->first();
            $newOffer->delivery_airport_id = ($country_airport != null) ? $country_airport->id : null;
        }

        $newOffer->supplier_id = ($izsContact) ? $izsContact->id : null;

        $newOffer->offer_status = 'Pending';
        $newOffer->status_level = 'Inquiry';
        $newOffer->save();

        $index = 0;
        foreach ($request->input('surplusses') as $inquiryAnimal) {
            $surplus = OurSurplus::where('id', $inquiryAnimal['surplus_id'])->firstOrFail();

            if ($index == 0) {
                $newOffer->update(['offer_currency' => ($surplus->sale_currency != null ? $surplus->sale_currency : 'EUR')]);
            }

            $newOfferSpecies                  = new OfferSpecies();
            $newOfferSpecies->offer_id        = $newOffer->id;
            $newOfferSpecies->oursurplus_id   = $surplus->id;
            $newOfferSpecies->offerQuantityM  = $inquiryAnimal['quantityM'] ?? 0;
            $newOfferSpecies->offerQuantityF  = $inquiryAnimal['quantityF'] ?? 0;
            $newOfferSpecies->offerQuantityU  = $inquiryAnimal['quantityU'] ?? 0;
            $newOfferSpecies->offerQuantityP  = ( $inquiryAnimal['quantityM'] > 0 &&  $inquiryAnimal['quantityM'] == $inquiryAnimal['quantityF'] && $surplus->salePriceP > 0) ? $inquiryAnimal['quantityM'] * 2 : 0;
            $newOfferSpecies->offerCostPriceM = $surplus->salePriceM * 0.80;
            $newOfferSpecies->offerCostPriceF = $surplus->salePriceF * 0.80;
            $newOfferSpecies->offerCostPriceU = $surplus->salePriceU * 0.80;
            $newOfferSpecies->offerCostPriceP = $surplus->salePriceP * 0.80;
            $newOfferSpecies->offerSalePriceM = ($inquiryAnimal['quantityM'] > 0) ? $surplus->salePriceM : 0;
            $newOfferSpecies->offerSalePriceF = ($inquiryAnimal['quantityF'] > 0) ? $surplus->salePriceF : 0;
            $newOfferSpecies->offerSalePriceU = ($inquiryAnimal['quantityU'] > 0) ? $surplus->salePriceU : 0;
            $newOfferSpecies->offerSalePriceP = ( $inquiryAnimal['quantityM'] > 0 &&  $inquiryAnimal['quantityM'] == $inquiryAnimal['quantityF'] && $surplus->salePriceP > 0) ? $surplus->salePriceP : 0;
            $newOfferSpecies->origin          = $surplus->origin;
            $newOfferSpecies->region_id       = $surplus->region_id;
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

            if (!empty($newOffer->delivery_country)) {
                $departure_continent = $surplus->region_id;
                $arrival_continent   = $newOffer->delivery_country->region_id;
                if ($newOffer->airfreight_type == 'pallets') {
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

            $index++;
        }

        $additionalCosts = AdditionalCost::get();

        foreach ($additionalCosts as $ac) {
            $newOfferAdditionalCost            = new OfferAdditionalCost();
            $newOfferAdditionalCost->offer_id  = $newOffer->id;
            $newOfferAdditionalCost->name      = $ac->name;
            $newOfferAdditionalCost->quantity  = 1;
            $newOfferAdditionalCost->currency  = $newOffer->offer_currency;
            $newOfferAdditionalCost->costPrice = ($newOffer->offer_currency == 'USD') ? $ac->usdCostPrice : $ac->eurCostPrice;
            $newOfferAdditionalCost->salePrice = ($newOffer->offer_currency == 'USD') ? $ac->usdSalePrice : $ac->eurSalePrice;
            $newOfferAdditionalCost->is_test   = $ac->is_test;
            $newOfferAdditionalCost->save();
        }

        $fileName      = 'Offer ' . $newOffer->full_number . '.pdf';
        $document_info = $this->offerService->create_offer_pdf($newOffer->id);
        $html          = str_replace('http://127.0.0.1:8000', base_path() . '/public', $document_info);
        $pdf           = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');
        Storage::put('public/offers_docs/' . $newOffer->full_number . '/' . $fileName, $pdf->output());

        return response()->json(['success' => true]);
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
}
