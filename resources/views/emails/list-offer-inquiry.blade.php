<!doctype html>
<html>
@include('emails.email-header')
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td class="container">
            <div class="content">
                <p>{{$email_title}},</p>
                <br>
                @if (!empty($offer_send))
                    <a href="{{url('/')}}/offers/resetListEmailOfferInquiry">Reset List</a>
                @endif
                <table style="font-size: 11px;" border="0">
                    @php($date = "")
                    @foreach ($offers as $offer)
                        <tr style="vertical-align: top;">
                            <td colspan="7">
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td colspan="7">
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td colspan="7">
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td colspan="7">
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td colspan="7">
                            </td>
                        </tr>
                        <tr @if(!empty($offer->reminder_second) && $offer->reminder_second == 1) style="color: rgb(211, 12, 12) !important" @endif>
                            <td style="font-weight: bold; margin-bottom: 10px;">
                                Status
                            </td>
                            <td style="font-weight: bold; margin-bottom: 10px;">
                                Req. No
                            </td>
                            <td style="font-weight: bold; margin-bottom: 10px;">
                                Client
                            </td>
                            <td style="font-weight: bold; margin-bottom: 10px;">
                                Total species
                            </td>
                            <td style="font-weight: bold; margin-bottom: 10px;">
                                Url
                            </td>
                        </tr>
                        @if($date != date("d-m-Y", strtotime($offer->next_reminder_at)) && $date_send)
                            @php($date = date("d-m-Y", strtotime($offer->next_reminder_at)))
                            <tr style="vertical-align: top;">
                                <td colspan="7">
                                </td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td colspan="7">
                                </td>
                            </tr>
                            <tr style="vertical-align: top; @if(!empty($offer->reminder_second) && $offer->reminder_second == 1) color: rgb(211, 12, 12) !important; @endif" >
                                <td colspan="7">
                                    <b>Date Send Out: {{ date("d F Y", strtotime($offer->next_reminder_at)) }}</b>
                                </td>
                            </tr>
                        @endif
                        <tr style="font-size: 10px !important; @if(!empty($offer->reminder_second) && $offer->reminder_second == 1) color: rgb(211, 12, 12) !important; @endif">
                            <td style="1px solid #000">
                                {{ $offer->offer_status }}
                                @if(!empty($offer->status_level))
                                    @if ($offer->status_level === "Forapproval")
                                        (For approval)
                                    @elseif ($offer->status_level === "Tosearch")
                                        (To search)
                                    @else
                                        ({{$offer->status_level}})
                                    @endif
                                @endif
                            </td>
                            <td>
                                <span>{{ $offer->full_number }}</span><br>
                                <span>{{ $offer->offer_type }}</span>
                            </td>
                            <td>
                                @if ($offer->client)
                                    {{ ($offer->client->organisation && $offer->client->organisation->name) ? $offer->client->organisation->name : $offer->client->full_name }}<br>
                                    ({{ $offer->client->email }})
                                @elseif($offer->organisation)
                                    {{ ($offer->organisation && $offer->organisation->name) ? $offer->organisation->name : "" }}<br>
                                    ({{ $offer->organisation->email ?? "" }})
                                @else
                                    <p>No client added yet.</p>
                                @endif
                            </td>
                            <td>
                                @if (count($offer->species_ordered) == 1)
                                    @foreach ($offer->species_ordered as $species)
                                        {{ ($species->oursurplus && $species->oursurplus->sale_currency) ? $species->oursurplus->sale_currency : 'ERROR' }} {{ number_format($species->total_sales_price, 2, '.', '') }}<br>
                                    @endforeach
                                @elseif(count($offer->species_ordered) > 1)
                                    {{ ($offer->species_ordered[0]->oursurplus && $offer->species_ordered[0]->oursurplus->sale_currency) ? $offer->species_ordered[0]->oursurplus->sale_currency : 'ERROR' }} {{ number_format($offer->species_ordered[0]->total_sales_price, 2, '.', '') }}
                                @endif
                            </td>
                            <td>
                                <a href="{{env("APP_URL")}}/offers/{{$offer->id}}">{{env("APP_URL")}}/offers/{{$offer->id}}</a>
                            </td>
                        </tr>
                        @if (!empty($offer->species_ordered))
                            <tr @if(!empty($offer->reminder_second) && $offer->reminder_second == 1) style="color: rgb(211, 12, 12) !important" @endif>
                                <td><b>SPECIES</b></td>
                                <td><b>QUANTITY</b></td>
                                <td><b>COSTS</b></td>
                                <td><b>SALES</b></td>
                                <td><b>PROFIT</b></td>
                            </tr>
                            @foreach($offer->species_ordered as $key => $species)
                                <tr @if(!empty($offer->reminder_second) && $offer->reminder_second == 1) style="color: rgb(211, 12, 12) !important" @endif>
                                    <td>
                                        @if (!empty($species->oursurplus) && $species->oursurplus->animal)
                                            {{$species->oursurplus->animal->common_name}} <br> ({{$species->oursurplus->animal->scientific_name}})
                                        @else
                                            ERROR - NO STANDARD SURPLUS
                                        @endif
                                    </td>
                                    <td>
                                        M: {{ $species->offerQuantityM }} F: {{ $species->offerQuantityF }} U: {{ $species->offerQuantityU }}
                                    </td>
                                    <td>
                                        M:
                                        @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                                            €
                                        @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                                            $
                                        @else
                                            €
                                        @endif
                                        @if ($species->offerQuantityM > 0)
                                            {{ number_format($species->offerCostPriceM, 2, '.', '') }}
                                        @else
                                            {{ number_format(0, 2, '.', '') }}
                                        @endif
                                        F:
                                        @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                                            €
                                        @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                                            $
                                        @else
                                            €
                                        @endif
                                        @if ($species->offerQuantityF > 0)
                                            {{ number_format($species->offerCostPriceF, 2, '.', '') }}
                                        @else
                                            {{ number_format(0, 2, '.', '') }}
                                        @endif
                                        U:
                                        @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                                            €
                                        @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                                            $
                                        @else
                                            €
                                        @endif
                                        @if ($species->offerQuantityU > 0)
                                            {{ number_format($species->offerCostPriceU, 2, '.', '') }}
                                        @else
                                            {{ number_format(0, 2, '.', '') }}
                                        @endif
                                        <br>
                                        Total :
                                        € {{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_cost_price), 2, '.', '') : number_format(($species->total_cost_price_usd * $offer->currency_rate_eur), 2, '.', '')}}
                                        $ {{number_format($species->total_cost_price_usd, 2, '.', '')}}
                                    </td>
                                    <td>
                                        M:
                                        @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                                            €
                                        @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                                            $
                                        @else
                                            €
                                        @endif
                                        @if ($species->offerQuantityM > 0)
                                            {{ number_format($species->offerSalePriceM, 2, '.', '') }}
                                        @else
                                            {{ number_format(0, 2, '.', '') }}
                                        @endif
                                        F:
                                        @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                                            €
                                        @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                                            $
                                        @else
                                            €
                                        @endif
                                        @if ($species->offerQuantityF > 0)
                                            {{ number_format($species->offerSalePriceF, 2, '.', '') }}
                                        @else
                                            {{ number_format(0, 2, '.', '') }}
                                        @endif
                                        U:
                                        @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                                            €
                                        @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                                            $
                                        @else
                                            €
                                        @endif
                                        @if ($species->offerQuantityU > 0)
                                            {{ number_format($species->offerSalePriceU, 2, '.', '') }}
                                        @else
                                            {{ number_format(0, 2, '.', '') }}
                                        @endif
                                        <br>
                                        Total :
                                        € {{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_sale_price), 2, '.', '') : number_format(($species->total_sale_price_usd * $offer->currency_rate_eur), 2, '.', '')}}
                                        $ {{number_format($species->total_sale_price_usd, 2, '.', '')}}
                                    </td>
                                    <td>
                                        Total :
                                        € {{ number_format(($offer->offerTotalSpeciesSalePrice - $offer->offerTotalSpeciesCostPrice), 2, '.', '') }}
                                        $ {{ number_format(($offer->offerTotalSpeciesSalePriceUSD - $offer->offerTotalSpeciesCostPriceUSD), 2, '.', '') }}
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td colspan="7">
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td colspan="7">
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td colspan="7">
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td colspan="7">
                                    </td>
                                </tr>
                                <tr style="vertical-align: top;">
                                    <td colspan="7">
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </table>
                <br>
                <br>
                @include('emails.email-signature')
            </div>
        </td>
    </tr>
</table>
</body>
</html>
