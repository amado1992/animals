@include('components.errorlist')

<div class="row">
    <div class="col-md-6">
        {!! Form::label('animal', 'Animal *', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::checkbox('is_public', null) !!}
        {!! Form::label('public', 'Public', ['class' => 'font-weight-bold']) !!}
    </div>
</div>
@if ( isset($ourSurplus) )
    <div class="row">
        <div class="col-md-6">
            {!! Form::label('our_surplus_animal', $ourSurplus->animal, ['class' => 'text-danger']) !!}
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-6">
        <select class="animal-select2 form-control" type="default" style="width: 100%" name="animal_id">
            @if( isset($ourSurplus) && $ourSurplus->animal_id )
                <option value="{{ $ourSurplus->animal_id }}" selected>{{ $ourSurplus->animal }}</option>
            @endif
        </select>
        {!! Form::hidden('oursurplus_id', ( isset($ourSurplus) ) ? $ourSurplus->id : null, ['class' => 'form-control']) !!}
    </div>
</div>
<hr/>
<div class="row mb-3">
    <div class="col-md-12 text-center">
        {!! Form::label('info', 'INFORMATION') !!}
        <div class="row">
            <div class="col">
                {!! Form::label('availability', 'Availability *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('availability', $availability, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col">
                {!! Form::label('origin', 'Origin *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('origin', $origin, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col">
                {!! Form::label('age', 'Age group', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('age_group', $ageGroup, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col">
                {!! Form::label('bornYear', 'Yr of birth', ['class' => 'font-weight-bold']) !!}
                {!! Form::text('bornYear', null, ['class' => 'form-control']) !!}
            </div>
            <div class="col">
                {!! Form::label('size', 'Size cm', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('size', $sizes, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        {!! Form::label('area', 'Area', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('area', $areas->pluck('name', 'id'), (isset($ourSurplus)) ? $ourSurplus->area_region_id : null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('region', 'Continent', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('region', $regionsNames, (isset($ourSurplus)) ? $ourSurplus->region_id : null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-striped">
            <thead>
                <tr style="text-align: center;">
                    <th style="width: 100px;"></th>
                    <th>Curr *</th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Unknown</th>
                    <th>Pair</th>
                </tr>
            </thead>
            <tbody>
                @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                    <tr>
                        <td>Cost prices</td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::select('cost_currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('costPriceM', (isset($ourSurplus)) ? $ourSurplus->costPriceM : 0, ['class' => 'form-control calculate_sales']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('costPriceF', (isset($ourSurplus)) ? $ourSurplus->costPriceF : 0, ['class' => 'form-control calculate_sales']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('costPriceU', (isset($ourSurplus)) ? $ourSurplus->costPriceU : 0, ['class' => 'form-control calculate_sales']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('costPriceP', (isset($ourSurplus)) ? $ourSurplus->costPriceP : 0, ['class' => 'form-control calculate_sales']) !!}
                            </div>
                        </td>
                    </tr>
                @endif
                @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                    <tr>
                        <td>Sale prices</td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::select('sale_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceM', (isset($ourSurplus)) ? $ourSurplus->salePriceM : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceF', (isset($ourSurplus)) ? $ourSurplus->salePriceF : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceU', (isset($ourSurplus)) ? $ourSurplus->salePriceU : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceP', (isset($ourSurplus)) ? $ourSurplus->salePriceP : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-4">
        {!! Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('intern_remarks', 'Intern remarks', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('intern_remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('special_conditions', 'Special conditions', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('special_conditions', null, ['class' => 'form-control']) !!}
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
        {!! Form::label('area', 'Area:', ['class' => 'font-weight-bold']) !!}
        @foreach($areas as $area)
            <label class="checkbox-inline ml-2">
                {!! Form::checkbox('area_id[]', $area->id, (isset($ourSurplusAreasSelected) && $ourSurplusAreasSelected->contains($area->id)) ? true : false) !!} {{$area->short_cut}}
            </label>
        @endforeach
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
        {!! Form::label('our_surplus_lists', 'Select/Unselect surplus lists: ', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('ourSurplusLists[]', $ourSurplusLists, (isset($ourSurplusListsSelected)) ? $ourSurplusListsSelected : null, ['id' => 'ourSurplusLists', 'class' => 'form-control', 'multiple']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="button" id="ourSurplusSubmitBtn">{{ $submitButtonText }}</button>
@if (isset($ourSurplus))
    <a href="{{ route('our-surplus.show', $ourSurplus->id) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('our-surplus.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#ourSurplusForm');
        original = form.serialize();

        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        //Select2 animal selection
        $('[name=animal_id]').on('change', function () {
            var animalId = $(this).val();

            if(animalId != null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('api.animal-by-id') }}",
                    data: {
                        id: animalId,
                    },
                    success:function(data) {
                        // create the option and append to Select2
                        var newOption = new Option(data.animal.common_name.trim(), data.animal.id, true, true);
                        // Append it to the select
                        $('[name=animal_id]').append(newOption);
                    }
                });
            }

            $.ajax({
                type:'POST',
                url:"{{ route('api.duplicate-species-name') }}",
                data:{animalId: animalId},
                success:function(data){
                    if(data.error){

                    }else{
                        if(data.duplicate > 0){
                            Swal.fire({
                                title: "Please check",
                                html: "Please check or ask which of the scientific names you must select",
                                icon: 'warning',
                                showCancelButton: true,
                                cancelButtonText: "Done",
                                cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                                buttonsStyling: false,
                                closeOnConfirm: true,
                                showConfirmButton: false,
                                closeOnCancel: true,
                            });
                        }
                    }
                }
            });
        });

        $('#ourSurplusLists').multiselect({
            includeSelectAllOption: true,
            disableIfEmpty: true,
            buttonContainer: '<div class="btn-group" />',
            buttonWidth: '250px',
            maxHeight: 400,
            dropUp: true,
            templates: {
                li: '<li class="ml-n4" style="width: 270px;"><a><label></label></a></li>'
            }
        });

        $('#ourSurplusSubmitBtn').on('click', function(event) {
            event.preventDefault();
            window.onbeforeunload = null;
            if ($('[name=animal_id]').val() != null && $('[name=area]').val() != null && $('[name=origin]').val() != null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('our-surplus.checkSameRecord') }}",
                    data:{
                        oursurplus_id: $('[name=oursurplus_id]').val(),
                        animal_id: $('[name=animal_id]').val(),
                        area_region_id: $('[name=area]').val(),
                        origin: $('[name=origin]').val()
                    },
                    success:function(data) {
                        if (data.success) {
                            if (confirm("A surplus with the same species, origin, and area already exist. Do you want to insert/update anyway?"))
                                $('#ourSurplusForm').submit();
                        }
                        else
                            $('#ourSurplusForm').submit();
                    }
                });
            }
            else
                $('#ourSurplusForm').submit();
        });

        $('.calculate_cost').on('change', function() {
            let origin = $("#origin").val();
            let input = $(this);
            if(origin !== "stuffed"){
                input.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 200px");
                $.ajax({
                    type:'POST',
                    url:"{{ route('our-surplus.calculateCostPercentage') }}",
                    data:{
                        sale_price: input.val()
                    },
                    success:function(data) {
                        input.attr("style", "#fff");
                        if(data.length > 0) {
                            if(input.attr("name") === "salePriceM"){
                                $("[name=costPriceM]").val(data)
                            }
                            if(input.attr("name") === "salePriceF"){
                                $("[name=costPriceF]").val(data)
                            }
                            if(input.attr("name") === "salePriceU"){
                                $("[name=costPriceU]").val(data)
                            }
                            if(input.attr("name") === "salePriceP"){
                                $("[name=costPriceP]").val(data)
                            }
                        }
                    }
                });
            }
        });

        $('.calculate_sales').on('change', function() {
            let origin = $("#origin").val();
            let input = $(this);
            if(origin !== "stuffed"){
                input.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 200px");
                $.ajax({
                    type:'POST',
                    url:"{{ route('our-surplus.calculateSalesPercentage') }}",
                    data:{
                        cost_price: input.val()
                    },
                    success:function(data) {
                        input.attr("style", "#fff");
                        if(data.length > 0) {
                            if(input.attr("name") === "costPriceM"){
                                $("[name=salePriceM]").val(data)
                            }
                            if(input.attr("name") === "costPriceF"){
                                $("[name=salePriceF]").val(data)
                            }
                            if(input.attr("name") === "costPriceU"){
                                $("[name=salePriceU]").val(data)
                            }
                            if(input.attr("name") === "costPriceP"){
                                $("[name=salePriceP]").val(data)
                            }
                        }
                    }
                });
            }
        });

    });

</script>

@endsection
