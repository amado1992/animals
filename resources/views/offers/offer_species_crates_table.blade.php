<div class="table-responsive">
    <table class="table " id="offerSpeciesCrateTable" width="100%" cellspacing="0">
        <thead>
        <tr class="green header text-center">
            <th class="none"><i class="fas fa-dice-d6 fa-sm fa-fw fa-1x mr-3 ml-1 text-black-400" aria-hidden="true"></i><span >CRATES</span></th>
            <th class="none"><span > CRATE COSTS</span></th>
            <th class="none"><span >CRATE SALES</span></th>
            <th class="none"><span >PROFIT</span></th>
        </tr>
        </thead>
        <tbody>
        @foreach($offer->species_ordered as $species)
            @if(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR")
                @php $sale_currency = "fa-euro-sign"; @endphp
            @elseif(!empty($species->oursurplus) && $species->oursurplus->sale_currency === "USD")
                @php $sale_currency = "fa-dollar-sign"; @endphp
            @else
                @php $sale_currency = "fa-euro-sign"; @endphp
            @endif
            <tr offerSpeciesCrateId="{{ $species->species_crate->id }}" class='blueborder'>               
                <th class="gray" style="width: 30%;">
                    <div class="d-flex ml-2 mt-1">
                       <div class="colFlex">
                           <p style="font-weight: normal;margin:4px 0 0">Cost&nbsp;status</p>
                           @include('offers.costs_status', ['id' => 'sc' . $species->species_crate->offer_species_id.$species->species_crate->crate_id, 'table' => 'offers_species_crates', 'cost_id' => $species->species_crate->id, 'selected' => $species->species_crate->status])
                        </div>
                        <div class="colFlex ml-2 mt-1">
                        <div class="colFlex">
                            <div class="d-flex gap">
                                <p class="align">P <input type="text" class="input-group input-group-sm check block" name="quantity_pairs" value="{{ $species->species_crate->quantity_pairs }}" oldValue="{{ $species->species_crate->quantity_pairs }}"></p>
                                <p class="align">M <input type="text" class="input-group input-group-sm check block" name="quantity_males" value="{{ $species->species_crate->quantity_males }}" oldValue="{{ $species->species_crate->quantity_males }}"></p>
                                <p class="align">F <input type="text" class="input-group input-group-sm check block" name="quantity_females" value="{{ $species->species_crate->quantity_females }}" oldValue="{{ $species->species_crate->quantity_females }}"></p>
                                <p class="align">U <input type="text" class="input-group input-group-sm check block" name="quantity_unsexed" value="{{ $species->species_crate->quantity_unsexed }}" oldValue="{{ $species->species_crate->quantity_unsexed }}"></p>
                            </div>
                        </div>
                        <div class="ml-2" style="margin: -26px 0 0 13px !important;">
                            @if (!empty($species->species_crate->crate_id))
                                <a href="{{ route('crates.edit', [$species->species_crate->crate_id]) }}" style="float: right; margin: 4px 3px 0 1px;" ><i class="fas fa-edit"></i></a>
                            @endif
                            <p class="name @if(!isset($species->species_crate->crate_id)) text-danger @endif" style="margin: 10px 0 2px 0;">
                                @if ($species->oursurplus && $species->oursurplus->animal)
                                    {{ $species->oursurplus->animal->common_name }}
                                @else
                                    ERROR - NO STANDARD SURPLUS
                                @endif
                            </p>
                            @if (Auth::user()->hasPermission('offers.update'))
                                <div class="row">
                                    <div class="col-md-4" style="padding: 0 0 0 3px !important;">
                                        <select class="mb-1" name="crateSelection" id="crateSelection">
                                            <option @if(!isset($species->species_crate->crate_id)) selected @endif value="0">--Select crate--</option>
                                            @foreach ($species->species_crates as $crate)
                                                <option value="{{ $crate->id }}" @if(isset($species->species_crate->crate_id) && $crate->id == $species->species_crate->crate_id) selected @endif>{{ $crate->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4" style="margin: -14px 0 0px 0; padding: 0 0 0 5px !important;">
                                        <div id="dimensions" class="d-flex">
                                            <div class="colFlex mr-1">
                                                <p class="align species" style="margin: -2px 0 2px 0;">L</p>
                                                <input type="text" class="input-group input-group-sm d-inline-block bordered" name="length" value="{{ $species->species_crate->length }}" oldValue="{{ $species->species_crate->length }}">
                                            </div>
                                            <div class="colFlex mr-1">
                                                <p class="align species" style="margin: -2px 0 2px 0;">B</p>
                                                <input type="text" class="input-group input-group-sm d-inline-block bordered" name="wide" value="{{ $species->species_crate->wide }}" oldValue="{{ $species->species_crate->wide }}">
                                            </div>
                                            <div class="colFlex mr-1">
                                                <p class="align species" style="margin: -2px 0 2px 0;">H</p>
                                                <input type="text" class="input-group input-group-sm d-inline-block bordered" name="height" value="{{ $species->species_crate->height }}" oldValue="{{ $species->species_crate->height }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="margin: -14px 0 0px 0; padding: 0 6px 0 0 !important;">
                                        <div id="dimensions" class="d-flex">
                                            <div class="colFlex mr-2">
                                                <p class="align species" style="margin: -2px 0 2px 0;">VOL.KG</p>
                                                <input type="text" class="input-group input-group-sm d-inline-block bordered" disabled="disabledv" name="length" value="{{ number_format($species->total_volKg, 2, '.', '') }} Kg" oldValue="{{ number_format($species->total_volKg, 2, '.', '') }} Kg">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            @elseif ($species->species_crate)
                                <p class="name m-0 mt-1 mb-2">{{ $species->species_crate->name }}</p>
                                <div id="dimensions" class="d-flex mb-3 ">
                                    <div class="colFlex mr-2">
                                        <p class="align species m-0" >L</p>
                                        <p class="name m-0 mt-1 mb-2">{{ $species->species_crate->length }}</p>
                                    </div>
                                    <div class="colFlex mr-2">
                                        <p class="align species m-0" >B</p>
                                        <p class="name m-0 mt-1 mb-2">{{ $species->species_crate->wide }}</p>
                                    </div>
                                    <div class="colFlex mr-2">
                                        <p class="align species m-0" >H</p>
                                        <p class="name m-0 mt-1 mb-2">{{ $species->species_crate->height }}</p>
                                    </div>
                                </div>
                            @else
                                &nbsp;
                            @endif
                        </div>
                    </div>
                </th>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td >
                        <div class="d-flex mt-1">
                            <div class="colFlex">
                                <p class="align" >Costs/Each</p>
                                <div class="icon ">
                                    <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                    <input type="text" class="input-group input-group-sm bordered" name="cost_price" value="{{ number_format($species->species_crate->cost_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p class="align" >M</p>
                                <div class="icon ">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateCostPriceM" value="{{ number_format($species->species_crate->quantity_males * $species->species_crate->cost_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p class="align" >F</p>
                                <div class="icon">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateCostPriceF" value="{{ number_format($species->species_crate->quantity_females * $species->species_crate->cost_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p>U</p>
                                <div class="icon">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateCostPriceU" value="{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->cost_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p >P</p>
                                <div class="icon">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateCostPriceP" value="{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->cost_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p>Costs total</p>
                                <div class="icon">
                                    <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                    <input class="bordered current" name="cost total 1" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->species_crate->total_cost_price), 2, '.', '') : number_format(($species->species_crate->total_cost_price_usd * $offer->currency_rate_eur), 2, '.', '')}}"/>
                                </div>
                                <div class="icon mt-1 mb-2">
                                    <i class="fas fa-dollar-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                    <p class="m-0"><em>{{ number_format($species->species_crate->total_cost_price_usd, 2, '.', '') }}</em></p>
                                </div>
                            </div>
                        </div>
                    </td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td class="gray">
                        <div class="d-flex mt-1">
                            <div class="colFlex">
                                <p class="align" >Sales/Each</p>
                                <div class="icon ">
                                    <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                    <input type="text" class="input-group input-group-sm bordered" name="sale_price" value="{{ number_format($species->species_crate->sale_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p class="align" >M</p>
                                <div class="icon ">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateSalePriceM" value="{{ number_format($species->species_crate->quantity_males * $species->species_crate->sale_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p class="align" >F</p>
                                <div class="icon">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateSalePriceF" value="{{ number_format($species->species_crate->quantity_females * $species->species_crate->sale_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p>U</p>
                                <div class="icon">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateSalePriceU" value="{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->sale_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p >P</p>
                                <div class="icon">
                                    <input type="text" disabled class="input-group input-group-sm bordered" name="crateSalePriceP" value="{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->sale_price, 2, '.', '') }}">
                                </div>
                            </div>
                            <div class="colFlex">
                                <p>Sales total</p>
                                <div class="icon">
                                    <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                    <input class="bordered current" name="cost total 1" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->species_crate->total_sale_price), 2, '.', '') : number_format(($species->species_crate->total_sale_price_usd * $offer->currency_rate_eur), 2, '.', '')}}"/>
                                </div>
                                <div class="icon mt-1 mb-2">
                                    <i class="fas fa-dollar-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                                    <p class="m-0"><em>{{ number_format($species->species_crate->total_sale_price_usd, 2, '.', '') }}</em></p>
                                </div>
                            </div>
                        </div>
                    </td>
                @endif
                <td style="width: 7%;">
                    <div class="colFlex mt-1 margin">
                        <p class="align">
                            Profit
                            <div class="icon reset-value" data-id="{{ $species->species_crate->id }}" style="margin: -37px 0 7px 50px; cursor:pointer;" >
                                <i class="fas fa-reply fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                            </div>
                        </p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered current" name="species 1 profit" type="text" value="{{!empty($species->oursurplus) && $species->oursurplus->sale_currency === "EUR" ? number_format(($species->species_crate->total_sale_price - $species->species_crate->total_cost_price), 2, '.', '') : number_format((($species->species_crate->total_sale_price_usd - $species->species_crate->total_cost_price_usd) * $offer->currency_rate_eur), 2, '.', '')}}"/>
                        </div>
                        <div class="icon mt-1 mb-2">
                            <i class="fas fa-dollar-sign fa-sm fa-fw  text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format(($species->species_crate->total_sale_price_usd - $species->species_crate->total_cost_price_usd), 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        <tr class="blue">
            <th class="none p-2">
                <div class="d-flex footer ">
                    <div class="colFlex" style="align-items: flex-start;">
                        <div class="icon m-0">
                            <i class="fas fa-dice-d6 fa-sm fa-1x fa-fw  m-1 text-black-400" aria-hidden="true"></i>
                            <p class="white m-0 ">CRATE TOTALS</p><br>
                        </div>
                        <div class="icon mt-1">
                            <div class="colFlex">
                                <div class="icon m-0 mr-2">
                                    <i class="fas fa-weight-hanging text-black-400" aria-hidden="true" style="margin: 0 7px 0 5px;"></i>
                                    <p class="white m-0 " >{{ number_format($offer->offerTotalVolKg, 2, '.', '') }} Kg</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <th class="none pt-2 pb-2">
                    <div class="d-flex move ">
                        <p class="white thin  m-0 mr-1">Crate costs total:</p>
                        <div class="colFlex">
                            <div class="icon m-0 mr-2">
                                <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                                <p class="white m-0 " >{{ number_format(($offer->offerTotalCratesCostPrice), 2, '.', '') }}</p>
                            </div>

                            <div class="icon mt-1">
                                <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                                <p class="m-0 "><em>{{ number_format($offer->offerTotalCratesCostPriceUSD, 2, '.', '') }}</em></p>
                            </div>

                        </div>
                    </div>
                </th>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <th class="none pt-2 pb-2">
                    <div class="d-flex move ">
                        <p class="white thin  m-0 mr-1" >Crate sales total: </p>
                        <div class="colFlex">
                            <div class="icon m-0 mr-2">
                                <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                                <p class="white m-0 " >{{ number_format(($offer->offerTotalCratesSalePrice), 2, '.', '') }}</p>
                            </div>
                            <div class="icon mt-1">
                                <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                                <p class="m-0 "><em>{{ number_format($offer->offerTotalCratesSalePriceUSD, 2, '.', '') }}</em></p>
                            </div>
                        </div>
                    </div>
                </th>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <th class="none pt-2 pb-2">
                    <div class="icon move mr-2 mt-0">
                        <i class="fas fa-euro-sign fa-sm size fa-fw mr-2 text-black-400" aria-hidden="true"></i>
                        <p class="white m-0" >{{ number_format(($offer->offerTotalCratesSalePrice - $offer->offerTotalCratesCostPrice), 2, '.', '') }}</p>
                    </div>
                    <div class="icon move mt-1">
                        <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                        <p class="m-0 "><em>{{ number_format(($offer->offerTotalCratesSalePriceUSD - $offer->offerTotalCratesCostPriceUSD), 2, '.', '') }}</em></p>
                    </div>
                </th>
            @endif
        </tr>
        </tbody>
    </table>
</div>

