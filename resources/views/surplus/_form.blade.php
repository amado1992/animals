@include('components.errorlist')

<div class="row">
    <div class="col-md-5">
        {!! Form::label('supplier_institution', 'Supplier institution *', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::label('animal', 'Animal *', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-2 p-0">
        {!! Form::checkbox('to_members', null) !!}
        {!! Form::label('to_members', 'To members', ['class' => 'font-weight-bold']) !!}
    </div>
</div>
@if ( isset($surplus) )
<div class="row">
    <div class="col-md-5">
        {!! Form::label('surplus_institution', ($surplus->organisation) ? $surplus->organisation->name : ' ', ['class' => 'text-danger']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::label('surplus_animal', $surplus->animal, ['class' => 'text-danger', 'id' => 'surplus_animal']) !!}
    </div>
</div>
@endif
<div class="row">
    <div class="col-md-5">
        <select class="institution-select2 form-control" type="default" style="width: 100%" name="organisation_id">
            @if( isset($surplus) && $surplus->organisation_id )
                <option value="{{ $surplus->organisation_id }}" selected>{{ $surplus->organisation->name }}</option>
            @endif
        </select>
        {!! Form::hidden('surplus_id', ( isset($surplus) ) ? $surplus->id : null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-5">
        <select class="animal-select2 form-control" type="default" style="width: 100%" name="animal_id">
            @if( isset($surplus) && $surplus->animal_id )
                <option value="{{ $surplus->animal_id }}" selected>{{ $surplus->animal }}</option>
            @endif
        </select>
    </div>
</div>

@if (isset($surplus) && $surplus->organisation == null && $surplus->contact != null)
    <div class="row">
        <div class="col-md-5">
            {!! Form::label('supplier_contact', 'Supplier contact', ['class' => 'font-weight-bold']) !!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <select class="contact-select2 form-control" type="default" style="width: 100%" name="contact_id">
                @if( isset($surplus) && $surplus->contact_id )
                    <option value="{{ $surplus->contact_id }}" selected>{{ $surplus->supplier }}</option>
                @endif
            </select>
        </div>
    </div>
@endif

<hr/>

<div class="row mb-3">
    <div class="col-md-4 text-center">
        {!! Form::label('quantities', 'QUANTITY', ['class' => 'font-weight-bold']) !!}
        <div class="row">
            <div class="col pr-0">
                {!! Form::label('males', 'M', ['class' => 'font-weight-bold']) !!}
                {!! Form::text('quantityM', (isset($surplus)) ? $surplus->quantityM : 0, ['class' => 'form-control']) !!}
            </div>
            <div class="col pr-0">
                {!! Form::label('females', 'F', ['class' => 'font-weight-bold']) !!}
                {!! Form::text('quantityF', (isset($surplus)) ? $surplus->quantityF : 0, ['class' => 'form-control']) !!}
            </div>
            <div class="col pr-0">
                {!! Form::label('females', 'U', ['class' => 'font-weight-bold']) !!}
                {!! Form::text('quantityU', (isset($surplus)) ? $surplus->quantityU : 0, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-8 text-center">
        {!! Form::label('info', 'INFORMATION', ['class' => 'font-weight-bold']) !!}
        <div class="row">
            <div class="col pr-0 info_alert">
                {!! Form::label('origin', 'Origin *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('origin', $origin, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                <span class="invalid-feedback" role="alert">
                    <strong>Remember to update later the origin information</strong>
                </span>
            </div>
            <div class="col pr-0">
                {!! Form::label('age_group', 'Age group', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('age_group', $ageGroup, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col pr-0">
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
    <div class="col-md-12 text-center">
        <div class="row">
            <div class="col-md-3">
                {!! Form::label('surplus_status', 'Status', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('surplus_status', $surplus_status, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::label('area', 'Area *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('area_region_id', $areas, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::label('country', 'Country *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::label('organisation_level', 'Level', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('institution_level', $organization_levels, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-striped mb-n2">
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
                @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                    <tr>
                        <td class="font-weight-bold">Cost prices</td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::select('cost_currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::number('costPriceM', (isset($surplus)) ? $surplus->costPriceM : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::number('costPriceF', (isset($surplus)) ? $surplus->costPriceF : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::number('costPriceU', (isset($surplus)) ? $surplus->costPriceU : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::number('costPriceP', (isset($surplus)) ? $surplus->costPriceP : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                    </tr>
                @endif
                @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                    <tr>
                        <td class="font-weight-bold">Sale prices</td>
                        <td>
                            <div class="input-group input-group-sm">
                                {!! Form::select('sale_currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceM', (isset($surplus)) ? $surplus->salePriceM : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceF', (isset($surplus)) ? $surplus->salePriceF : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceU', (isset($surplus)) ? $surplus->salePriceU : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                            {!! Form::number('salePriceP', (isset($surplus)) ? $surplus->salePriceP : 0, ['class' => 'form-control']) !!}
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<hr/>
<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('intern_remarks', 'Intern remarks', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('intern_remarks', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        {!! Form::label('special_conditions', 'Special conditions', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('special_conditions', null, ['class' => 'form-control']) !!}
    </div>
</div>
<hr/>
@if (!Auth::user()->hasRole('office'))
    <div class="row">
        <div class="col-md-12">
            {!! Form::label('surplus_lists', 'Select/Unselect surplus lists: ', ['class' => 'font-weight-bold']) !!}
            {!! Form::select('surplusLists[]', $surplusLists, (isset($surplusListsSelected)) ? $surplusListsSelected : null, ['id' => 'surplusLists', 'class' => 'form-control', 'multiple']) !!}
        </div>
    </div>
@endif

<hr class="mb-4">
{!! Form::hidden('url', !empty($url) ? $url : "", ['class' => 'form-control']) !!}
<button class="btn btn-primary btn-lg" id="surplusSubmitBtn" type="submit">{{ $submitButtonText }}</button>
@if (isset($surplus) && empty($url))
    <a href="{{ route('surplus.show', $surplus) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('surplus.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#surplusForm');
        original = form.serialize();

        form.submit(function(e) {
            window.onbeforeunload = null;
        })

        var catalog_pic = "{{ empty($surplus->catalog_pic) }}";
        var imagen_first = "{{ empty($surplus->imagen_first) }}";

        if (catalog_pic && imagen_first){
            $('#surplusSubmitBtn').click(function(event) {
                event.preventDefault();
                Swal.fire({
                        title: "Update species image !",
                        html: "Remember that it is necessary to insert the picture catalog of the new species once created.",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: "Done",
                        confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
                        cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                        buttonsStyling: false,
                        closeOnConfirm: true,
                        showConfirmButton: true,
                        closeOnCancel: true,
                    }).then((result) => {
                        $('#surplusForm').submit();
                    });
            });
        }



        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        //Select2 institution selection
        $('[name=organisation_id]').on('change', function () {
            var institutionId = $(this).val();

            if(institutionId != null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('api.institution-contacts') }}",
                    data: {
                        value: institutionId,
                    },
                    success:function(data) {
                        // create the option and append to Select2
                        var newOption = new Option(data.organization.name.trim(), data.organization.id, true, true);
                        // Append it to the select
                        $('[name=organisation_id]').append(newOption);
                    }
                });
            }
        });

        $('[name=origin]').on("change", function(){
            alert_origin();
        });

        function alert_origin(){
            let val_origin = $('[name=origin]').val();
            if(val_origin.length !== 0 && val_origin === "unknown"){
                Swal.fire({
                    title: "Please ask for the origin",
                    html: "Remember to update later the origin information.",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: "Done",
                    cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                    buttonsStyling: false,
                    closeOnConfirm: true,
                    showConfirmButton: false,
                    closeOnCancel: true,
                })
            }
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

        $('#surplusLists').multiselect({
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

        $('[name=to_members]').on('change', function () {

            var value = $(this).is(":checked");
            var surplus_id = $('[name=surplus_id]').val();

            $.ajax({
                type:'POST',
                url:"{{ route('surplus.updateToMembersDate') }}",
                data:{
                    value: (value) ? 1 : 0,
                    surplus_id: surplus_id
                },
                success:function(data){

                }
            });
        });

        $('[name=area_region_id]').on('change', function () {
            var value = $(this).val();

            $.ajax({
                type:'POST',
                url:"{{ route('countries.getCountriesByArea') }}",
                data:{
                    value: value,
                },
                success:function(data){
                    $('[name=country_id]').empty();
                    $('[name=country_id]').append('<option value="">- select -</option>');
                    $.each(data.countries, function(key, value) {
                        $('[name=country_id]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        });

        $(".animal-select2").on("change", function(){
            var animal_id = $("[name=animal_id]").val();
            var organisation_id = $("[name=organisation_id]").val();
            $.ajax({
                type:'POST',
                url:"{{ route('surplus.duplicateSurplus') }}",
                data:{
                    animal_id: animal_id,
                    organisation_id: organisation_id,
                },
                success:function(data){
                    if(data.success){
                        Swal.fire({
                            title: "Duplicate species",
                            html: "There is already a surplus with this same species and supplier",
                            icon: 'warning',
                            showCancelButton: true,
                            cancelButtonText: "Done",
                            confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
                            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                            buttonsStyling: false,
                            closeOnConfirm: true,
                            showConfirmButton: false,
                            closeOnCancel: true,
                        })
                    }
                }
            });
        });
    });

</script>

@endsection
