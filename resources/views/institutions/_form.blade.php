
<div id="institutionMessage" class="alert alert-warning d-none"></div>

@include('components.errorlist')

<div id="countryMessage" class="alert alert-warning" role="warning">
    Please check if the name of the country can be located, see also website or email extension In case there is absolutely no country, then insert the institution without it.
</div>

<div class="row">
    <div class="col-md-6">
        {!! Form::label('email', 'Email address') !!}
        {!! Form::text('email', null, ['class' => 'form-control ' . ($errors->has('email') ? 'is-invalid': '')]) !!}
        {!! Form::hidden('organization_id', isset($organisation) ? $organisation->id : null, ['class' => 'form-control']) !!}
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-md-6">
        {!! Form::label('domain_name', 'Domain name') !!}
        {!! Form::text('domain_name', $domain ?? null, ['class' => 'form-control']) !!}
        {!! Form::label('domain_msg', '(Fill in, if institution has a unique website address)') !!}
    </div>
</div>

<div class="row mb-2">
    <div class="{{isset($organisation) ? 'col-md-4' : 'col-md-4'}}">
        {!! Form::label('name', 'Institution name *') !!}
        {!! Form::text('name', $name ?? null, ['class' => 'form-control ' . ($errors->has('name') ? 'is-invalid': ''), 'required']) !!}
        @error('name')
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-md-2" id="canonical_name">
        {!! Form::label('canonical_name_type', 'Canonical Name*') !!}
        <div class="frmSearch">
            <div class="invalid-feedback-tooltips d-none">
                <span id="invalid-canonical_name" role="alert">
                </span>
                <div class="invalid-arrow">
                </div>
            </div>
            {!! Form::text('canonical_name', $name ?? null, ['class' => 'form-control', 'id' => 'search-box' , 'autocomplete' => 'off', 'data-validate' => "true"]) !!}
            <div id="suggesstion-box" class="d-none"></div>
        </div>
    </div>
    <div class="col-md-2">
        {!! Form::label('specialty', 'Specialty *') !!}
        {!! Form::select('specialty', $specialties, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-2">
        {!! Form::label('organisation_type', 'Type *') !!}
        {!! Form::select('organisation_type', $organization_types, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-2">
        {!! Form::label('level', 'Level') !!}
        {!! Form::select('level', $organization_levels, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>
<div class="row mb-2">
    @if(!isset($organisation))
        <div class="col-md-2">
            {!! Form::label('contact_first_name', 'Contact First name') !!}
            {!! Form::text('contact_first_name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::label('contact_last_name', 'Contact Last name') !!}
            {!! Form::text('contact_last_name', null, ['class' => 'form-control']) !!}
        </div>
    @endif
    <div class="{!! !isset($organisation) ? "col-md-4" : "col-md-6" !!}">
        {!! Form::label('address', 'Address') !!}
        {!! Form::text('address', null, ['class' => 'form-control']) !!}
    </div>
    <div class="{!! !isset($organisation) ? "col-md-2" : "col-md-3" !!}">
        {!! Form::label('zipcode', 'Zipcode') !!}
        {!! Form::text('zipcode', null, ['class' => 'form-control']) !!}
    </div>
    <div class="{!! !isset($organisation) ? "col-md-2" : "col-md-3" !!}">
        {!! Form::label('info_status', 'Info status') !!}
        {!! Form::select('info_status', $infoStatuses, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>


<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('country', 'Country') !!}
        {!! Form::select('country_id', $countries, $country_id ?? null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-3" id="city">
        {!! Form::label('city', 'City') !!}
        {!! Form::text('city', $city ?? null, ['class' => 'form-control']) !!}
        <span class="invalid-feedback" role="alert">
            <strong>Remember to update later the city information</strong>
        </span>
    </div>
    <div class="col-md-3">
        {!! Form::label('phone', 'Phone') !!}
        {!! Form::number('phone', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('vat', 'Vat number') !!}
        {!! Form::text('vat_number', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('website', 'Website') !!}
        {!! Form::text('website', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('facebook_page', 'Facebook page') !!}
        {!! Form::text('facebook_page', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('short_description', 'Short description') !!}
        {!! Form::text('short_description', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('public_zoos_relation', 'Public zoos relation') !!}
        {!! Form::text('public_zoos_relation', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('animal_related_association', 'Animal related association') !!}
        {!! Form::text('animal_related_association', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('remarks', 'Remarks') !!}
        {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('open_remarks', 'Open remarks') !!}
        {!! Form::text('open_remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('internal_remarks', 'Internal remarks') !!}
        {!! Form::text('internal_remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('mailing_category', 'Mailing category') !!}
        {!! Form::select('mailing_category', $mailing_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('name', 'Synonyms') !!}
        {!! Form::textarea('synonyms', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('interest_sections', 'Interest sections') !!}<br>
        @foreach($interest_sections as $section)
            <label class="checkbox-inline ml-2">
                {!! Form::checkbox('interest_section[]', $section->key, (isset($organizationInterestSections) && $organizationInterestSections->contains($section->key)) ? true : false) !!} {{$section->label}}
            </label>
        @endforeach
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('associations', 'Associations') !!}<br>
        @foreach($associations as $association)
            <label class="checkbox-inline ml-2">
                {!! Form::checkbox('associations[]', $association->key, (isset($organizationAssociations) && $organizationAssociations->contains($association->key)) ? true : false) !!} {{$association->label}}
            </label>
        @endforeach
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('relation_type', 'Relation type') !!}<br>
        @foreach(['supplier', 'client', 'both'] as $relationType)
            <label class="checkbox-inline ml-2">
                {!! Form::radio('relation_type', $relationType, true) !!} {!! ucfirst($relationType) !!}
            </label>
        @endforeach
    </div>
</div>

<hr/>

<input type="hidden" name="edit" value="{{ $edit ?? "" }}">
<input type="hidden" name="edit_id" value="{{ $edit_id ?? "" }}">
<button class="btn btn-primary btn-lg" id="saveInstitution" type="submit">{{ $submitButtonText }}</button>
@if (isset($organisation))
    <a href="{{ route('organisations.show', $organisation) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('organisations.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#institutionForm');
        original = form.serialize();

        form.submit(function() {
            window.onbeforeunload = null
        })

        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        $('[name=email]').trigger('keyup');

        $("#submmit_institucion").hover(function(){
            alert_city();
        });

        $("#submmit_institucion").focus(function(){
            alert_city();
        });

        $('[name=city]').on("change", function(){
            alert_city();
        });

        $('[name=city]').keyup(function(){
            $('[name=city]').removeClass("is-invalid");
        });

    });

    function alert_city(){
        let val_city = $('[name=city]').val();
        if(val_city.length === 0){
            $('[name=city]').addClass("is-invalid");
        }else{
            $('[name=city]').removeClass("is-invalid");
        }
    }

    $('[name=email]').keyup(function() {
        if ($('[name=email]').val().trim() != '') {
            $.ajax({
                type:'POST',
                url:"{{ route('api.check-institution') }}",
                data: {
                    id: $('[name=organization_id]').val(),
                    email: $('[name=email]').val()
                },
                success:function(data) {
                    if(data.already_in) {
                        $('[name=domain_name]').val('');
                        $('#institutionMessage').removeClass("d-none");
                        $('#institutionMessage').html("Institution email already in.");
                    }
                    else if (data.generic_domain) {
                        $('[name=domain_name]').val('');
                        $('#institutionMessage').removeClass("d-none");
                        $('#institutionMessage').html("Email domain is a generic domain. Check if the institution has other main email if is possible.");
                    }
                    else {
                        $('[name=domain_name]').val(data.domain_name);
                        $('#institutionMessage').addClass("d-none");
                    }
                }
            });
        }
    });

    $('[name=email]').on("change", function() {
        var email = $(this);
        if (email.val().trim() != '') {
            $.ajax({
                type:'GET',
                url:"{{ route('api.search-domain-name') }}",
                data: {
                    email: email.val()
                },
                beforeSend: function() {
                    email.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
                },
                success:function(data) {
                    if(data.domain_name != null){
                        $("#search-box").removeClass("search-box-correct");
                        $('[name=canonical_name]').val(data.domain_name.canonical_name);
                    }
                },complete: function() {
                    email.css("background", "#FFF");
                }
            });
        }
    });

    $("#search-box").keyup(function() {
        $("#search-box").removeClass("is-invalid");
        $("#suggesstion-box").addClass("d-none");
        $("#search-box").removeClass("search-box-correct");
        validateCanonical($(this).val());
        $.ajax({
            type: "get",
            url: "{{ route('api.canonical-name-select2') }}",
            data: {
                search: $(this).val(),
                email: $("[name=email]").val()
            },
            beforeSend: function() {
                $("#search-box").css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
            },
            success: function(data) {
                if(typeof data.items !== "undefined" && data.items.length > 0) {
                    var $container = $(
                        "<div class='select2-result-repository clearfix'>" +
                        "<div class='select2-result-repository__meta'>" +
                            "<div class='select2-result-repository__name'></div>" +
                            "<div class='select2-result-repository__canonical_name'></div>" +
                        "</div>" +
                        "</div>"
                    );
                    var html = "";
                    $.each(data.items, function(key, value) {
                        if(typeof value.name !== "undefined" && value.name !== ""){
                            $container.find(".select2-result-repository__name").text("Institution: " + value.name);
                        }
                        if(typeof value.domain_name !== "undefined" && value.domain_name !== ""){
                            $container.find(".select2-result-repository__name").text("Domain name: " + value.domain_name);
                        }
                        $container.find(".select2-result-repository__canonical_name").text("Canonical Name: " + value.canonical_name);
                        var canonical_name = value.canonical_name.replace("'", "´");
                        $container.find(".select2-result-repository__meta").attr("onclick", "set_value('" + canonical_name + "')");
                        html += $container.html();
                    });
                    $("#suggesstion-box").removeClass("d-none");
                    $("#suggesstion-box").html(html);
                }else{
                    $("#suggesstion-box").addClass("d-none");
                }
            },complete: function() {
                $("#search-box").css("background", "#FFF");
            },
        });
    });
    function set_value(value){
        value = value.replace("´", "'");
        validateCanonical(value);
        $("#search-box").val(value);
        $("#search-box").attr("onchange", "test('" + value + "')");
        $("#suggesstion-box").addClass("d-none");
    }

    function validateCanonical(value = null){
        $.ajax({
            type: "get",
            url: "{{ route('organisations.validateCanonical') }}",
            data: {
                search: value,
                email: $("[name=email]").val()
            },
            beforeSend: function() {
                $("#search-box").css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
            },
            success: function(data) {
                if(data.error){
                    $("#search-box").addClass("is-invalid");
                    $("#search-box").removeClass("is-valid");
                    $("#invalid-canonical_name").removeClass("d-none");
                    $("#invalid-canonical_name").html(data.message);
                    $("#search-box").removeClass("search-box-correct");
                    $(".invalid-feedback-tooltips").removeClass("d-none");
                    $("#search-box").attr("data-validate", "false");
                }else{
                    $("#search-box").removeClass("is-invalid");
                    $("#search-box").addClass("is-valid");
                    $("#invalid-canonical_name").html("");
                    $("#search-box").addClass("search-box-correct");
                    $(".invalid-feedback-tooltips").addClass("d-none");
                    $("#search-box").attr("data-validate", "true")
                }
            },complete: function(r) {
                $("#search-box").css("background", "#FFF");
            },
        });
    }

    $("html").click(function() {
        $("#suggesstion-box").addClass("d-none");
    });

    $("#saveInstitution").on("click", function(e) {
        if($("#search-box").attr("data-validate") === "false"){
            e.preventDefault();
        }
    });

</script>

@endsection
