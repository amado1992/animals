<div class="table-responsive">
    <table class="table " id="offerSpeciesAirfreightTable" width="100%" cellspacing="0">
        <thead>
        <tr class="green header">
            <th class="none"><i class="fas fa fa-plane fa-sm fa-fw mr-3 ml-2 fa-1x fa-rotate-45 text-black-400" aria-hidden="true"></i><span >FLIGHTS</span></th>
            <th class="none"><span >FLIGHT COSTS</span></th>
            <th class="none"><span >FLIGHT SALES</span></th>
            <th class="none"><span >PROFIT</span></th>
        </tr>
        </thead>
        <tbody>
        @foreach($offer->species_ordered as $species)
            @if (count($species->species_airfreights) > 0)
                @foreach ($species->species_airfreights as $specie_airfreight)
                    @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                        @php $sale_currency = "fa-euro-sign"; @endphp
                    @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                        @php $sale_currency = "fa-dollar-sign"; @endphp
                    @else
                        @php $sale_currency = "fa-euro-sign"; @endphp
                    @endif
                    <tr class='blueborder' speciesAirfreightId="{{$specie_airfreight->id}}">
                        <th class="gray width" style="width: 30%;">
                            <div class="d-flex ml-2 mt-1">
                                <div class="colFlex">
                                    <p style="font-weight: normal;margin:4px 0 0">Cost&nbsp;status</p>
                                    @include('offers.costs_status', ['id' => 'sa' . $specie_airfreight->id, 'table' => 'offers_species_airfreights', 'cost_id' => $specie_airfreight->id, 'selected' => $specie_airfreight->status])
                               </div>
                               <div class="colFlex ml-2 mt-1">
                                    <div class="d-flex gap">
                                        @if (Auth::user()->hasPermission('offers.update'))
                                            <div>
                                                <input type="checkbox" class="selectorSpeciesAirfreight mr-3" value="{{ $species->id }}">
                                                @if (Auth::user()->hasPermission('airfreights.create'))
                                                    <a href="{{ route('airfreights.create', [$species->id, $offer->offer_airfreight_type]) }}" class="mr-3" title="Add new airfreight"><i class="fas fa-plus"></i></a>
                                                @endif
                                                <a href="#" title="Select airfreight for specific record" id="selectAirfreights" data-toggle="modal" data-id="{{ $species->id }}" isPallet="0"><i class="fas fa-plane"></i></a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex gap">
                                        <p class="align ">P
                                            <span class= "check block ">
                                                {{ $species->offerQuantityP }}
                                            </span>
                                        </p>
                                        <p class="align">M <span class= "check block ">{{ $species->offerQuantityM }}</span></p>
                                        <p class="align">F <span class= "check block ">{{ $species->offerQuantityF }}</span></p>
                                        <p class="align ">U <span class= "check block ">{{ $species->offerQuantityU }}</span></p>
                                    </div>
                                </div>
                                <div class="mt-2" style="margin: 2px 0 0 6px !important; width: 100%;">
                                    <p class="name m-0 mt-1" style="margin: 0 0 0 -4px !important;">
                                        @if ($species->oursurplus && $species->oursurplus->animal)
                                            {{ $species->oursurplus->animal->common_name }}
                                        @else
                                            ERROR - NO STANDARD SURPLUS
                                        @endif
                                    </p>
                                    <div class="row mr-2">
                                        <div class="col-md-12">
                                            <p class="species m-0">
                                                @if (!empty($specie_airfreight) && !empty($specie_airfreight->airfreight))
                                                    <a href="{{ route('airfreights.edit', [$specie_airfreight->airfreight->id]) }}" style="float: right; margin: -23px 8px 0 1px;" ><i class="fas fa-edit"></i></a>

                                                    FROM:
                                                    <span class="name country mr-2">{{$specie_airfreight->airfreight->from_continent->name}}</span><br>
                                                    TO:
                                                    <span class="name country mr-2">{{$specie_airfreight->airfreight->to_continent->name}}</span>
                                                @else
                                                    FROM:
                                                    <span class="name country mr-2">{{($species->region != null) ? $species->region->name : $species->oursurplus->region->name ?? ""}}</span><br>
                                                    TO:
                                                    <span class="name country mr-2">{{$offer->delivery_country->region->name}}</span>
                                                @endif
                                                <br>
                                                Total vol.kg:
                                                <span class="name country mr-2">
                                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                                        {{ number_format($species->oursurplus->animal->body_weight, 2, '.', '') }} Kg
                                                    @else
                                                        ERROR - NO STANDARD SURPLUS
                                                    @endif
                                                </span>
                                                Total act.kg:
                                                <span class="name country">{{ number_format($species->total_volKg, 2, '.', '') }} Kg</span>
                                            </p>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </th>
                        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                            <td >
                                <div class="d-flex mt-1">
                                    <div class="d-flex mt-1">
                                        <div class="colFlex">
                                            <p class="align" >vol.kg</p>
                                            <div class="icon ">
                                                <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                                <input type="text" class="input-group input-group-sm bordered" name="cost_volKg" value="{{ number_format($specie_airfreight->cost_volKg, 2, '.', '') }}" oldValue="{{ number_format($specie_airfreight->cost_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p class="align" >M</p>
                                            <div class="icon ">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightCostPriceM" value="{{ number_format($species->species_crate->m_volKg * $specie_airfreight->cost_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p class="align" >F</p>
                                            <div class="icon">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightCostPriceF" value="{{ number_format($species->species_crate->f_volKg * $specie_airfreight->cost_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p>U</p>
                                            <div class="icon">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightCostPriceU" value="{{ number_format($species->species_crate->u_volKg * $specie_airfreight->cost_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p >P</p>
                                            <div class="icon">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightCostPriceP" value="{{ number_format($species->species_crate->p_volKg * $specie_airfreight->cost_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p>Costs total</p>
                                            <div class="icon">
                                                <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                                <input class="bordered current" name="cost total 1" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_volKg * $specie_airfreight->cost_volKg), 2, '.', '') : number_format((($species->total_volKg * $specie_airfreight->cost_volKg * $species->currency_rate) * $offer->currency_rate_eur), 2, '.', '')}}"/>
                                            </div>
                                            <div class="icon mt-1 mb-2">
                                                <i class="fas fa-dollar-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                                <p class="m-0"><em>{{ number_format($species->total_volKg * $specie_airfreight->cost_volKg * $species->currency_rate, 2, '.', '') }}</em></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endif
                        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                            <td class="gray">
                                <div class="d-flex mt-1">
                                    <div class="d-flex mt-1">
                                        <div class="colFlex">
                                            <p class="align" >vol.kg</p>
                                            <div class="icon ">
                                                <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                                <input type="text" class="input-group input-group-sm bordered" name="sale_volKg" value="{{ number_format($specie_airfreight->sale_volKg, 2, '.', '') }}" oldValue="{{ number_format($specie_airfreight->sale_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p class="align" >M</p>
                                            <div class="icon ">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightSalePriceM" value="{{ number_format($species->species_crate->m_volKg * $specie_airfreight->sale_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p class="align" >F</p>
                                            <div class="icon">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightSalePriceF" value="{{ number_format($species->species_crate->f_volKg * $specie_airfreight->sale_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p>U</p>
                                            <div class="icon">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightSalePriceU" value="{{ number_format($species->species_crate->u_volKg * $specie_airfreight->sale_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p >P</p>
                                            <div class="icon">
                                                <input type="text" class="input-group input-group-sm bordered" name="airfreightSalePriceP" value="{{ number_format($species->species_crate->p_volKg * $specie_airfreight->sale_volKg, 2, '.', '') }}">
                                            </div>
                                        </div>
                                        <div class="colFlex">
                                            <p>Sales total</p>
                                            <div class="icon">
                                                <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                                <input class="bordered current" name="cost total 1" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_volKg * $specie_airfreight->sale_volKg), 2, '.', '') : number_format((($species->total_volKg * $specie_airfreight->sale_volKg * $species->currency_rate) * $offer->currency_rate_eur), 2, '.', '')}}"/>
                                            </div>
                                            <div class="icon mt-1 mb-2">
                                                <i class="fas fa-dollar-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                                <p class="m-0"><em>{{ number_format($species->total_volKg * $specie_airfreight->sale_volKg * $species->currency_rate, 2, '.', '') }}</em></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endif
                        @if (Auth::user()->hasPermission('offers.see-profit-value'))
                            <td style="width: 7%;">
                                <div class="colFlex mt-2 margin">
                                    <p class="align mr-4">Profit</p>
                                    <div class="icon">
                                        <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                        <input class="bordered current" name="species 1 profit" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->total_airfreight_sale_price - $species->total_airfreight_cost_price), 2, '.', '') : number_format((($species->total_airfreight_sale_price_usd - $species->total_airfreight_cost_price_usd) * $offer->currency_rate_eur), 2, '.', '')}}"/>
                                    </div>
                                    <div class="icon mt-1 mb-2">
                                        <i class="fas fa-dollar-sign fa-sm fa-fw  text-black-400" aria-hidden="true"></i>
                                        <p class="m-0"><em>{{ number_format($species->total_airfreight_sale_price_usd - $species->total_airfreight_cost_price_usd, 2, '.', '') }}</em></p>
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr class='blueborder'>
                    <th class="gray width">
                        <div class="d-flex ml-2 mt-1">
                            <div class="colFlex">
                                <div class="d-flex gap">
                                    @if (Auth::user()->hasPermission('offers.update'))
                                        <div>
                                            <input type="checkbox" class="selectorSpeciesAirfreight mr-3" value="{{ $species->id }}">
                                            @if (Auth::user()->hasPermission('airfreights.create'))
                                                <a href="{{ route('airfreights.create', [$species->id, $offer->offer_airfreight_type]) }}" class="mr-3" title="Add new airfreight"><i class="fas fa-plus"></i></a>
                                            @endif
                                            <a href="#" title="Select airfreight for specific record" id="selectAirfreights" data-toggle="modal" data-id="{{ $species->id }}" isPallet="0"><i class="fas fa-plane"></i></a>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex gap">
                                    <p class="align ">P
                                        <span class= "check block ">
                                            @if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerSalePriceP > 0)
                                                {{ $species->offerQuantityM }}
                                            @else
                                                0
                                            @endif
                                            </span>
                                    </p>
                                    <p class="align">M <span class= "check block ">{{ $species->offerQuantityM }}</span></p>
                                    <p class="align">F <span class= "check block ">{{ $species->offerQuantityF }}</span></p>
                                    <p class="align ">U <span class= "check block ">{{ $species->offerQuantityU }}</span></p>
                                </div>
                            </div>
                            <div class="mt-2 ml-2" >
                                <p class="name m-0 mt-1" style="margin: -7px 0 0 -4px !important;">
                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                        {{ $species->oursurplus->animal->common_name }}
                                    @else
                                        ERROR - NO STANDARD SURPLUS
                                    @endif
                                </p>
                                <div class="row mr-2">
                                    <div class="col-md-12">
                                        <p class="species m-0">
                                            @if (!empty($specie_airfreight) && !empty($specie_airfreight->airfreight))
                                                <a href="{{ route('airfreights.edit', [$specie_airfreight->airfreight->id]) }}" style="float: right; margin: -23px 8px 0 1px;" ><i class="fas fa-edit"></i></a>

                                                FROM:
                                                <span class="name country mr-2">{{$specie_airfreight->airfreight->from_continent->name}}</span><br>
                                                TO:
                                                <span class="name country mr-2">{{$specie_airfreight->airfreight->to_continent->name}}</span><br>
                                            @else
                                                FROM:
                                                <span class="name country mr-2">{{($species->region != null) ? $species->region->name : $species->oursurplus->region->name ?? ""}}</span><br>
                                                TO:
                                                <span class="name country mr-2">{{$offer->delivery_country->region->name}}</span><br>
                                            @endif
                                            Total vol.kg:
                                            <span class="name country mr-2">
                                                @if ($species->oursurplus && $species->oursurplus->animal)
                                                    {{ number_format($species->oursurplus->animal->body_weight, 2, '.', '') }} Kg
                                                @else
                                                    ERROR - NO STANDARD SURPLUS
                                                @endif
                                            </span>
                                            Total act.kg:
                                            <span class="name country">{{ number_format($species->total_volKg, 2, '.', '') }} Kg</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <td >

                    </td>
                    <td class="gray">

                    </td>
                    <td>

                    </td>
                </tr>
            @endif
        @endforeach
        <tr class="blue">
            <th class="none p-2">
                <div class="d-flex footer ">
                    <div class="colFlex" style="align-items: flex-start;">
                        <div class="icon m-0">
                            <i class="fas fa-plane fa-rotate-45 fa-sm fa-1x fa-fw  mr-1 ml-1  text-black-400" aria-hidden="true"></i>
                            <p class="white m-0 ">FLIGHT TOTALS</p><br>
                        </div>
                        <div class="icon mt-1">
                            <div class="colFlex">
                                <div class="icon m-0 mr-2">
                                    <i class="fas fa-weight-hanging text-black-400" aria-hidden="true" style="margin: 0 7px 0 5px;"></i>
                                    <p class="white m-0 " >Actual Weight: {{ number_format($offer->offerTotalActualWeight, 2, '.', '') }} Kg</p>
                                    <i class="fas fa-weight-hanging text-black-400" aria-hidden="true" style="margin: 0 7px 0 5px;"></i>
                                    <p class="white m-0 " >VolKg: {{ number_format($offer->offerTotalVolKg, 2, '.', '') }} Kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <th class="none pt-2 pb-2">
                    <div class="d-flex move ">
                        <p class="white thin  m-0 mr-1">Flight costs total:</p>
                        <div class="colFlex">
                            <div class="icon m-0 mr-2">
                                <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                                <p class="white m-0 " >{{ number_format($offer->offerTotalAirfreightsCostPrice, 2, '.', '') }}</p>
                            </div>
                            <div class="icon mt-1">
                                <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                                <p class="m-0 "><em>{{ number_format($offer->offerTotalAirfreightsCostPriceUSD, 2, '.', '') }}</em></p>
                            </div>
                        </div>
                    </div>
                </th>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <th class="none pt-2 pb-2">
                    <div class="d-flex move ">
                        <p class="white thin  m-0 mr-1" >Flight sales total: </p>
                        <div class="colFlex">
                            <div class="icon m-0 mr-2">
                                <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                                <p class="white m-0 " >{{ number_format($offer->offerTotalAirfreightsSalePrice, 2, '.', '') }}</p>
                            </div>
                            <div class="icon mt-1">
                                <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                                <p class="m-0 "><em>{{ number_format($offer->offerTotalAirfreightsSalePriceUSD, 2, '.', '') }}</em></p>
                            </div>
                        </div>
                    </div>
                </th>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <th class="none pt-2 pb-2">
                    <div class="icon move mr-2 mt-0">
                        <i class="fas fa-euro-sign fa-sm size fa-fw mr-2 text-black-400" aria-hidden="true"></i>
                        <p class="white m-0" >{{ number_format(($offer->offerTotalAirfreightsSalePrice - $offer->offerTotalAirfreightsCostPrice), 2, '.', '') }}</p>
                    </div>
                    <div class="icon move mt-1">
                        <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                        <p class="m-0 "><em>{{ number_format(($offer->offerTotalAirfreightsSalePriceUSD - $offer->offerTotalAirfreightsCostPriceUSD), 2, '.', '') }}</em></p>
                    </div>

                </th>
            @endif
        </tr>
        </tbody>
    </table>
</div>

