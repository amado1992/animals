@include('components.errorlist')

<div class="mb-3">
    {!! Form::label('scientific_name', 'Scientific name *') !!}
    {!! Form::text('scientific_name', null, ['class' => 'form-control']) !!}
</div>
<div class="mb-3">
    {!! Form::label('scientific_name_alt', 'Scientific name alternative') !!}
    {!! Form::text('scientific_name_alt', null, ['class' => 'form-control']) !!}
</div>
<div class="mb-3">
    {!! Form::label('common_name', 'Common name *') !!}
    {!! Form::text('common_name', null, ['class' => 'form-control']) !!}
</div>
<div class="mb-3">
    {!! Form::label('common_name_alt', 'Common name alternative') !!}
    {!! Form::text('common_name_alt', null, ['class' => 'form-control']) !!}
</div>
<div class="mb-3">
    {!! Form::label('spanish_name', 'Spanish name') !!}
    {!! Form::text('spanish_name', null, ['class' => 'form-control']) !!}
</div>
<div class="mb-3">
    {!! Form::label('chinese_name', 'Chinese name') !!}
    {!! Form::text('chinese_name', null, ['class' => 'form-control']) !!}
</div>
<div class="mb-3">
    {!! Form::label('class_id', 'Class *') !!}
    {!! Form::select('class_id', $classes, (isset($animal) && !empty($animal->classification->class)) ? $animal->classification->class->id : null, ['id' => 'class_id', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>
<div class="mb-3">
    {!! Form::label('order_id', 'Order *') !!}
    {!! Form::select('order_id', (isset($animal) && !empty($orders)) ? $orders : array(), (isset($animal) && !empty($animal->classification->order)) ? $animal->classification->order->id : null, ['id' => 'order_id', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>
<div class="mb-3">
    {!! Form::label('family_id', 'Family *') !!}
    {!! Form::select('family_id', (isset($animal) && !empty($families)) ? $families : array(), (isset($animal) && !empty($animal->classification->family)) ? $animal->classification->family->id : null, ['id' => 'family_id', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>
<div class="mb-3">
    {!! Form::label('genus_id', 'Genus *') !!}
    {!! Form::select('genus_id', (isset($animal) && !empty($genuses)) ? $genuses : array(), (isset($animal) && !empty($animal->classification)) ? $animal->classification->id : null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>
<div class="mb-3">
    {!! Form::label('iata_code', 'IATA code *') !!}
    {!! Form::text('iata_code', null, ['class' => 'form-control']) !!}
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        {!! Form::label('code_number', 'Code number *') !!}
        <span class="spinner-border spinner-border-sm spinner-number d-none" role="status"></span>
    </div>
    <div class="col-md-7 mb-3">
        {!! Form::text('code_number_temp', (isset($animal)) ? $animal->code_number_temp : '000000000000', ['id' => 'code_number_temp', 'class' => 'form-control', 'readonly']) !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! Form::text('code_number', (isset($animal)) ? $animal->code_number : '0000', ['id' => 'code_number', 'class' => 'form-control']) !!}
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-4">
        {!! Form::label('cites_global_key', 'Cites global') !!}
        {!! Form::select('cites_global_key', $cites_global, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('cites_europe_key', 'Cites europe') !!}
        {!! Form::select('cites_europe_key', $cites_europe, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('body_weight', 'Body weight') !!}
            {!! Form::number('body_weight', null, ['class' => 'form-control']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if (isset($animal))
    <a href="{{ route('animals.show', $animal) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('animals.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var form = $('#animalForm');
    original = form.serialize();

    form.submit(function() {
        window.onbeforeunload = null
    })

    window.onbeforeunload = function() {
        if (form.serialize() != original)
            return 'Are you sure you want to leave?'
    }

    $("#class_id").change(function() {
        var value = $(this).val();
        $(".spinner-number").removeClass('d-none');

        $("#code_number_temp").val('');

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getOrdersByClass') }}",
            data:{value: value},
            success:function(data) {
                $(".spinner-number").addClass('d-none');
                $("#code_number_temp").val(data.code_number_temp);

                $("#order_id").empty();
                $('#order_id').append('<option value="">- select -</option>');
                $.each(data.orders, function(key, value) {
                    $('#order_id').append('<option value="'+ value +'">'+ key +'</option>');
                });

                $("#family_id").empty();
                $('#family_id').append('<option value="">- select -</option>');

                $("#genus_id").empty();
                $('#genus_id').append('<option value="">- select -</option>');
            }
        });
    });

    $("#order_id").change(function() {
        var value = $(this).val();
        $(".spinner-number").removeClass('d-none');

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getFamiliesByOrder') }}",
            data:{value: value},
            success:function(data) {
                $(".spinner-number").addClass('d-none');
                $("#code_number_temp").val(data.code_number_temp);

                $("#family_id").empty();
                $('#family_id').append('<option value="">- select -</option>');
                $.each(data.families, function(key, value) {
                    $('#family_id').append('<option value="'+ value +'">'+ key +'</option>');
                });

                $("#genus_id").empty();
                $('#genus_id').append('<option value="">- select -</option>');
            }
        });
    });

    $("#family_id").change(function() {
        var value = $(this).val();
        $(".spinner-number").removeClass('d-none');

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getGenusByFamily') }}",
            data:{value: value},
            success:function(data) {
                $(".spinner-number").addClass('d-none');
                $("#code_number_temp").val(data.code_number_temp);

                $("#genus_id").empty();
                $('#genus_id').append('<option value="">- select -</option>');
                $.each(data.genuss, function(key, value) {
                    $('#genus_id').append('<option value="'+ value +'">'+ key +'</option>');
                });
            }
        });
    });

    $("#genus_id").change(function() {
        var value = $(this).val();
        $(".spinner-number").removeClass('d-none');

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getGenusCode') }}",
            data:{value: value},
            success:function(data){
                $("#code_number_temp").val(data.code_number_temp);
                var number = $("#code_number_temp").val();
                $.ajax({
                    type:'POST',
                    url:"{{ route('animals.verifiCodeNumber') }}",
                    data:{value: number},
                    success:function(data){
                        $(".spinner-number").addClass('d-none');
                        if(data.error){
                            $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        }else{
                            $("#code_number").val(data.number);
                        }
                    }
                });
            }
        });

    });



</script>

@endsection
