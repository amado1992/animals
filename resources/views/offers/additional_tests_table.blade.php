@foreach($offer->offerAdditionalTests as $at)
    @if(!empty($at->currency) && $at->currency === "EUR")
        @php $sale_currency = "fa-euro-sign"; @endphp
    @elseif(!empty($at->currency) && $at->currency === "USD")
        @php $sale_currency = "fa-dollar-sign"; @endphp
    @else
        @php $sale_currency = "fa-euro-sign"; @endphp
    @endif
    <tr class='offerBasicTestRow' idAdditionalCost="{{ $at->id }}">
        <th class="gray">
            <div class="ml-3">
                <div class="d-flex mb-3" style="margin: 16px 0 0 0px;">
                    <input class="mr-1 ml-2 mt-2 selectorTest" name="species 1 checkbox" type="checkbox" value="{{ $at->id }}" style="margin: 0 0 -6px 0;"/>
                    <p class="m-0 ml-1 species">QUANTITY<input type="text" class="input-group input-group-sm block bordered area " name="quantity" value="{{ $at->quantity }}" oldValue="{{ $at->quantity }}"></p>
                    <div class="m-0 ml-2" >
                        <p class="name" style="margin: 15px 0 0 0 !important;">{{ $at->name }}</p>
                    </div>
                </div>
            </div>
        </th>
        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
            <td >
                <div class="d-flex mt-2 self ">
                    <div class="colFlex mt-1">
                        <p class="mr-2 reduce "  >COST EACH</p>
                        <div class="icon ">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 mt-1 text-black-400 " aria-hidden="true"></i>
                            <input type="text" class="input-group input-group-sm bordered bordered2" name="costPrice" value="{{ number_format($at->costPrice, 2, '.', '') }}" oldValue="{{ number_format($at->costPrice, 2, '.', '') }}">
                        </div>
                    </div>
                    <div class="colFlex ">
                        <p class="ml-2" >Total</p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 mt-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered bordered2 current" name="bc sales total 2" type="text" value="{{ number_format($at->quantity * $at->costPrice * $general_rate_eur, 2, '.', '') }}"/>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format($at->quantity * $at->costPrice * $general_rate_usd, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </td>
        @endif
        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
            <td class="gray">
                <div class="d-flex mt-2 self ">
                    <div class="colFlex mt-1">
                        <p class=" mr-2 reduce pb-2 mb-2"  >PRICE EACH</p>
                        <div class="icon ">
                            <i class="fas {{$sale_currency}} fa-sm fa-fw mr-1 mt-1 text-black-400 " aria-hidden="true"></i>
                            <input type="text" class="input-group input-group-sm bordered bordered2" name="salePrice" value="{{ number_format($at->salePrice, 2, '.', '') }}" oldValue="{{ number_format($at->salePrice, 2, '.', '') }}">
                        </div>
                    </div>
                    <div class="colFlex ">
                        <p class="ml-2" >Total</p>
                        <div class="icon">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-1 mt-1 text-black-400" aria-hidden="true"></i>
                            <input class="bordered bordered2 current" name="bc sales total 2" type="text" value="{{ number_format($at->quantity * $at->salePrice * $general_rate_eur, 2, '.', '') }}"/>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0"><em>{{ number_format($at->quantity * $at->salePrice * $general_rate_usd, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </td>
        @endif
        @if (Auth::user()->hasPermission('offers.see-profit-value'))
            <td >
                <div class="colFlex mt-2  margin">
                    <p class="align mr-4">Profit</p>
                    <div class="icon">
                        <i class="fas fa-euro-sign fa-sm fa-fw mr-1 text-black-400" aria-hidden="true"></i>
                        <input class="bordered current" name="species 1 profit" type="text" value="{{ number_format(($at->quantity * ($at->salePrice - $at->costPrice)) * $general_rate_eur, 2, '.', '') }}"/>
                    </div>
                    <div class="icon mt-1">
                        <i class="fas fa-dollar-sign fa-sm fa-fw  text-black-400" aria-hidden="true"></i>
                        <p class="m-0"><em>{{ number_format($at->quantity * ($at->salePrice - $at->costPrice) * $general_rate_usd, 2, '.', '') }}</em></p>
                    </div>
                </div>
            </td>
        @endif
    </tr>
@endforeach
<tr class="blue mt-3">
    <th class="none p-2">
        <div class="d-flex footer ">
            <i class="fas fa-syringe fa-sm fa-rotate-270  fa-1x fa-fw   m-1 text-black-400" aria-hidden="true"></i>
            <p class="white m-0 ">TEST TOTALS</p>
        </div>
    </th>
    @if (Auth::user()->hasPermission('offers.see-cost-prices'))
        <th class="none pt-2 pb-2">
            <div class="d-flex move ">
                <p class="white thin  m-0 mr-1">Test costs total:</p>
                <div class="colFlex">
                    <div class="icon m-0 mr-2">
                        <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                        <p class="white m-0 " >{{ number_format($offer->offerAdditionalTestsTotalCost * $general_rate_eur, 2, '.', '') }}</p>
                    </div>
                    <div class="icon mt-1">
                        <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                        <p class="m-0 "><em>{{ number_format($offer->offerAdditionalTestsTotalCostUSD, 2, '.', '') }}</em></p>
                    </div>
                </div>
            </div>
        </th>
    @endif
    @if (Auth::user()->hasPermission('offers.see-sale-prices'))
        <th class="none pt-2 pb-2">
            <div class="d-flex move ">
                <p class="white thin  m-0 mr-1" >Test sales total: </p>
                <div class="colFlex">
                    <div class="icon m-0 mr-2">
                        <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                        <p class="white m-0 " >{{ number_format($offer->offerAdditionalTestsTotalSale * $general_rate_eur, 2, '.', '') }}</p>
                    </div>
                    <div class="icon mt-1">
                        <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                        <p class="m-0 "><em>{{ number_format($offer->offerAdditionalTestsTotalSaleUSD, 2, '.', '') }}</em></p>
                    </div>
                </div>
            </div>
        </th>
    @endif
    @if (Auth::user()->hasPermission('offers.see-profit-value'))
        <th class="none pt-2 pb-2">
            <div class="icon move mr-2 mt-0">
                <i class="fas fa-euro-sign fa-sm size fa-fw mr-2 text-black-400" aria-hidden="true"></i>
                <p class="white m-0" >{{ number_format(($offer->offerAdditionalTestsTotalSale - $offer->offerAdditionalTestsTotalCost) * $general_rate_eur, 2, '.', '') }}</p>
            </div>
            <div class="icon move mt-1">
                <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                <p class="m-0 "><em>{{ number_format(($offer->offerAdditionalTestsTotalSaleUSD - $offer->offerAdditionalTestsTotalCostUSD), 2, '.', '') }}</em></p>
            </div>
        </th>
    @endif
</tr>
