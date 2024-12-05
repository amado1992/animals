@include('components.errorlist')

<div class="row mb-3">
    <div class="col-md-8 ">
        {!! Form::label('name', 'Name *') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('type', 'Type *') !!}
        {!! Form::select('type', ['Standard' => 'Standard', 'Estimation' => 'Estimation'], null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-3">
  <div class="col-md-4">
    {!! Form::label('length', 'Length *') !!}
    {!! Form::number('length', null, ['class' => 'form-control', 'required']) !!}
  </div>
  <div class="col-md-4">
    {!! Form::label('wide', 'Wide *') !!}
    {!! Form::number('wide', null, ['class' => 'form-control', 'required']) !!}
  </div>
  <div class="col-md-4">
    {!! Form::label('height', 'Height *') !!}
    {!! Form::number('height', null, ['class' => 'form-control', 'required']) !!}
  </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        {!! Form::label('currency', 'Currency *') !!}
        {!! Form::select('currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('cost_price', 'Cost price *') !!}
        {!! Form::number('cost_price', null, ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('sale_price', 'Sale price *') !!}
        {!! Form::number('sale_price', null, ['class' => 'form-control', 'required']) !!}
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        {!! Form::label('animal_quantity', 'Animal quantity *') !!}
        {!! Form::number('animal_quantity', null, ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('weight', 'Vol.weight *') !!}
        {!! Form::number('weight', null, ['class' => 'form-control', 'maxlength' => 50, 'required', 'step' => 'any']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('iata_code', 'IATA Code *') !!}
        {!! Form::number('iata_code', null, ['class' => 'form-control', 'maxlength' => 5, 'required']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ $cancelLink }}" class="btn btn-link" type="button">Cancel</a>

@section('page-scripts')

<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#length, #wide, #height').on("change", function(){
        var length = $('#length').val();
        var wide = $('#wide').val();
        var height = $('#height').val();
        var volk = (length * wide * height) / 6000;
        $("#weight").val(Math.round(volk));
    });

    var form = $('#crateForm');
    original = form.serialize();

    form.submit(function() {
        window.onbeforeunload = null
    })

    window.onbeforeunload = function() {
        if (form.serialize() != original)
            return 'Are you sure you want to leave?'
    }

    $("#cost_price").on("change", function(){
        var buttonValue = $(this).html();
        $(this).html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
        var cost = Number($(this).val());
        var percent_sale = Number(cost * 0.12);
        cost = cost + percent_sale;
        $("#sale_price").val(cost.toFixed(2));
        $(this).html(buttonValue);
    });

</script>

@endsection
