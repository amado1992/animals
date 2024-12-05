@include('components.errorlist')

{!! Form::hidden('offer_or_species_id', (isset($offerOrSpecies) && $offerOrSpecies != null) ? $offerOrSpecies->id : null, ['class' => 'form-control']) !!}
{!! Form::hidden('offer_airfreight_type', (isset($offerAirfreightType) && $offerAirfreightType != null) ? $offerAirfreightType : null, ['class' => 'form-control']) !!}

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('source', 'Source *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('source', ['offer' => 'Offer', 'estimation' => 'Estimation'], null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('offered_date', 'Offered date', ['class' => 'font-weight-bold']) !!}
        {!! Form::date('offered_date', '', ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('transport_agent', 'Transport agent', ['class' => 'font-weight-bold']) !!}
        @if ( isset($airfreight) )
            <br>{!! Form::label('airfreight_agent', (isset($airfreight) && $airfreight->agent != null) ? $airfreight->agent->email : 'No agent selected.', ['class' => 'text-danger']) !!}
        @endif
        <select class="contact-select2 form-control" type="default" style="width: 100%" name="transport_agent">
            @if( isset($airfreight) && $airfreight->transport_agent )
                <option value="{{ $airfreight->transport_agent }}" selected>{{ $airfreight->agent->email }}</option>
            @endif
        </select>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('remarks', 'Description animals *', ['class' => 'font-weight-bold']) !!}
        {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => 2, 'required']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-4">
        {!! Form::label('departure_continent', 'Departure continent *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('departure_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('arrival_continent', 'Arrival continent *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('arrival_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('type', 'Select option: ', ['class' => 'font-weight-bold']) !!}
    </div>
    @if (isset($airfreight) && $airfreight->type != null)
        <div class="col-md-3">
            {!! Form::radio('type', 'volKg', ($airfreight->type === 'volKg') ? true : false) !!}
            {!! Form::label('per_volKg', 'Per vol.kg') !!}
        </div>
        <div class="col-md-3">
            {!! Form::radio('type', 'lowerdeck', ($airfreight->type === 'lowerdeck') ? true : false) !!}
            {!! Form::label('lowerdeck_pallet', 'Lowerdeck pallet') !!}
        </div>
        <div class="col-md-3">
            {!! Form::radio('type', 'maindeck', ($airfreight->type === 'maindeck') ? true : false) !!}
            {!! Form::label('maindeck_pallet', 'Maindeck pallet') !!}
        </div>
    @else
        <div class="col-md-3">
            {!! Form::radio('type', 'volKg', true) !!}
            {!! Form::label('per_volKg', 'Per vol.kg') !!}
        </div>
        <div class="col-md-3">
            {!! Form::radio('type', 'lowerdeck') !!}
            {!! Form::label('lowerdeck_pallet', 'Lowerdeck pallet') !!}
        </div>
        <div class="col-md-3">
            {!! Form::radio('type', 'maindeck') !!}
            {!! Form::label('maindeck_pallet', 'Maindeck pallet') !!}
        </div>
    @endif
</div>

<div class="row mb-2">
    <div class="col-md-3"></div>
    <div class="col-md-3">
        {!! Form::label('volKg_weight_value', 'Total vol.kg') !!}
        {!! Form::text('volKg_weight_value', (isset($airfreight)) ? $airfreight->volKg_weight_value : 0, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('lowerdeck_pallet', 'Total lowerdeck pallet') !!}
        {!! Form::text('lowerdeck_value', (isset($airfreight)) ? $airfreight->lowerdeck_value : 0, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('lowerdeck_pallet', 'Total maindeck pallet') !!}
        {!! Form::text('maindeck_value', (isset($airfreight)) ? $airfreight->maindeck_value : 0, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        {!! Form::label('currency', 'Currency') !!}
        {!! Form::select('currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('price_per_volKg', ' Price per vol.kg') !!}
        {!! Form::text('volKg_weight_cost', (isset($airfreight)) ? $airfreight->volKg_weight_cost : 0, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('price_lowerdeck_pallet', 'Price lowerdeck pallet') !!}
        {!! Form::text('lowerdeck_cost', (isset($airfreight)) ? $airfreight->lowerdeck_cost : 0, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('price_maindeck_pallet', 'Price maindeck pallet') !!}
        {!! Form::text('maindeck_cost', (isset($airfreight)) ? $airfreight->maindeck_cost : 0, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('standard_flight', 'Standard Flight: ',['class' => 'font-weight-bold mr-2']) !!}
        {!! Form::checkbox('standard_flight', null, !empty($airfreight) ? $airfreight->standard_flight : 1) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>

@if (isset($offerOrSpecies) && $offerOrSpecies != null)
    @switch($offerAirfreightType)
        @case('offer_volKg')
            <a href="{{ route('offers.show', $offerOrSpecies->offer_id) }}" class="btn btn-link" type="button">Cancel</a>
            @break
        @case('offer_pallet')
            <a href="{{ route('offers.show', $offerOrSpecies->id) }}" class="btn btn-link" type="button">Cancel</a>
            @break
        @case('order_volKg')
            <a href="{{ route('orders.show', $offerOrSpecies->offer->order->id) }}" class="btn btn-link" type="button">Cancel</a>
            @break
        @case('order_pallet')
            <a href="{{ route('orders.show', $offerOrSpecies->order->id) }}" class="btn btn-link" type="button">Cancel</a>
            @break
    @endswitch
@else
    <a href="{{ route('airfreights.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#airfreightForm');
        original = form.serialize();

        form.submit(function() {
            window.onbeforeunload = null;
        })

        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        $('input[name=type]').trigger('change');
    });

    $('input[name=type]').change(function() {
        var checkedOption = $('input[name=type]:checked').val();

        if (checkedOption == 'lowerdeck') {
            $('input[name=volKg_weight_cost]').val(0);
            $('input[name=maindeck_cost]').val(0);
            $('input[name=volKg_weight_cost]').prop('disabled', true);
            $('input[name=lowerdeck_cost]').prop('disabled', false);
            $('input[name=maindeck_cost]').prop('disabled', true);

            $('input[name=volKg_weight_value]').val(0);
            $('input[name=maindeck_value]').val(0);
            $('input[name=volKg_weight_value]').prop('disabled', true);
            $('input[name=lowerdeck_value]').prop('disabled', false);
            $('input[name=maindeck_value]').prop('disabled', true);
        }
        else if (checkedOption == 'maindeck') {
            $('input[name=volKg_weight_cost]').val(0);
            $('input[name=lowerdeck_cost]').val(0);
            $('input[name=volKg_weight_cost]').prop('disabled', true);
            $('input[name=lowerdeck_cost]').prop('disabled', true);
            $('input[name=maindeck_cost]').prop('disabled', false);

            $('input[name=volKg_weight_value]').val(0);
            $('input[name=lowerdeck_value]').val(0);
            $('input[name=volKg_weight_value]').prop('disabled', true);
            $('input[name=lowerdeck_value]').prop('disabled', true);
            $('input[name=maindeck_value]').prop('disabled', false);
        }
        else {
            $('input[name=lowerdeck_cost]').val(0);
            $('input[name=maindeck_cost]').val(0);
            $('input[name=volKg_weight_cost]').prop('disabled', false);
            $('input[name=lowerdeck_cost]').prop('disabled', true);
            $('input[name=maindeck_cost]').prop('disabled', true);

            $('input[name=lowerdeck_value]').val(0);
            $('input[name=maindeck_value]').val(0);
            $('input[name=volKg_weight_value]').prop('disabled', false);
            $('input[name=lowerdeck_value]').prop('disabled', true);
            $('input[name=maindeck_value]').prop('disabled', true);
        }
    });

</script>

@endsection
