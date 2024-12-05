@include('components.errorlist')

<div class="row">
    <div class="col-md-4">
        {!! Form::label('supplier_institution', 'Supplier institution *', ['class' => 'font-weight-bold']) !!}
    </div>
    @if (isset($surplus) && $surplus->organisation == null && $surplus->contact != null)
        <div class="col-md-4">
            {!! Form::label('supplier', 'Supplier contact', ['class' => 'font-weight-bold']) !!}
        </div>
    @endif
    <div class="col-md-4">
        {!! Form::label('animal', 'Animal *', ['class' => 'font-weight-bold']) !!}
    </div>
</div>
@if ( isset($surplus) )
    <div class="row">
        <div class="col-md-4">
            {!! Form::label('surplus_institution', ($surplus->organisation) ? $surplus->organisation->name : ' ', ['class' => 'text-danger']) !!}
        </div>
        @if ($surplus->organisation == null && $surplus->contact != null)
            <div class="col-md-4">
                {!! Form::label('surplus_supplier', $surplus->supplier, ['class' => 'text-danger']) !!}
            </div>
        @endif
        <div class="col-md-4">
            {!! Form::label('surplus_animal', $surplus->animal, ['class' => 'text-danger']) !!}
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-4">
        <select class="institution-select2 form-control" type="default" style="width: 100%" name="organisation_id">
            @if( isset($surplus) && $surplus->organisation_id )
                <option value="{{ $surplus->organisation_id }}" selected>{{ $surplus->organisation->name }}</option>
            @endif
        </select>
        {!! Form::hidden('surplus_id', ( isset($surplus) ) ? $surplus->id : null, ['class' => 'form-control']) !!}
    </div>
    @if (isset($surplus) && $surplus->organisation == null && $surplus->contact != null)
        <div class="col-md-4">
            <select class="contact-select2 form-control" type="default" style="width: 100%" name="contact_id">
                @if( isset($surplus) && $surplus->contact_id )
                    <option value="{{ $surplus->contact_id }}" selected>{{ $surplus->supplier }}</option>
                @endif
            </select>
        </div>
    @endif
    <div class="col-md-4">
        <select class="animal-select2 form-control" type="default" style="width: 100%" name="animal_id">
            @if( isset($surplus) && $surplus->animal_id )
                <option value="{{ $surplus->animal_id }}" selected>{{ $surplus->animal }}</option>
            @endif
        </select>
    </div>
</div>

<hr/>

<div class="row mb-3">
    <div class="col-md-12 text-center">
        {!! Form::label('info', 'INFORMATION', ['class' => 'font-weight-bold']) !!}
        <div class="row">
            @if (isset($surplus))
                <div class="col">
                    {!! Form::label('surplus_status', 'Status', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('surplus_status', $surplus_status, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                </div>
            @endif
            <div class="col">
                {!! Form::label('origin', 'Origin *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('origin', $origin, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col">
                {!! Form::label('age_group', 'Age group', ['class' => 'font-weight-bold']) !!}
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

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if (isset($surplus))
    <a href="{{ route('surplus-collection.show', $surplus) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('surplus-collection.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#surplusCollectionForm');
        original = form.serialize();

        form.submit(function() {
            window.onbeforeunload = null;
        })

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

    });

</script>

@endsection
