@extends('layouts.admin')

@section('header-content')

    <div class="row mb-2">
        <div class="col-md-2">
            <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Contacts') }}</h1>
        </div>
        <div class="col-md-10 text-right">
            <div class="mb-2">
                @if (Auth::user()->hasPermission('contacts.create'))
                    <a id="create-button" href="{{ route('contacts.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Create
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterContacts">
                    <i class="fas fa-fw fa-filter"></i> Filter
                </button>
                @if (!Auth::user()->hasRole('office'))
                    <a href="{{ route('contacts.doublesView') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-clone"></i> Find doubles
                    </a>
                @endif
                <a href="{{ route('contacts.showAll') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-window-restore"></i> Show all
                </a>
                @if (Auth::user()->hasPermission('contacts.update'))
                    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                        <i class="fas fa-fw fa-edit"></i> Edit selection
                    </button>
                @endif
                @if (Auth::user()->hasPermission('contacts.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn btn-light">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
                @if (Auth::user()->hasPermission('contacts.export-contacts'))
                    <a id="exportContactRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportContacts">
                        <i class="fas fa-fw fa-save"></i> Export
                    </a>
                @endif
                @if (Auth::user()->hasPermission('contacts.contacts-address-list'))
                    <a id="createMailingAddressList" href="#" class="btn btn-light" data-toggle="modal" data-target="#createAddressList">
                        <i class="fas fa-fw fa-save"></i> Address list
                    </a>
                @endif
                <a href="{{ route('contacts.contactsSendEmail') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-envelope"></i> Send email
                </a>
                <a href="{{ route('organisations.showNewAnimals') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-envelope"></i> Send new animals to A-level institutions
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex flex-row justify-content-between items-center text-white mb-2">
        <div class="d-flex align-items-center">
            <label class="text-sm pr-2 pt-1">Order by:</label>
            {!! Form::open(['id' => 'contactsOrderByForm', 'route' => 'contacts.filterContacts', 'method' => 'GET']) !!}
                <select class="custom-select custom-select-sm w-auto" id="orderByField" name="orderByField">
                    @foreach ($orderByOptions as $orderByKey => $orderByValue)
                        <option value="{{ $orderByKey }}" @if(isset($orderByField) && $orderByField == $orderByKey) selected @endif>{{$orderByValue}}</option>
                    @endforeach
                </select>
                <select id="orderByDirection" name="orderByDirection" class="custom-select custom-select-sm w-auto">
                    <option @if(!isset($orderByDirection)) selected @endif value="desc">Descending</option>
                    <option @if(isset($orderByDirection) && $orderByDirection == 'asc') selected @endif value="asc">Ascending</option>
                </select>
            {!! Form::close() !!}
        </div>

        <div class="d-flex align-items-center">
            Page: {{$contacts->currentPage()}} | Records:&nbsp;
            @if (Auth::user()->hasPermission('contacts.see-all-contacts'))
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'contacts.recordsPerPage', 'method' => 'GET']) !!}
                    {!! Form::text('recordsPerPage', $contacts->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
            @else
                {{$contacts->count()}}
            @endif
            &nbsp;| Total: {{$contacts->total()}}
        </div>
    </div>

    @if (Auth::user()->hasPermission('contacts.see-all-contacts'))
        <div class="float-right ml-2">
            {{$contacts->links()}}
        </div>
    @endif
@endsection

@section('main-content')

<div class="card shadow mb-2">
    <div class="card-body">
        <div class="d-flex flex-row items-center">
            <div class="d-flex align-items-center">
                <input type="checkbox" id="selectAll" name="selectAll" />&nbsp;Select all
                <input type="hidden" id="countContactsVisible" value="{{ ($contacts->count() > 0) ? $contacts->count() : 0 }}" />
            </div>

            <div class="d-flex align-items-center">
                <span class="ml-3 mr-1">Filtered on:</span>
                @foreach ($filterData as $key => $value)
                    <a href="{{ route('contacts.removeFromContactSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-2">
    <div class="card-body">
        @unless($contacts->isEmpty())
            <div class="table-responsive mb-2">
                <table class="table table-striped table-sm mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="border-top-0" style="width: 4%"></th>
                            <th class="border-top-0">Name</th>
                            <th class="border-top-0">Institution</th>
                            <th class="border-top-0" style="width: 5%">Type</th>
                            <th class="border-top-0" style="width: 5%">Active in</th>
                            <th class="border-top-0">Mobile</th>
                            <th class="border-top-0">City</th>
                            <th class="border-top-0">Country</th>
                            <th class="border-top-0">Email address</th>
                            <th class="border-top-0">Website</th>
                            <th class="border-top-0" style="width: 5%">Level</th>
                            <th class="border-top-0">Mailing category</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr @if($contact->source == 'website') style="color: red;" @endif>
                                <td class="pr-0">
                                    <input type="checkbox" class="selector" value="{{ $contact->id }}" />
                                    @if (Auth::user()->hasPermission('contacts.read'))
                                        <a href="{{ route('contacts.show', [$contact->id]) }}" title="Show contact"><i class="fas fa-search"></i></a>
                                    @endif
                                </td>
                                <td style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;">{{ $contact->full_name }}</td>
                                <td style="word-wrap: break-word; min-width: 140px;max-width: 140px; white-space:normal;">@if($contact->organisation) {{ $contact->organisation->name }} @else - @endif</td>
                                <td>
                                    @if($contact->organisation && $contact->organisation->type)
                                        <span class="self-cursor" title="{{ $contact->organisation->type->label }}">{{ $contact->organisation->type->key }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><span class="self-cursor" title="{{ $contact->active_state_str }}">{{ $contact->active_state }}</span></td>
                                <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">{{ $contact->mobile_phone }}</td>
                                <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">{{ $contact->city }}</td>
                                <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">@if($contact->country) {{ $contact->country->name }} @else - @endif</td>
                                <td style="word-wrap: break-word; min-width: 160px;max-width: 160px; white-space:normal;"><a href="mailto:{{ $contact->email }}"><u>{{ $contact->email }}</u></a></td>
                                <td style="word-wrap: break-word; min-width: 160px;max-width: 160px; white-space:normal;">@if($contact->organisation) <a href="//{{$contact->organisation->website}}" target="_blank"><u>{{ $contact->organisation->website }}</u></a> @else - @endif</td>
                                <td>
                                    @if($contact->organisation)
                                        <p class="level-{{$contact->organisation->level ?? ""}}">{{$contact->organisation->level}}</p>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">{{ $contact->mailing }}</td>
                            </tr>
                            <tr><td colspan="12"></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (Auth::user()->hasPermission('contacts.see-all-contacts'))
                <div class="float-right">
                    {{$contacts->links()}}
                </div>
            @endif
        @else
            <p> No contacts are added yet </p>
        @endunless
    </div>
</div>

  @include('contacts.filter_modal', ['modalId' => 'filterContacts'])

  @include('contacts.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

  @include('export_excel.export_options_modal', ['modalId' => 'exportContacts'])

  @include('contacts.address_list_modal', ['modalId' => 'createAddressList'])

@endsection

@section('page-scripts')

<style>

    .preflightOptionContainer {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        row-gap: 10px;
    }

    .preflightOption {
        text-decoration: underline;
        font-weight: 500;
        padding: 12px;
        border-radius: 9px;
        width: 60%;
        cursor: pointer;
    }

    .preflightOption:hover {
        color: #bdbdbd;
    }
</style>

<script type="text/javascript">
    const preflightCreationCheck = (e) => {
        e.preventDefault();
        e.stopImmediatePropagation();

        let formHtml = `
                <p>Check if the contact exists before creating.</p>
                <div class="modal-body" style="text-align: left;">
                    {!! Form::label('name', 'First name *') !!}
                    {!! Form::text('preflight_check_first_name', null, ['id' => 'preflight_check_first_name', 'class' => 'form-control', 'required']) !!}

                    {!! Form::label('name', 'Last name *') !!}
                    {!! Form::text('preflight_check_last_name', null, ['id' => 'preflight_check_last_name', 'class' => 'form-control', 'required']) !!}

                    {!! Form::label('domain', 'Domain') !!}
                    {!! Form::text('preflight_check_domain', null, ['id' => 'preflight_check_domain', 'class' => 'form-control', 'required']) !!}

                    {!! Form::label('city', 'City *') !!}
                    {!! Form::text('preflight_check_city', null, ['id' => 'preflight_check_city', 'class' => 'form-control', 'required']) !!}

                    {!! Form::label('country', 'Country *') !!}
                    {!! Form::select('preflight_check_country', $countries, null, ['id' => 'preflight_check_country', 'class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                </div>
            `;

        Swal.fire({
            title: "Pre-creation check",
            html: formHtml,
            confirmButtonText: "Check for existing institutes",
            preConfirm: function () {
                return new Promise(function (resolve) {
                    resolve([
                        document.getElementById('preflight_check_first_name').value,
                        document.getElementById('preflight_check_last_name').value,
                        document.getElementById('preflight_check_domain').value,
                        document.getElementById('preflight_check_city').value,
                        document.getElementById('preflight_check_country').value,
                    ])
                })
            },
        }).then((result) => {
            let firstName = result.value[0];
            let lastName = result.value[1];
            let domain = result.value[2];
            let city = result.value[3];
            let country = result.value[4];

            let url = "{{ route('contacts.checkForExistence') }}";
            url += `?first_name=${firstName}&last_name=${lastName}&domain=${domain}&city=${city}&country=${country}`;

            axios.get(url)
                .then(res => {
                    let organisations = res.data.organisations;
                    let contacts = res.data.contacts;

                    if (organisations.length || contacts.length) {
                        let selectHtml = "<div><p>Click on the institute to view it</p>";

                        if (organisations.length) {
                            selectHtml += "<div class='preflightOptionContainer'>";
                            organisations.forEach(organisation => {
                                selectHtml += `
                                    <a href="/organisations/${organisation.id}" class='preflightOption'>
                                         ${organisation.name}
                                    </a>`;
                            });
                            selectHtml += "</div>";
                        }

                        if (contacts.length) {
                            selectHtml += "<h2>Contacts</h2>";
                            selectHtml += "<div class='preflightOptionContainer'>";
                            contacts.forEach(contact => {
                                selectHtml += `
                                    <a href="/contacts/${contact.id}" class='preflightOption'>
                                         ${contact.first_name ?? ""} ${contact.last_name ?? ""}
                                    </a>`;
                            });
                            selectHtml += "</div>";
                        }

                        selectHtml += "</div>";

                        Swal.fire({
                            title: "Already exists",
                            html: selectHtml,
                            showConfirmButton: true,
                            confirmButtonText: "Close"
                        });
                    } else {
                        Swal.fire({
                            title            : "Create contact or institute?",
                            confirmButtonText: "Contact",
                            denyButtonText   : "Institute",
                            cancelButtonText : "Close",
                            showDenyButton: true,
                            showCancelButton: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = `{{ route('contacts.create') }}?preset=1&first_name=${firstName}&last_name=${lastName}&domain=${domain}&city=${city}&country_id=${country}`;
                            } else if (result.isDenied) {
                                window.location.href = `{{ route('organisations.create') }}?preset=1&name=${firstName} ${lastName}&domain=${domain}&city=${city}&country_id=${country}`;
                            }
                        });
                    }
                })
        });
    }

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
        document.getElementById('create-button').addEventListener('click', preflightCreationCheck);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    $('#recordsPerPage').on('keypress', function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13')
            $('#recordsPerPageForm').submit();

        event.stopPropagation();
    });

    $('#orderByField').on('change', function () {
        $('#contactsOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#contactsOrderByForm').submit();
    });

    $('#filterContacts input[name=filter_name_empty]:checkbox').change(function () {
        if($(this).is(':checked'))
            $("#filterContacts input[name=filter_name]").prop('disabled', true);
        else
            $("#filterContacts input[name=filter_name]").prop('disabled', false);
    });

    $('#filterContacts input[name=filter_email_empty]:checkbox').change(function () {
        if($(this).is(':checked'))
            $("#filterContacts input[name=filter_email]").prop('disabled', true);
        else
            $("#filterContacts input[name=filter_email]").prop('disabled', false);
    });

    $('#filterContacts input[name=filter_institution_empty]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterContacts [name=filter_institution_type]").prop('disabled', true);
            $("#filterContacts input[name=filter_institution_name]").prop('disabled', true);
        }
        else {
            $("#filterContacts [name=filter_institution_type]").prop('disabled', false);
            $("#filterContacts input[name=filter_institution_name]").prop('disabled', false);
        }
    });

    $('#filterContacts input[name=filter_city_empty]:checkbox').change(function () {
        if($(this).is(':checked'))
            $("#filterContacts input[name=filter_city]").prop('disabled', true);
        else
            $("#filterContacts input[name=filter_city]").prop('disabled', false);
    });

    $("#filterContacts #resetBtn").click(function() {
        $("#filterContacts input[name=filter_name]").prop('disabled', false);
        $("#filterContacts input[name=filter_email]").prop('disabled', false);
        $("#filterContacts [name=filter_institution_type]").prop('disabled', false);
        $("#filterContacts input[name=filter_institution_name]").prop('disabled', false);
        $('#filterContacts .animal-select2').val(null).trigger('change');
        $("#filterContacts").find('form').trigger('reset');
    });

    //Select2 animal selection
    $('#filterContacts [name=filter_animal_id]').on('change', function () {
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
                    $('#filterContacts [name=filter_animal_id]').append(newOption);
                }
            });
        }
    });

    //Select2 level updated
    $('.level_type').on('change', function () {
        var level = $(this).val();
        var id = $(this).attr("data_organisation_id");
        var value_old = $(this).attr("data_value_old");
        $(this).removeClass("level-" + value_old);
        $(this).addClass("level-" + level);
        $(this).attr("data_value_old", level);
        if(level !== null && id !== null) {
            $.ajax({
                type:'POST',
                url:"{{ route('organisations.editLevel') }}",
                data: {
                    id: id,
                    level: level
                },
                success:function(data) {
                    if(typeof data.message != "undefined"){
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                    }
                }
            });
        }
    });

    //Select2 institution client selection
    $('#editSelectedRecords [name=institution_id]').on('change', function () {
        var institutionId = $(this).val();

        if(institutionId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.institution-by-id') }}",
                data: {
                    id: institutionId,
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.institution.name.trim(), data.institution.id, true, true);
                    // Append it to the select
                    $('#editSelectedRecords [name=institution_id]').append(newOption);
                }
            });
        }
    });

    $('#editSelectedRecords').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#editSelectedRecords input[name=institution_name]').prop('disabled', false);
        $('#editSelectedRecords [name=country_id]').prop('disabled', false);
    });

    $('#editSelectedRecords input[name=makeInstitutionNameBasedOnCityAndType]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $('#editSelectedRecords input[name=institution_name]').val(null);
            $('#editSelectedRecords input[name=institution_name]').prop('disabled', true);
        }
        else
            $('#editSelectedRecords input[name=institution_name]').prop('disabled', false);
    });

    $('#editSelectedRecords input[name=makeCountryBasedOnEmailExtension]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $('#editSelectedRecords [name=country_id]').val(null);
            $('#editSelectedRecords [name=country_id]').prop('disabled', true);
        }
        else
            $('#editSelectedRecords [name=country_id]').prop('disabled', false);
    });

    $('#sendEditSelectionForm').on('click', function(event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to edit.");
        else {
            $(".modal-footer").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('contacts.editSelectedRecords') }}",
                data:{
                    items: ids,
                    title: $('#editSelectedRecords [name=title]').val(),
                    first_name: $('#editSelectedRecords [name=first_name]').val(),
                    last_name: $('#editSelectedRecords [name=last_name]').val(),
                    city: $('#editSelectedRecords [name=city]').val(),
                    country_id: $('#editSelectedRecords [name=country_id]').val(),
                    institution_id: $('#editSelectedRecords [name=institution_id]').val(),
                    institution_name: $('#editSelectedRecords [name=institution_name]').val(),
                    institution_level: $('#editSelectedRecords [name=institution_level]').val(),
                    make_institution_name: $('#editSelectedRecords [name=makeInstitutionNameBasedOnCityAndType]').is(":checked") ? 1 : 0,
                    make_country: $('#editSelectedRecords [name=makeCountryBasedOnEmailExtension]').is(":checked") ? 1 : 0,
                    level: $('#editSelectedRecords [name=level]').val(),
                    mailing_category: $('#editSelectedRecords [name=mailing_category]').val()
                },
                success:function(data) {
                    if (data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#5ba035', 'success');
                        location.reload();
                    }
                }
            });
        }
    });

    var contacts_email_url = "{{ route('api.contacts-email') }}";

    $.get(contacts_email_url , function(data, status){
        $("#filter_email").autocomplete({
            source: data,
            treshold: 3,
            highlightClass: 'text-danger'
        });
    });

    $('#deleteSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('contacts.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    if (data.message)
                        alert(data.message);

                    location.reload();
                }
            });
        }
    });

    $('#exportContactRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countContactsVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportContacts').modal('show');
    });

    $('#exportContacts').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#exportContacts').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportContacts [name=export_option]:checked').val();
        var file_type = $('#exportContacts [name=file_option]:checked').val();

        var ids = [];
        if(export_option == "selection") {
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }
        else {
            $(".selector").each(function(){
                ids.push($(this).val());
            });
        }

        if(ids.length == 0)
            alert("There are not records to export.");
        else {
            var url = "{{route('contacts.export')}}?items=" + ids + "&file_type=" + file_type;
            window.location = url;

            $('#exportContacts').modal('hide');
        }
    });

    $('#createMailingAddressList').on('click', function () {
        $('#createAddressList').modal('show');

        $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
        $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

        $('#createAddressList [name=exclude_continents]').prop('disabled', false);
        $('#createAddressList [name=exclude_countries]').prop('disabled', false);
    });

    $('#createAddressList').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');

        $("#createAddressList [name=institution_type_selection_all]").prop('checked', false);
        $("#createAddressList [name=institution_type_selection_all]").trigger('change');
    });

    $("#createAddressList [name=institution_type_selection_all]").change( function() {
        if($(this).prop('checked')) {
            $('#createAddressList [name=institution_type_selection]').each(function() {
                $(this).prop('checked', false);
                $(this).prop('disabled', true);
            });
        }
        else {
            $('#createAddressList [name=institution_type_selection]').each(function () {
                $(this).prop('checked', false);
                $(this).prop('disabled', false);
            });
        }
    });

    $("#createAddressList [name=world_region]").change( function() {
        var world_region = $(this).val();

        if (world_region == 'area') {
            $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
            $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

            $('#createAddressList [name=exclude_continents]').prop('disabled', false);
            $('#createAddressList [name=exclude_countries]').prop('disabled', false);
        }
        else if (world_region == 'region') {
            $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
            $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

            $('#createAddressList [name=exclude_continents]').prop('disabled', true);
            $('#createAddressList [name=exclude_countries]').prop('disabled', false);
        }
        else {
            $('#createAddressList [name=exclude_continents]').val(null).trigger('change');
            $('#createAddressList [name=exclude_countries]').val(null).trigger('change');

            $('#createAddressList [name=exclude_continents]').prop('disabled', true);
            $('#createAddressList [name=exclude_countries]').prop('disabled', true);
        }

        $.ajax({
            type:'POST',
            url:"{{ route('api.getWorldRegionData') }}",
            data:{
                value: world_region,
            },
            success:function(data) {
                if(data.success) {
                    $('[name=world_region_selection]').empty();
                    $('[name=world_region_selection]').append('<option value="0">All</option>');
                    $.each(data.cmbData, function(key, value) {
                        $('[name=world_region_selection]').append('<option value="'+ key +'">' + value +'</option>');
                    });
                }
            }
        });
    });

    $('#createAddressList').on('submit', function (event) {
        event.preventDefault();

        var language_option = $('#createAddressList [name=language_option]:checked').val();
        var level = $('#createAddressList [name=select_institution_level]').val();
        var world_region = $('#createAddressList [name=world_region]:checked').val();
        var world_region_selection = $('#createAddressList [name=world_region_selection]').val();
        var institution_type_selection_all = $('#createAddressList [name=institution_type_selection_all]').is(':checked');

        var institution_types = [];
        $('#createAddressList [name=institution_type_selection]:checked').each( function() {
            institution_types.push($(this).val());
        });

        var exclude_continents = $('#createAddressList [name=exclude_continents]').val();

        var exclude_countries = $('#createAddressList [name=exclude_countries]').val();

        if(!institution_type_selection_all && institution_types.length == 0)
            alert("You must select at least one institution type option.");
        else {
            if(institution_type_selection_all)
                var url = "{{route('contacts.createContactAddressList')}}?language=" + language_option +
                    "&level=" + level +
                    "&world_region=" + world_region +
                    "&world_region_selection=" + world_region_selection +
                    "&exclude_continents=" + exclude_continents +
                    "&exclude_countries=" + exclude_countries;
            else
                var url = "{{route('contacts.createContactAddressList')}}?itypes=" + institution_types +
                    "&language=" + language_option +
                    "&level=" + level +
                    "&world_region=" + world_region +
                    "&world_region_selection=" + world_region_selection +
                    "&exclude_continents=" + exclude_continents +
                    "&exclude_countries=" + exclude_countries;
            /*alert(url);*/
            window.location = url;

            $('#createAddressList').modal('hide');
        }
    });

    $("#createAddressList #resetBtn").click(function() {
        $("#createAddressList").find('form').trigger('reset');

        $("#createAddressList [name=institution_type_selection_all]").prop('checked', false);
        $("#createAddressList [name=institution_type_selection_all]").trigger('change');

        $("#createAddressList [name=world_region]").val('area');
        $("#createAddressList [name=world_region]").trigger('change');
    });

</script>

@endsection
