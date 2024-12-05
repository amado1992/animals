@include('components.errorlist')

<div id="contactMessage" class="alert alert-danger d-none" role="alert"></div>

<div id="countryMessage" class="alert alert-warning" role="warning">
    Please check if the name of the country can be located, see also website or email extension In case there is absolutely no country, then insert the contact without it.
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('contact_email', 'Email address') !!}
        {!! Form::text('contact_email', null, ['class' => 'form-control']) !!}
        {!! Form::hidden('select_institution_option', 'keep_institution', ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('city', 'City *') !!}
        {!! Form::text('city', null, ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('country', 'Country') !!}
        {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('title', 'Title') !!}
        {!! Form::select('title', ['Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.', 'Dr.' => 'Dr.', 'Ing.' => 'Ing.'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('first_name', 'First name') !!}
        {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::label('last_name', 'Last name') !!}
        {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('mobile_phone', 'Phone number') !!}
        {!! Form::text('mobile_phone', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('position', 'Position') !!}
        {!! Form::text('position', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('mailing_category', 'Mailing category') !!}
        {!! Form::select('mailing_category', $mailing_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
        {!! Form::hidden('organization_id', $organization->id, ['class' => 'form-control']) !!}
    </div>
</div>

<div>
    {!! Form::label('interest_sections', 'Interest sections:') !!}<br>
    @foreach($interest_sections as $section)
        <label class="checkbox-inline ml-2">
            {!! Form::checkbox('interest_section[]', $section->key, false) !!} {{$section->label}}
        </label>
    @endforeach
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit" id="submitBtn">{{ $submitButtonText }}</button>
<a href="{{ route('organisations.show', [$organization->id]) }}" class="btn btn-link" type="button">Cancel</a>

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('[name=contact_email]').keyup(function() {
            if ($('[name=contact_email]').val().trim() != '') {
                $.ajax({
                    type:'POST',
                    url:"{{ route('api.check-contact') }}",
                    data: {
                        id: $('[name=contact_id]').val(),
                        email: $('[name=contact_email]').val(),
                        city: $('[name=city]').val()
                    },
                    success:function(data) {
                        console.log(data);

                        if(data.contact) {
                            $('#contactMessage').removeClass("d-none");
                            $('#contactMessage').html("Contact email already in.");
                        }
                        else
                            $('#contactMessage').addClass("d-none");
                    }
                });
            }
        });

        $('#submitBtn').on('click', function () {
            var country = $('[name=country_id]').val();

            if (country.trim() == '') {
                if (!confirm('The country is empty. Are you sure to add the contact without country?'))
                    return;
            }

            window.onbeforeunload = null

            return $('#addContactForm').submit();
        });

    });

</script>

@endsection
