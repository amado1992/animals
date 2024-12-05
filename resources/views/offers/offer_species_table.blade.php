<table class="table " id="offerSpeciesTable" width="100%" cellspacing="0">
    <thead>
    <tr class="green header">
        <th class="none"><span >SPECIES</span></th>
        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
            <th class="none"><span >COSTS</span></th>
        @endif
        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
            <th class="none"><span >SALES</span></th>
        @endif
        @if (Auth::user()->hasPermission('offers.see-profit-value'))
            <th class="none"><span >PROFIT</span></th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($offer->species_ordered as $key => $species)
        @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
            @php $sale_currency = "fa-euro-sign"; @endphp
        @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
            @php $sale_currency = "fa-dollar-sign"; @endphp
        @else
            @php $sale_currency = "fa-euro-sign"; @endphp
        @endif
        <tr class="blueborder" offerSpeciesId="{{ $species->id }}">
            <th class="gray" style="width: 35% !important;">
                <div class="d-flex ml-2 mt-1">
                    <div class="colFlex">
                        <p style="font-weight: normal;margin:0">Cost&nbsp;status</p>
                        @include('offers.costs_status', ['id' => 'sp' . $species->id, 'table' => 'offers_species', 'cost_id' => $species->id, 'selected' => $species->status])
                    </div>
                    <div class="d-flex gap" style="margin: 1px 0 0 5px">
                          <p class="align " style="@if($key != 0) margin: 19px 0px 0 0; @endif">@if($key == 0) M @endif<input type="text" class="input-group input-group-sm" style="width: 25px;" name="offerQuantityM" value="{{ $species->offerQuantityM }}" oldValue="{{ $species->offerQuantityM }}"></p>
                          <p class="align " style="@if($key != 0) margin: 19px 0px 0 0; @endif">@if($key == 0) F @endif<input type="text" class="input-group input-group-sm" style="width: 25px;" name="offerQuantityF" value="{{ $species->offerQuantityF }}" oldValue="{{ $species->offerQuantityF }}"></p>
                          <p class="align " style="@if($key != 0) margin: 19px 0px 0 0; @endif">@if($key == 0) U @endif<input type="text" class="input-group input-group-sm" style="width: 25px;" name="offerQuantityU" value="{{ $species->offerQuantityU }}" oldValue="{{ $species->offerQuantityU }}"></p>
                          <p class="align " style="@if($key != 0) margin: 19px 0px 0 0; @endif">@if($key == 0) P @endif<input type="text" class="input-group input-group-sm" style="width: 25px;" name="offerQuantityP" value="{{ $species->offerQuantityP }}" oldValue="{{ $species->offerQuantityP }}"></p>
                    </div>
                    <div class="mt-3 ml-2" style="width: 100%;">
                        <a href="{{ route("our-surplus.show", [$species->oursurplus->id]) }}" style="float: right; margin: 7px 3px 0 1px;" ><i class="fas fa-edit"></i></a>
                        <p class="name">
                            @if (!empty($species->oursurplus) && $species->oursurplus->animal)
                                <strong style="font-size: 14px;">{{$species->oursurplus->animal->common_name}}</strong> <span class="ml-1"> {{ ($species->origin != null) ? $species->origin : $species->oursurplus->origin }}</span> <span class="ml-1">{{ ($species->region != null) ? $species->region->name : $species->oursurplus->region->name ?? "" }}</span><br>
                                <span style="font-size: 14px;">({{$species->oursurplus->animal->scientific_name}})</span>
                            @else
                                ERROR - NO STANDARD SURPLUS
                            @endif
                        </p>
                    </div>
                    <input class="mr-1 ml-2 mt-2 selectorSpecies" name="species 1 checkbox" type="checkbox" value="{{ $species->id }}" style="position: absolute; margin: 66px 0 0 3px !important;"/>
                    <a href="#" class="related_surplus" data-key="{{ $key }}" data-id="{{ $species->oursurplus->id }}" data-show="true" style="margin: 53px -21px 0 -115px; width: 192px; font-size: 12px;">{{ $species->oursurplus->animal_related_surplus->count() }} Related suppliers <i class="mdi mdi-chevron-down" style="font-size: 14px;"></i></a>
                </div>
            </th>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td >
                    <div class="d-flex mt-1">
                        <div class="colFlex">
                            <p class="align" >M</p>
                            <div class="icon ">
                                <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                @if ($species->offerQuantityM > 0)
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceM" value="{{ number_format($species->offerCostPriceM, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceM, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceM" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>

                        </div>
                        <div class="colFlex">
                            <p class="align" >F</p>
                            <div class="icon">
                                @if ($species->offerQuantityF > 0)
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceF" value="{{ number_format($species->offerCostPriceF, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceF, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceF" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>

                        </div>
                        <div class="colFlex">
                            <p>U</p>
                            <div class="icon">
                                @if ($species->offerQuantityU > 0)
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceU" value="{{ number_format($species->offerCostPriceU, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceU, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceU" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>

                        </div>
                        <div class="colFlex">
                            <p >P</p>
                            <div class="icon">
                                @if ($species->offerQuantityP > 0)
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceP" value="{{ number_format($species->offerCostPriceP, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceP, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered" name="offerCostPriceP" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>

                        </div>
                        <div class="colFlex">
                            <p>Total</p>
                            <div class="icon">
                                <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                <input class="bordered current" name="cost_total_species" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_cost_price), 2, '.', '') : number_format(($species->total_cost_price_usd * $offer->currency_rate_eur), 2, '.', '')}}"/>
                            </div>
                            <div class="icon mt-1 mb-2">
                                <i class="fas fa-dollar-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                <p class="m-0"><em>{{number_format($species->total_cost_price_usd, 2, '.', '')}}</em></p>
                            </div>
                        </div>
                    </div>
                </td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td class="gray">
                    <div class="d-flex mt-1">
                        <div class="colFlex">
                            <p class="align" >M</p>
                            <div class="icon ">
                                <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                @if ($species->offerQuantityM > 0)
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceM" value="{{ number_format($species->offerSalePriceM, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceM, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceM" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>
                        </div>
                        <div class="colFlex">
                            <p class="align" >F</p>
                            <div class="icon">
                                @if ($species->offerQuantityF > 0)
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceF" value="{{ number_format($species->offerSalePriceF, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceF, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceF" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>
                        </div>
                        <div class="colFlex">
                            <p>U</p>
                            <div class="icon">
                                @if ($species->offerQuantityU > 0)
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceU" value="{{ number_format($species->offerSalePriceU, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceU, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceU" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>
                        </div>
                        <div class="colFlex">
                            <p >P</p>
                            <div class="icon">
                                @if ($species->offerQuantityP > 0)
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceP" value="{{ number_format($species->offerSalePriceP, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceP, 2, '.', '') }}">
                                @else
                                    <input type="text" class="input-group input-group-sm bordered"  name="offerSalePriceP" value="{{ number_format(0, 2, '.', '') }}" oldValue="{{ number_format(0, 2, '.', '') }}">
                                @endif
                            </div>
                        </div>
                        <div class="colFlex">
                            <p>Total</p>
                            <div class="icon ">
                                <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                <input class="bordered current" name="sales total 1" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_sale_price), 2, '.', '') : number_format(($species->total_sale_price_usd * $offer->currency_rate_eur), 2, '.', '')}}"/>
                            </div>
                            <div class="icon mt-1 mb-2">
                                <i class="fas fa-dollar-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                <p class="m-0"><em>{{ number_format($species->total_sale_price_usd, 2, '.', '') }}</em></p>
                            </div>
                        </div>
                    </div>
                    <div class="link-standard-surplus">
                            @if (Auth::user()->hasPermission('standard-surplus.read'))
                                <a href="{{ route('our-surplus.show', [$species->oursurplus->id]) }}" title="Edit values" target="_blank">
                                    <i class="fas fa-search"></i>
                                    <span style="margin-left: 2px;">Edit values</span>
                                </a>
                            @endif
                            <a href="#" class="related_species" data-key="{{ $key }}" data-id="{{ $species->oursurplus->id }}" data-show="true" style="padding-left: 4px; font-weight: bold; font-size: 12px;">{{ $species->oursurplus->species_same_continent->count() }} Related species <i class="mdi mdi-chevron-down" style="font-size: 14px;"></i></a>
                    </div>
                </td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td style="width: 7%;">
                    <div class="colFlex mt-1 margin">
                        <p class="align">Profit</p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered current" name="species 1 profit" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_sale_price- $species->total_cost_price), 2, '.', '') : number_format(((($species->total_sale_price_usd- $species->total_cost_price_usd)) * $offer->currency_rate_eur), 2, '.', '')}}"/>
                        </div>
                        <div class="icon mt-1 mb-2">
                            <i class="fas fa-dollar-sign fa-sm fa-fw  text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format(($species->total_sale_price_usd- $species->total_cost_price_usd), 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </td>
            @endif
        </tr>
        <tr>
            <td colspan="4" class="item_related_{{ $key }}">

            </td>
        </tr>

        <tr>
            <td colspan="4" class="item_related_species_same_{{ $key }}">

            </td>
        </tr>
    @endforeach
    <tr class="blue">
        <th class="none p-2">
            <p class="white m-0">SPECIES TOTALS</p>
        </th>
        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
            <th class="none pt-2 pb-2">
                <div class="d-flex move ">
                    <p class="white thin  m-0 mr-1">Animal costs total:</p>
                    <div class="colFlex">
                        <div class="icon m-0 mr-2">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                            <p class="white m-0 " >{{ number_format(($offer->offerTotalSpeciesCostPrice), 2, '.', '') }}</p>
                        </div>

                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0 "><em>{{ number_format($offer->offerTotalSpeciesCostPriceUSD, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </th>
        @endif
        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
            <th class="none pt-2 pb-2">
                <div class="d-flex move ">
                    <p class="white thin  m-0 mr-1" >Animal sales total: </p>
                    <div class="colFlex">
                        <div class="icon m-0 mr-2">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                            <p class="white m-0 " >{{ number_format(($offer->offerTotalSpeciesSalePrice), 2, '.', '') }}</p>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0 "><em>{{ number_format($offer->offerTotalSpeciesSalePriceUSD, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </th>
        @endif
        @if (Auth::user()->hasPermission('offers.see-profit-value'))
            <th class="none pt-2 pb-2">
                <div class="icon move mr-2 mt-0">
                    <i class="fas fa-euro-sign fa-sm size fa-fw mr-2 text-black-400" aria-hidden="true"></i>
                    <p class="white m-0" >{{ number_format(($offer->offerTotalSpeciesSalePrice - $offer->offerTotalSpeciesCostPrice), 2, '.', '') }}</p>
                </div>
                <div class="icon move mt-1">
                    <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                    <p class="m-0 "><em>{{ number_format(($offer->offerTotalSpeciesSalePriceUSD - $offer->offerTotalSpeciesCostPriceUSD), 2, '.', '') }}</em></p>
                </div>
            </th>
        @endif
    </tr>
    </tbody>
</table>
