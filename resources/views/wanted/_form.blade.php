@include('components.errorlist')

@if (empty($wanted->catalog_pic) && empty($wanted->imagen_first))
    <div id="imageAnimalMessage" class="alert alert-danger" role="danger">
        <strong>Remember that it is necessary to insert the picture catalog of the new species once created.</strong>
    </div>
@endif

<div class="row">
    <div class="col-md-4">
        {!! Form::label('client_institution', 'Client institution *', ['class' => 'font-weight-bold']) !!}
    </div>
    @if ( isset($wanted) )
        <div class="col-md-4">
            {!! Form::label('client_contact', 'Client contact *', ['class' => 'font-weight-bold']) !!}<br>
        </div>
    @endif
    <div class="col-md-4">
        {!! Form::label('animal', 'Animal *', ['class' => 'font-weight-bold']) !!}<br>
    </div>
</div>
@if ( isset($wanted) )
    <div class="row">
        <div class="col-md-4">
            {!! Form::label('wanted_institution', ($wanted->organisation) ? $wanted->organisation->name : ' ', ['class' => 'text-danger']) !!}
        </div>
        <div class="col-md-4">
            {!! Form::label('wanted_contact', $wanted->client, ['class' => 'text-danger']) !!}
        </div>
        <div class="col-md-4">
            {!! Form::label('wanted_animal', $wanted->animal, ['class' => 'text-danger']) !!}
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-4">
        <select class="institution-select2 form-control" type="default" style="width: 100%" name="organisation_id">
            @if( isset($wanted) && $wanted->organisation_id )
                <option value="{{ $wanted->organisation_id }}" selected>{{ $wanted->organisation->name }}</option>
            @endif
        </select>
    </div>
    @if ( isset($wanted) )
        <div class="col-md-4">
            <select class="contact-select2 form-control" type="default" style="width: 100%" name="client_id">
                @if( isset($wanted) && $wanted->client_id )
                    <option value="{{ $wanted->client_id }}" selected>{{ $wanted->client }}</option>
                @endif
            </select>
        </div>
    @endif
    <div class="col-md-4">
        <select class="animal-select2 form-control" type="default" style="width: 100%" name="animal_id">
            @if( isset($wanted) && $wanted->animal_id )
                <option value="{{ $wanted->animal_id }}" selected>{{ $wanted->animal }}</option>
            @endif
        </select>
    </div>
</div>
<hr/>
<div class="row mb-3">
    <div class="col-md-12 text-center">
        {!! Form::label('info', 'INFORMATION', ['class' => 'font-weight-bold']) !!}
        <div class="row">
            <div class="col">
                {!! Form::label('origin', 'Origin *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('origin', $origin, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col">
                {!! Form::label('age_group', 'Age group', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('age_group', $ageGroup, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
            <div class="col">
                {!! Form::label('looking_for', 'Looking for *', ['class' => 'font-weight-bold']) !!}
                {!! Form::select('looking_for', $lookingFor, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-6">
        {!! Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('intern_remarks', 'Intern remarks', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('intern_remarks', null, ['class' => 'form-control']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if (isset($wanted))
    <a href="{{ route('wanted.show', $wanted) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('wanted.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#wantedForm');
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

    });

</script>

@endsection
