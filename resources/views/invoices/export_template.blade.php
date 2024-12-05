{{-- <table>
    <thead>
      <tr>
          <th>Order No.</th>
          <th>Order status</th>
          <th>Invoice No.</th>
          <th>Invoice type</th>
          <th>Invoice date</th>
          <th>Contact</th>
          <th>Bank account</th>
          <th>Invoice amount</th>
          <th>Received</th>
          <th>Received in USD</th>
          <th>Paid on</th>
      </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ ($invoice->order != null) ? $invoice->order->full_number : '' }}</td>
            <td>{{ ($invoice->order != null) ? $invoice->order->order_status : '' }}</td>
            <td>{{ $invoice->full_number }}</td>
            <td>{{ $invoice->invoice_type }}</td>
            <td>{{ date('Y-m-d', strtotime($invoice->invoice_date)) }}</td>
            <td>{{ ($invoice->contact != null) ? $invoice->contact->email : 'No contact.' }}</td>
            <td>{{ $invoice->bank_account->name }}</td>
            <td>{{ $invoice->invoice_amount }}</td>
            <td>{{ $invoice->paid_value }}</td>
            <td>{{ $invoice->paid_value_usd }}</td>
            <td>{{ date('Y-m-d', strtotime($invoice->paid_date)) }}</td>
        </tr>
        @endforeach
        <tr><td colspan="11">&nbsp;</td></tr>
        <tr>
            <td colspan="9" style="text-align: right; font-weight: bold;">TOTAL:</td>
            <td style="font-weight: bold;">{{ $total_paid_value_usd }}</td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
</table> --}}
<table>
    <thead>
        <tr>
            <th>Invoice No.</th>
            <th>Sent / received on</th>
            <th>Invoice type</th>
            <th>Client / Provider</th>
            <th>Description goods</th>
            <th>Bank account</th>
            <th>Payment type</th>
            <th>Invoice amount</th>
            <th>Received / Paid</th>
            <th>Payment date</th>
            <th>Total order sales</th>
            <th>Total order costs</th>
            <th>Order no.</th>
            <th>Order status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoices as $invoice)
            <tr>
                <td>{{ $invoice->full_number }}</td>
                <td>{{ date('d/m/Y', strtotime($invoice->paid_date)) }}</td>
                <td>{{ $invoice->invoice_type }}</td>
                <td>{{ $invoice->institution->name }} - {{ $invoice->institution->country->name }}</td>
                <td>{{ $invoice->animals[0]->common_name }} ({{count($invoice->animals) }})</td>
                <td>{{ $invoice->bank_account->name }}-{{$invoice->bank_account->currency }}</td>
                <td>{{ $invoice->payment_type }}</td>
                <td>{{ $invoice->invoice_amount }}</td>
                <td>{{ $invoice->paid_value }}</td>
                <td>{{ date('d/m/Y', strtotime($invoice->paid_data) ) }}</td>
                <td>{{ number_format($invoice->offer_totals['offerTotalSalePrice'], 2) }}</td>
                <td>{{ number_format($invoice->offer_totals['offerTotalCostPrice'], 2) }}</td>
                <td>{{ $invoice->order->full_number }}</td>
                <td>{{ $invoice->order->order_status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
