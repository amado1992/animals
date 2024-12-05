@if ($offer->extraFeeValue > 0)
    <div class="font-weight-bold text-center text-danger">
        <label><input type="checkbox" id="setExtraFee" name="setExtraFee" offerId="{{ $offer->offerId }}" @if($offer->extra_fee) checked @endif> For orders less than Euro 5000,00; a fee of Euro 750,00 will be charged as administration costs.</label>
    </div>
@endif
<table class="table " id="dataTable" width="100%" cellspacing="0">
    <thead>
    <tr class="header">
        <th class="none"></th>
        <th class="none"></th>
        <th class="none"></th>
        <th class="none"></th>
    </tr>
    </thead>
    <tbody>
    <tr class="blueborder">
        <th class="gray"></th>
        <td ></td>
        <td class="gray"></td>
        <td></td>
    </tr>
    <tr class="blue">
        <th class="none p-2" style="width: 50% !important;">
            <div class="d-flex move ">
                <div class="colFlex">
                    <div class="icon m-0 mr-2">
                        <p class="white m-0">TOTAL</p>
                    </div>
                    <div class="icon mt-1">
                        <p class="white m-0">Percent of the profit based on the total cost price: </p>
                    </div>
                </div>
            </div>
        </th>
        @if (Auth::user()->hasPermission('offers.see-cost-prices'))
            <th class="none pt-2 pb-2">
                <div class="d-flex move " style="width: 85% !important;">
                    <div class="colFlex">
                        <div class="icon m-0 mr-2">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                            <p class="white m-0 " >{{ number_format($offer->offerTotalCostPrice, 2, '.', '') }}</p>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0 "><em>{{ number_format($offer->offerTotalCostPriceUSD, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </th>
        @endif
        @if (Auth::user()->hasPermission('offers.see-sale-prices'))
            <th class="none pt-2 pb-2">
                <div class="d-flex move " style="width: 161% !important;">
                    <div class="colFlex">
                        <div class="icon m-0 mr-2">
                            <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-white-400" aria-hidden="true"></i>
                            <p class="white m-0 " >{{ number_format($offer->offerTotalSalePrice, 2, '.', '') }}</p>
                        </div>
                        <div class="icon mt-1">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                            <p class="m-0 "><em>{{ number_format($offer->offerTotalSalePriceUSD, 2, '.', '') }}</em></p>
                        </div>
                    </div>
                </div>
            </th>
        @endif
        @if (Auth::user()->hasPermission('offers.see-profit-value'))
            <th class="none pt-2 pb-2">
                <div class="icon move mr-2 mt-0">
                    <i class="fas fa-euro-sign fa-sm size fa-fw mr-2 text-black-400" aria-hidden="true"></i>
                    <p class="white m-0" >{{ number_format($offer->total_profit, 2, '.', '') }}</p>
                </div>
                <div class="icon move mt-1">
                    <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                    <p class="m-0 "><em>{{ $offer->total_profitUSD }}</em></p>
                </div>
                <div class="icon move mt-1">
                    <i class="fas fa-dollar-sign fa-sm fa-fw text-black-400" aria-hidden="true"></i>
                    <p class="m-0 "><em>{{!empty($offer->offer_currency) && $offer->offer_currency === "EUR" ? number_format(($offer->percent_profit), 2, '.', '') : number_format(($offer->percent_profitUSD), 2, '.', '')}} %</em></p>
                </div>
            </th>
        @endif
    </tr>
    </tbody>
</table>
