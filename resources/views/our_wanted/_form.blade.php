@include('components.errorlist')

<div class="row">
    <div class="col-md-6">
        {!! Form::label('animal', 'Animal *', ['class' => 'font-weight-bold'], ['class' => 'font-weight-bold']) !!}
    </div>
</div>
@if ( isset($ourWanted) )
    <div class="row">
        <div class="col-md-6">
            {!! Form::label('our_wanted_animal', $ourWanted->animal, ['class' => 'text-danger']) !!}
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-6">
        <select class="animal-select2 form-control" type="default" style="width: 100%" name="animal_id">
            @if( isset($ourWanted) && $ourWanted->animal_id )
                <option value="{{ $ourWanted->animal_id }}" selected>{{ $ourWanted->animal }}</option>
            @endif
        </select>
        {!! Form::hidden('ourwanted_id', ( isset($ourWanted) ) ? $ourWanted->id : null, ['class' => 'form-control']) !!}
    </div>
</div>
<hr/>
<div class="row mb-3">
    <div class="col-md-12 text-center">
        {!! Form::label('info', 'INFORMATION') !!}
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
<hr/>
<div class="row">
    <div class="col-md-12">
        {!! Form::label('area', 'Area:', ['class' => 'font-weight-bold']) !!}<br>
        @foreach($areas as $area)
            <label class="checkbox-inline ml-2">
                {!! Form::checkbox('area_id[]', $area->id, (isset($ourWantedAreasSelected) && $ourWantedAreasSelected->contains($area->id)) ? true : false) !!} {{$area->short_cut}}
            </label>
        @endforeach
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
        {!! Form::label('our_wanted_lists', 'Select/Unselect wanted lists: ', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('ourWantedLists[]', $ourWantedLists, (isset($ourWantedListsSelected)) ? $ourWantedListsSelected : null, ['id' => 'ourWantedLists', 'class' => 'form-control', 'multiple']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit" id="ourWantedSubmitBtn">{{ $submitButtonText }}</button>
@if (isset($ourWanted))
    <a href="{{ route('our-wanted.show', $ourWanted) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('our-wanted.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#ourWantedForm');
        original = form.serialize();

        form.submit(function() {
            window.onbeforeunload = null;
        })

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

        $('#ourWantedLists').multiselect({
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

        $('#ourWantedSubmitBtn').on('click', function(event) {
            event.preventDefault();
            window.onbeforeunload = null;
            if ($('[name=animal_id]').val() != null && $('[name=origin]').val() != null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('our-wanted.checkSameRecord') }}",
                    data:{
                        ourwanted_id: $('[name=ourwanted_id]').val(),
                        animal_id: $('[name=animal_id]').val(),
                        origin: $('[name=origin]').val()
                    },
                    success:function(data) {
                        if (data.success) {
                            if (confirm("A standard wanted with the same species already exist. Do you want to insert/update anyway?"))
                                $('#ourWantedForm').submit();
                        }
                        else
                            $('#ourWantedForm').submit();
                    }
                });
            }
            else
                $('#ourWantedForm').submit();
        });

    });

</script>

@endsection
