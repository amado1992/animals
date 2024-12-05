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
                @if($status == "new")
                    <a href="{{url('/')}}/orders/resetListEmailNewOrderSend">Reset List</a>
                @endif
                @if($status == "realizaed")
                    <a href="{{url('/')}}/orders/resetListEmailRealizedOrderSend">Reset List</a>
                @endif
                <table style="font-size: 11px;" border="0">
                    <thead>
                        <tr>
                            <th class="border-top-0" colspan="3"></th>
                            <th class="border-top-0 text-center border-left-total" colspan="3">Costs</th>
                            <th class="border-top-0 text-center border-left-total" colspan="3">Sales</th>
                            <th class="border-top-0 text-center border-left-total" colspan="3">Profit</th>
                        </tr>
                        <tr>
                            <th class="border-top-0" style="width: 5%;">Order No</th>
                            <th class="border-top-0" style="width: 15%;">Client</th>
                            <th class="border-top-0" style="width: 15%;">Supplier</th>
                            <th class="border-top-0 border-left-total" style="width: 7%;">Orig currency</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">USD Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 7%;">Orig currency</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">USD Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 7%;">Orig currency</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">USD Amount</th>
                            <td style="font-weight: bold; margin-bottom: 10px;">
                                Url
                            </td>
                        </tr>
                    </thead>
                    @foreach ($orders as $order)
                        <tr>
                            <td>
                                {{ $order->full_number }}/{{ $order->offer->offer_number }}
                            </td>
                            <td>
                                @if ($order->client)
                                    {{ ($order->client->organisation && $order->client->organisation->name) ? $order->client->organisation->name : $order->client->full_name }}<br>
                                    <a href="mailto:{{ $order->client->email }}"><u>{{ $order->client->email }}</u></a><br>
                                    {{ $order->client->country->name }}
                                @else
                                    no information
                                @endif
                            </td>
                            <td>
                                @if ($order->supplier)
                                    {{ ($order->supplier->organisation && $order->supplier->organisation->name) ? $order->supplier->organisation->name : $order->supplier->full_name }}<br>
                                    <a href="mailto:{{ $order->supplier->email }}"><u>{{ $order->supplier->email }}</u></a><br>
                                    {{ $order->supplier->country->name }}
                                @else
                                    International Zoo Services
                                @endif
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency === "USD" ? number_format(($order->offer->offerTotalCostPriceUSD), 2, '.', '') : number_format(($order->offer->offerTotalCostPrice), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ number_format(($order->offer->offerTotalCostPriceUSD), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency === "USD" ? number_format(($order->offer->offerTotalSalePriceUSD), 2, '.', '') : number_format(($order->offer->offerTotalSalePrice), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ number_format(($order->offer->offerTotalSalePriceUSD), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency === "USD" ? number_format(($order->offer->offerTotalSalePriceUSD - $order->offer->offerTotalCostPriceUSD), 2, '.', '') : number_format(($order->offer->offerTotalSalePrice - $order->offer->offerTotalCostPrice), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ number_format(($order->offer->offerTotalSalePriceUSD - $order->offer->offerTotalCostPriceUSD), 2, '.', '') }}
                            </td>
                            <td>
                                <a href="{{env("APP_URL")}}/orders/{{$order->id}}">{{env("APP_URL")}}/orders/{{$order->id}}</a>
                            </td>
                        <tr>
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
