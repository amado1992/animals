<tr class='blueborder'>
@if(!empty($offer->airfreight_pallet->id))
    @if(!empty($offer->offer_currency) && $offer->offer_currency === "EUR")
        @php $sale_currency = "fa-euro-sign"; @endphp
    @elseif(!empty($offer->offer_currency) && $offer->offer_currency === "USD")
        @php $sale_currency = "fa-dollar-sign"; @endphp
    @else
        @php $sale_currency = "fa-euro-sign"; @endphp
    @endif
    <tr offerAirfreightPalletId="{{$offer->airfreight_pallet->id}}">
        <th class="gray width">
            <div class="d-flex">
                <div class="colFlex">
                    <p style="font-weight: normal;margin:4px 0 0">Cost&nbsp;status</p>
                     @include('offers.costs_status', ['id' => 'ap', 'table' => 'offers_airfreight_pallets', 'cost_id' => $offer->airfreight_pallet->id, 'selected' => $offer->airfreight_pallet->status])
                </div>
                <div class="colFlex ml-2 mt-1">
                    <div class="d-flex gap">
                        <p class="align ">Qty <input type="number" class="input-group input-group-sm block bordered area" name="pallet_quantity" value="{{ $offer->airfreight_pallet->pallet_quantity }}" oldValue="{{ $offer->airfreight_pallet->pallet_quantity }}"></p>
                    </div>
                </div>
                <div class="row mr-2 ml-2 mt-1">
                    <div class="col-md-12">
                        <p class="species m-0">
                            FROM:
                            <span class="name country mr-2">{{$offer->airfreight_pallet->from_continent->name}}</span>
                            TO:
                            <span class="name country mr-2">{{$offer->airfreight_pallet->to_continent->name}}</span>
                            Total vol.kg:
                            <span class="name country mr-2">
                                {{ number_format($offer->offerTotalActualWeight, 2, '.', '') }} Kg
                            </span>
                            Total act.kg:
                            <span class="name country mr-2">
                                {{ number_format($offer->offerTotalVolKg, 2, '.', '') }} Kg
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </th>
        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
            <td >
                <div class="d-flex mt-2 self ">
                    <div class="colFlex mt-1">
                        <p class="mr-2 reduce "  >Price per pallet</p>
                        <div class="icon ">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 mt-1 text-black-400 " aria-hidden="true"></i>
                            <input type="text" class="input-group input-group-sm bordered bordered2" name="pallet_cost_value" value="{{ number_format($offer->airfreight_pallet->pallet_cost_value, 2, '.', '') }}" oldValue="{{ number_format($offer->airfreight_pallet->pallet_cost_value, 2, '.', '') }}">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw ml-1 mt-1 text-black-400 " aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="colFlex ">
                        <p class="ml-2" >Costs total</p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 mt-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered bordered2 current" name="bc sales total 2" type="text" value="{{ number_format($offer->offerTotalAirfreightPalletCostPrice * $general_rate_eur, 2, '.', '') }}"/>
                        </div>
                        <div class="icon mt-1 mb-2">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format($offer->offerTotalAirfreightPalletCostPrice * $general_rate_usd, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </td>
        @endif
        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
            <td class="gray">
                <div class="d-flex mt-2 self ">
                    <div class="colFlex mt-1">
                        <p class=" mr-2 reduce pb-2 mb-2"  >Price per pallet</p>
                        <div class="icon ">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 mt-1 text-black-400 " aria-hidden="true"></i>
                            <input type="text" class="input-group input-group-sm bordered bordered2" name="pallet_sale_value" value="{{ number_format($offer->airfreight_pallet->pallet_sale_value, 2, '.', '') }}" oldValue="{{ number_format($offer->airfreight_pallet->pallet_sale_value, 2, '.', '') }}">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw ml-1 mt-1 text-black-400 " aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="colFlex ">
                        <p class="ml-2" >Sales total</p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 mt-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered bordered2 current" name="bc sales total 2" type="text" value="{{ number_format($offer->offerTotalAirfreightPalletSalePrice * $general_rate_eur, 2, '.', '') }}"/>
                        </div>
                        <div class="icon mt-1 mb-2">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format($offer->offerTotalAirfreightPalletSalePrice * $general_rate_usd, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </td>
        @endif
        @if (Auth::user()->hasPermission('offers.see-profit-value'))
            <td>
                <div class="colFlex mt-1 margin">
                    <p class="align mr-4">Profit</p>
                    <div class="icon">
                        <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                        <input class="bordered current" name="species 1 profit" type="text" value="{{ number_format(($offer->offerTotalAirfreightPalletSalePrice - $offer->offerTotalAirfreightPalletCostPrice) * $general_rate_eur, 2, '.', '') }}"/>
                    </div>
                    <div class="icon mt-1 mb-2">
                        <i class="fas fa-dollar-sign fa-sm fa-fw  text-black-400" aria-hidden="true"></i>
                        <p class="m-0"><em>{{ number_format(($offer->offerTotalAirfreightPalletSalePrice - $offer->offerTotalAirfreightPalletCostPrice) * $general_rate_usd, 2, '.', '') }}</em></p>
                    </div>
                </div>
            </td>
        @endif
    </tr>


    <tr class="blue">
        <th class="none p-2">
            <div class="d-flex footer ">
                <i class="fas fa-plane fa-rotate-45 fa-sm fa-1x fa-fw  mr-1 ml-1  text-black-400" aria-hidden="true"></i>
                <p class="white m-0 ">FLIGHT TOTALS</p>
            </div>
        </th>
        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
            <th class="none pt-2 pb-2">
                <div class="d-flex move ">
                    <p class="white thin  m-0 mr-1">Price per pallet costs total:</p>
                    <div class="colFlex">
                        <div class="icon m-0 mr-2">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                            <p class="white m-0 " >{{ number_format($offer->offerTotalAirfreightPalletCostPrice * $general_rate_eur, 2, '.', '') }}</p>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0 "><em>{{ number_format($offer->offerTotalAirfreightPalletCostPrice * $general_rate_usd, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </th>
        @endif
        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
        <th class="none pt-2 pb-2">
            <div class="d-flex move ">
                <p class="white thin  m-0 mr-1" >Price per pallet sales total: </p>
                <div class="colFlex">
                    <div class="icon m-0 mr-2">
                        <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                        <p class="white m-0 " >{{ number_format($offer->offerTotalAirfreightPalletSalePrice * $general_rate_eur, 2, '.', '') }}</p>
                    </div>
                    <div class="icon mt-1">
                        <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                        <p class="m-0 "><em>{{ number_format($offer->offerTotalAirfreightPalletSalePrice * $general_rate_usd, 2, '.', '') }}</em></p>
                    </div>
                </div>
            </div>
        </th>
        @endif
        <th class="none pt-2 pb-2">
            <div class="icon move mr-2 mt-0">
                <i class="fas fa-euro-sign fa-sm size fa-fw mr-2 text-black-400" aria-hidden="true"></i>
                <p class="white m-0" >{{ number_format(($offer->offerTotalAirfreightPalletSalePrice - $offer->offerTotalAirfreightPalletCostPrice) * $general_rate_eur, 2, '.', '') }}</p>
            </div>
            <div class="icon move mt-1">
                <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                <p class="m-0 "><em>{{ number_format(($offer->offerTotalAirfreightPalletSalePrice - $offer->offerTotalAirfreightPalletCostPrice) * $general_rate_usd, 2, '.', '') }}</em></p>
            </div>

        </th>
    </tr>
@endif
