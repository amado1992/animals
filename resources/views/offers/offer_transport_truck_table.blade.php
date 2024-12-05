<tr class='blueborder'>
@if(!empty($offer->transport_truck->id))
    @if(!empty($offer->offer_currency) && $offer->offer_currency === "EUR")
        @php $sale_currency = "fa-euro-sign"; @endphp
    @elseif(!empty($offer->offer_currency) && $offer->offer_currency === "USD")
        @php $sale_currency = "fa-dollar-sign"; @endphp
    @else
        @php $sale_currency = "fa-euro-sign"; @endphp
    @endif
    <tr offerTransportTruckId="{{$offer->transport_truck->id}}">
        <th class="gray width">
            <div class="d-flex ml-2 mt-1">
                <div class="colFlex" style="width: 72px">
                     <p style="font-weight: normal;margin:7px 5px 0">Cost&nbsp;status</p>
                     @include('offers.costs_status', ['id' => 'tt' .$offer->transport_truck->id, 'table' => 'offers_transport_truck', 'cost_id' => $offer->transport_truck->id, 'selected' => $offer->status])
                </div>
                <div class="d-flex gap" style="margin:8px 0 0 20px;">
                   <p class="align ">Total Km <input type="text" class="input-group input-group-sm bordered bordered2" name="total_km" value="{{ number_format($offer->transport_truck->total_km, 2, '.', '') }}" oldValue="{{ number_format($offer->transport_truck->total_km, 2, '.', '') }}"></p>
                </div>
                <div class="d-flex ml-2 mt-1" >
                    <div class="d-flex travel mt-2">
                        <div >
                            <p class="species m-0">FROM</p>
                            <p class="name m-0 mt-1 country">{{$offer->transport_truck->origin_country->name}}</p>
                        </div>
                        <div class="">
                            <p class="species m-0">TO</p>
                            <p class="name m-0 mt-1 country">{{$offer->transport_truck->delivery_country->name}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </th>
        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
            <td >
                <div class="d-flex mt-2 self ">
                    <div class="colFlex mt-1">
                        <p class="mr-2 reduce "  >Price per Km</p>
                        <div class="icon ">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 mt-1 text-black-400 " aria-hidden="true"></i>
                            <input type="text" class="input-group input-group-sm bordered bordered2" name="cost_rate_per_km" value="{{ number_format($offer->transport_truck->cost_rate_per_km, 2, '.', '') }}" oldValue="{{ number_format($offer->transport_truck->cost_rate_per_km, 2, '.', '') }}">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw ml-1 mt-1 text-black-400 " aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="colFlex ">
                        <p class="ml-2" >Total</p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 mt-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered bordered2 current" name="bc sales total 2" type="text" value="{{ number_format($offer->offerTotalTransportTruckCostPrice * $general_rate_eur, 2, '.', '') }}"/>
                        </div>
                        <div class="icon mt-1 mb-2">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format($offer->offerTotalTransportTruckCostPrice * $general_rate_usd, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </td>
        @endif
        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
            <td class="gray">
                <div class="d-flex mt-2 self ">
                    <div class="colFlex mt-1">
                        <p class=" mr-2 reduce pb-2 mb-2"  >Price per Km</p>
                        <div class="icon ">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 mt-1 text-black-400 " aria-hidden="true"></i>
                            <input type="text" class="input-group input-group-sm bordered bordered2" name="sale_rate_per_km" value="{{ number_format($offer->transport_truck->sale_rate_per_km, 2, '.', '') }}" oldValue="{{ number_format($offer->transport_truck->sale_rate_per_km, 2, '.', '') }}">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw ml-1 mt-1 text-black-400 " aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="colFlex ">
                        <p class="ml-2" >Total</p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 mt-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered bordered2 current" name="bc sales total 2" type="text" value="{{ number_format($offer->offerTotalTransportTruckSalePrice * $general_rate_eur, 2, '.', '') }}"/>
                        </div>
                        <div class="icon mt-1 mb-2">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format($offer->offerTotalTransportTruckSalePrice * $general_rate_usd, 2, '.', '') }}</em></p>
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
                        <input class="bordered current" name="species 1 profit" type="text" value="{{ number_format(($offer->offerTotalTransportTruckSalePrice - $offer->offerTotalTransportTruckCostPrice) * $general_rate_eur, 2, '.', '') }}"/>
                    </div>
                    <div class="icon mt-1 mb-2">
                        <i class="fas fa-dollar-sign fa-sm fa-fw  text-black-400" aria-hidden="true"></i>
                        <p class="m-0"><em>{{ number_format(($offer->offerTotalTransportTruckSalePrice - $offer->offerTotalTransportTruckCostPrice) * $general_rate_usd, 2, '.', '') }}</em></p>
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
                            <p class="white m-0 " >{{ number_format($offer->offerTotalTransportTruckCostPrice, 2, '.', '') }}</p>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0 "><em>{{ number_format($offer->offerTotalTransportTruckCostPrice * $general_rate_usd, 2, '.', '') }}</em></p>
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
                            <p class="white m-0 " >{{ number_format($offer->offerTotalTransportTruckSalePrice, 2, '.', '') }}</p>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0 "><em>{{ number_format($offer->offerTotalTransportTruckSalePrice * $general_rate_usd, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </th>
        @endif
        <th class="none pt-2 pb-2">
            <div class="icon move mr-2 mt-0">
                <i class="fas fa-euro-sign fa-sm size fa-fw mr-2 text-black-400" aria-hidden="true"></i>
                <p class="white m-0" >{{ number_format(($offer->offerTotalTransportTruckSalePrice - $offer->offerTotalTransportTruckCostPrice), 2, '.', '') }}</p>
            </div>
            <div class="icon move mt-1">
                <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                <p class="m-0 "><em>{{ number_format(($offer->offerTotalTransportTruckSalePrice - $offer->offerTotalTransportTruckCostPrice) * $general_rate_usd, 2, '.', '') }}</em></p>
            </div>

        </th>
    </tr>
@endif