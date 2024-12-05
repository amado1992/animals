@include('components.errorlist')

<div id="contactMessage" class="alert alert-danger d-none" role="alert"></div>

@if (session('contact_msg'))
    <div class="alert alert-danger" role="alert">
        {{ session('contact_msg') }}
    </div>
@endif

<div id="countryMessage" class="alert alert-warning" role="warning">
    Please check if the name of the country can be located, see also website or email extension In case there is absolutely no country, then insert the contact without it.
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('contact_email', 'Email address') !!}
        {!! Form::text('contact_email', null, ['class' => 'form-control']) !!}
        {!! Form::hidden('contact_id', isset($contact) ? $contact->id : null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('city', 'City *') !!}
        {!! Form::text('city', $city ?? null, ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('country', 'Country') !!}
        {!! Form::select('country_id', $countries, $country_id ?? null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card shadow mb-4">
            <div class="card-header p-1">Select institution</div>
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::radio('select_institution_option', 'matched_institution') !!}
                        {!! Form::label('matched_institution', 'Matched institutions') !!}
                        <label id="matched_institutions" class="text-danger mr-2">{{ count($matchedInstitutions) }}</label>
                        {!! Form::radio('select_institution_option', 'searched_institution') !!}
                        {!! Form::label('search_institution', 'Search institution', ['class' => 'mr-2']) !!}
                        @if (isset($contact))
                            {!! Form::radio('select_institution_option', 'keep_institution', true) !!}
                            {!! Form::label('keep_institution', 'Keep institution', ['class' => 'mr-2']) !!}
                        @endif
                        {!! Form::radio('select_institution_option', 'self_institution') !!}
                        {!! Form::label('self_institution', 'Self instituted') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::select('select_institution', $matchedInstitutions, (isset($contact) && $contact->organisation_id) ? $contact->organisation_id : null, ['class' => 'form-control', 'disabled', 'placeholder' => '- select -']) !!}
                    </div>
                    <div class="col-md-6">
                        <select class="institution-select2 form-control" type="default" style="width: 100%" name="organisation_id" disabled>
                            @if( isset($contact) && $contact->organisation_id )
                                <option value="{{ $contact->organisation_id }}" selected>{{ $contact->organisation->name }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if( isset($contact) && $contact->organisation_id )
        <div class="col-md-3">
            {!! Form::label('organisation_type', 'Type') !!}
            {!! Form::select('organisation_type', $organization_types, $contact->organisation->organisation_type, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::label('organisation_level', 'Level') !!}
            {!! Form::select('organisation_level', $organization_levels, $contact->organisation->level, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
        </div>
    @endif
</div>

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('title', 'Title') !!}
        {!! Form::select('title', ['Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.', 'Dr.' => 'Dr.', 'Ing.' => 'Ing.'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('first_name', 'First name') !!}
        {!! Form::text('first_name', $first_name ?? null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::label('last_name', 'Last name') !!}
        {!! Form::text('last_name', $last_name ?? null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('mobile_phone', 'Mobile phone') !!}
        {!! Form::text('mobile_phone', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('position', 'Position') !!}
        {!! Form::text('position', null, ['class' => 'form-control']) !!}
    </div>
    @if (isset($contact) && $contact->source == "website")
        <div class="col-md-3">
            {!! Form::label('member_approved_status', 'Approved status') !!}
            {!! Form::select('member_approved_status', $member_approved_status, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
        </div>
    @endif
    <div class="col-md-3">
        {!! Form::label('mailing_category', 'Mailing category') !!}
        {!! Form::select('mailing_category', $mailing_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-4">
        {!! Form::label('specialty', 'Specialty *') !!}
        {!! Form::select('specialty', $specialties, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div>
    {!! Form::label('relation_type', 'Relation type') !!}<br>
    @foreach(['supplier', 'client', 'both'] as $relationType)
        <label class="checkbox-inline ml-2">
            {!! Form::radio('relation_type', $relationType, true) !!} {!! ucfirst($relationType) !!}
        </label>
    @endforeach
</div>

<div>
    {!! Form::label('interest_sections', 'Interest sections:') !!}<br>
    @foreach($interest_sections as $section)
        <label class="checkbox-inline ml-2">
            {!! Form::checkbox('interest_section[]', $section->key, (isset($contactInterestSections) && $contactInterestSections->contains($section->key)) ? true : false) !!} {{$section->label}}
        </label>
    @endforeach
</div>

<hr class="mb-3">

<button class="btn btn-primary btn-lg" type="button" id="submitBtn">{{ $submitContactButtonText }}</button>
<input type="hidden" name="edit" value="{{ $edit ?? "" }}">
<input type="hidden" name="edit_id" value="{{ $edit_id ?? "" }}">
@if (isset($contact))
    <a href="{{ route('contacts.show', $contact) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('contacts.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#contactForm');
        original = form.serialize();

        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        var selectInstitutionOption = $('[name=select_institution_option]:checked').val();
        if (selectInstitutionOption == 'matched_institution') {
            $("[name=select_institution]").prop('disabled', false);
            $("[name=organisation_id]").prop('disabled', true);
        }
        else if (selectInstitutionOption == 'searched_institution') {
            $("[name=select_institution]").prop('disabled', true);
            $("[name=organisation_id]").prop('disabled', false);
        }
        else {
            $("[name=select_institution]").prop('disabled', true);
            $("[name=organisation_id]").prop('disabled', true);
        }

        $('[name=select_institution_option]').click(function() {
            if ($(this).val() == 'matched_institution') {
                $("[name=select_institution]").prop('disabled', false);
                $("[name=organisation_id]").prop('disabled', true);
            }
            else if ($(this).val() == 'searched_institution') {
                $("[name=select_institution]").prop('disabled', true);
                $("[name=organisation_id]").prop('disabled', false);
            }
            else {
                $("[name=select_institution]").prop('disabled', true);
                $("[name=organisation_id]").prop('disabled', true);
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
                        /***********************************************************/
                        var selected = '';

                        $('[name=select_institution]').empty();
                        $('[name=select_institution]').append('<option value="">- select -</option>');
                        $.each(data.matchedInstitutions, function(key, value) {
                            if ($('[name=contact_id]').val() != null && data.contact && data.contact.organisation)
                                selected = (key == data.contact.organisation.id) ? 'selected' : '';

                            $('[name=select_institution]').append('<option value="'+ key +'" ' + selected + '>' + value +'</option>');
                        });
                        $('#matched_institutions').html(data.matchedInstitutionsTotal);
                    }
                });
            }
        });

        $('[name=city]').keyup(function() {
            $.ajax({
                type:'POST',
                url:"{{ route('api.institutions-by-domain-city') }}",
                data: {
                    id: $('[name=contact_id]').val(),
                    email: $('[name=contact_email]').val(),
                    city: $('[name=city]').val()
                },
                success:function(data) {
                    var selected = '';

                    $('[name=select_institution]').empty();
                    $('[name=select_institution]').append('<option value="">- select -</option>');
                    $.each(data.matchedInstitutions, function(key, value) {
                        if ($('[name=contact_id]').val() != null && data.contact && data.contact.organisation)
                            selected = (key == data.contact.organisation.id) ? 'selected' : '';

                        $('[name=select_institution]').append('<option value="'+ key +'" ' + selected + '>' + value +'</option>');
                    });
                    $('#matched_institutions').html(data.matchedInstitutionsTotal);
                }
            });
        });

        //Select2 institution selection
        $('[name=organisation_id]').on('change', function () {
            var organisationId = $(this).val();

            if(organisationId != null) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('api.institution-by-id') }}",
                    data: {
                        id: organisationId,
                    },
                    success:function(data) {
                        // create the option and append to Select2
                        var newOption = new Option(data.institution.name.trim(), data.institution.id, true, true);
                        // Append it to the select
                        $('[name=organisation_id]').append(newOption);
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

            return $('#contactForm').submit();
        });

    });

</script>

@endsection
