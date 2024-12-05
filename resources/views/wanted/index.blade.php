@extends('layouts.admin')

@section('header-content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                @if (Auth::user()->hasPermission('wanted-clients.create'))
                    <a href="{{ route('wanted.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Add
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterWanted">
                    <i class="fas fa-fw fa-search"></i> Filter
                </button>
                <a href="{{ route('wanted.showAll') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-window-restore"></i> Show all
                </a>
                @if (Auth::user()->hasPermission('wanted-clients.update'))
                    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                        <i class="fas fa-fw fa-edit"></i> Edit selection
                    </button>
                @endif
                @if (Auth::user()->hasPermission('wanted-clients.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn btn-light">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
                @if (Auth::user()->hasPermission('surplus-suppliers.read-lists'))
                    <button id="printOptions" type="button" class="btn btn-light" data-toggle="modal" data-target="#printOptionsDialog">
                        <i class="fas fa-fw fa-print"></i> Print
                    </button>
                @endif
                @if (Auth::user()->hasPermission('wanted-clients.export-survey'))
                    <a id="exportWantedRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportWanteds">
                        <i class="fas fa-fw fa-save"></i> Export
                    </a>
                @endif
            </div>

            <h1 class="h1 text-white"><i class="fas fa-fw fa-hand-paper mr-2"></i> {{ __('Wanted of clients') }}</h1>
            <p class="text-white">All wanted animals from you or your contacts are listed as supply</p>
        </div>
    </div>

    <div class="d-flex flex-row justify-content-between items-center text-white mb-2">
        <div class="d-flex align-items-center">
            <label style="font-size: 16px;">Order by:</label>&nbsp;
            {!! Form::open(['id' => 'wantedOrderByForm', 'route' => 'wanted.orderBy', 'method' => 'GET']) !!}
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
        @if (Auth::user()->hasPermission('surplus-suppliers.read-lists'))
            <div class="col-md-6 text-right">
                <div class="d-inline-flex">
                    <label class="text-white" style="font-size: 18px;">Wanted lists:</label>&nbsp;
                    <div id="selectWantedList">
                        <div class="d-inline-flex">
                            {!! Form::open(['id' => 'selectWantedListForm', 'route' => 'wanted.selectWantedList']) !!}
                            <select class="custom-select custom-select-sm w-auto" id="wantedListsTopSelect" name="wantedListsTopSelect">
                                <option @if(!isset($wantedListSelected)) selected @endif value="0">--Select list--</option>
                                @foreach ($wantedLists as $wantedList)
                                    <option value="{{ $wantedList->id }}" @if(isset($wantedListSelected) && $wantedList->id == $wantedListSelected->id) selected @endif>{{ $wantedList->name }}</option>
                                @endforeach
                            </select>
                            {!! Form::close() !!}
                        </div>
                        @if (Auth::user()->hasPermission('surplus-suppliers.create-lists'))
                            &nbsp;
                            <button type="button" id="addWantedList" class="btn btn-sm btn-light">
                                <i class="fas fa-fw fa-plus"></i>
                            </button>&nbsp;
                            <button type="button" id="deleteWantedList" class="btn btn-sm btn-light">
                                <i class="fas fa-fw fa-window-close"></i>
                            </button>
                        @endif
                    </div>
                    <div id="newWantedList" class="d-none">
                        <input type="text" id="wantedListName" class="form-control form-control-sm pt-0 w-auto d-inline-flex">&nbsp;
                        <button type="button" id="saveWantedList" class="btn btn-sm btn-light">
                            <i class="fas fa-fw fa-save"></i>
                        </button>&nbsp;
                        <button type="button" id="discardWantedList" class="btn btn-sm btn-light">
                            <i class="fas fa-fw fa-undo"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('main-content')

<div class="card shadow mb-1">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div class="d-flex flex-row items-center">
                <div class="d-flex align-items-center">
                  <input type="checkbox" id="selectAll" name="selectAll" />&nbsp;Select all
                  <input type="hidden" id="countWantedVisible" value="{{ ($wanteds->count() > 0) ? $wanteds->count() : 0 }}" />
                  <input type="hidden" id="countWantedTotal" value="{{ $wanteds->total() }}" />
                </div>
                <div class="d-flex align-items-center">
                    <span class="ml-3 mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('wanted.removeFromWantedSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
             </div>
            <div class="d-flex align-items-center">
               Page: {{$wanteds->currentPage()}} | Records:&nbsp;
               @if (Auth::user()->hasPermission('wanted-clients.see-all-wanted'))
                   {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'wanted.recordsPerPage', 'method' => 'GET']) !!}
                       {!! Form::text('recordsPerPage', $wanteds->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                   {!! Form::close() !!}
               @else
                   {{$wanteds->count()}}
               @endif
               &nbsp;| Total: {{$wanteds->total()}}
        </div>
        </div>
    </div>
</div>

<div class="card shadow mb-2">
    <div class="card-body">
        @unless($wanteds->isEmpty())
            <div class="table-responsive mb-2">
                <table class="table table-striped table-sm mb-0" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th class="border-top-0" style="width: 3%"></th>
                            <th class="border-top-0" style="width: 8%"></th>
                            <th class="border-top-0" style="width: 20%">Species</th>
                            <th class="border-top-0" style="width: 20%">Client institution</th>
                            <th class="border-top-0" style="width: 10%">Country</th>
                            <th class="border-top-0" style="width: 10%">Looking for</th>
                            <th class="border-top-0" style="width: 10%">Origin</th>
                            <th class="border-top-0" style="width: 10%">Age</th>
                            <th class="border-top-0" style="width: 10%">Level</th>
                            <th class="border-top-0" style="width: 19%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wanteds as $wanted)
                            <tr>
                                <td class="pr-0">
                                    <input type="checkbox" class="selector" value="{{ $wanted->id }}" />

                                    @if (Auth::user()->hasPermission('wanted-clients.read'))
                                        <a href="{{ route('wanted.show', [$wanted->id]) }}" class="ml-1" title="Show wanted"><i class="fas fa-search"></i></a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($wanted->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$wanted->animal->id.'/'.$wanted->animal->catalog_pic))
                                        <img src="{{ asset('storage/animals_pictures/'.$wanted->animal->id.'/'.$wanted->animal->catalog_pic) }}" class="rounded" style="max-width:70px;" alt="" />
                                    @else
                                        @if(!empty($wanted->animal->imagen_first))
                                            <img src="{{ asset('storage/animals_pictures/'.$wanted->animal->id.'/'.$wanted->animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                                        @else
                                            <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="card-title mb-0">{{ ($wanted->animal != null) ? $wanted->animal->common_name : '' }}</span>
                                    <span><em>({{ ($wanted->animal != null) ? $wanted->animal->scientific_name : '' }})</em></span>
                                </td>
                                <td>
                                    @if ($wanted->organisation != null)
                                        <span class="card-title mb-0">{{ $wanted->organisation->name }} <em>({{ $wanted->organisation->email }})</em></span>
                                    @else
                                        <span class="card-title mb-0 text-danger">INSTITUTION NOT DEFINED</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ ($wanted->organisation != null && !empty($wanted->organisation->country)) ? $wanted->organisation->country->name : '' }}
                                </td>
                                <td class="text-center">
                                    {{ $wanted->looking_field }}
                                </td>
                                <td class="text-center">
                                    {{ $wanted->origin_field }}
                                </td>
                                <td class="text-center">
                                    {{ $wanted->age_field }}
                                </td>
                                <td class="text-center">
                                    @if (!empty($wanted->organisation->level))
                                        {{ $wanted->organisation->level ?? "" }}
                                    @endif
                                </td>
                                <td>
                                    {{ $wanted->remarks }}
                                    @if ($wanted->intern_remarks != null)
                                        <div class="self-cursor">(<i class="fas fa-fw fa-info" title="Internal remarks: {{ $wanted->intern_remarks }}"></i>)</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (Auth::user()->hasPermission('wanted-clients.see-all-wanted'))
                {{$wanteds->links()}}
            @endif
        @else
            <p> No wanted of clients are added yet </p>
        @endunless
    </div>
</div>

@include('wanted.filter_modal', ['modalId' => 'filterWanted'])

@include('wanted.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

@include('print_dialogs.print_modal_small', ['modalId' => 'printOptionsDialog'])

@include('export_excel.export_options_modal', ['modalId' => 'exportWanteds'])

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $(':checkbox:checked.selector').prop('checked', false);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    $('#orderByField').on('change', function () {
        $('#wantedOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#wantedOrderByForm').submit();
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
                url:"{{ route('wanted.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    function strpad00(s) {
       s = s + '';
       if (s.length === 1) s = '0'+s;
       return s;
    }

    $('#addWantedList').on('click', function () {
        $('#newWantedList').removeClass("d-none");

        var d = new Date();
        var strDate = d.getFullYear() + "-" + strpad00((d.getMonth()+1))  + "-" + d.getDate();

        $('#wantedListName').val('List '+strDate);

        $('#selectWantedList').hide();
    });

    $('#discardWantedList').on('click', function () {
        $('#newWantedList').addClass("d-none");
        $('#selectWantedList').show();
    });

    $('#saveWantedList').on('click', function () {
        if($('#wantedListName').val().trim() == '') {
            alert("You must enter a name for the wanted list.");
            return;
        }

        $.ajax({
                type:'POST',
                url:"{{ route('wanted.saveWantedList') }}",
                data:{name: $('#wantedListName').val()},
                success:function(data){
                    location.reload();
                }
            });
    });

    $('#deleteWantedList').on('click', function () {
        if($('#wantedListsTopSelect').val().trim() == '') {
            alert("You must select a wanted list to delete.");
            return;
        }

        $.ajax({
                type:'POST',
                url:"{{ route('wanted.deleteWantedList') }}",
                data:{id: $('#wantedListsTopSelect').val()},
                success:function(data){
                    location.reload();
                }
            });
    });

    $('#wantedListsTopSelect').on('change', function () {
        $('#selectWantedListForm').submit();
    });

    $('#filterWanted').on('shown.bs.modal', function () {
        $('#filterWanted .animal-select2').val(null).trigger('change');

        $('#filterWanted input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterWanted input[name=empty_institution]:checkbox').trigger('change');

        $('#filterWanted input[name=empty_client]:checkbox').prop('checked', false);
        $('#filterWanted input[name=empty_client]:checkbox').trigger('change');

        $('#filterWanted .institution-select2').val(null).trigger('change');
        $('#filterWanted .contact-select2').val(null).trigger('change');

        $("#filterWanted input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterWanted input[name=filter_animal_option]").trigger('change');
    });

    $('#filterWanted').on('hidden.bs.modal', function () {
        $('#filterWanted .animal-select2').val(null).trigger('change');

        $('#filterWanted input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterWanted input[name=empty_institution]:checkbox').trigger('change');

        $('#filterWanted input[name=empty_client]:checkbox').prop('checked', false);
        $('#filterWanted input[name=empty_client]:checkbox').trigger('change');

        $('#filterWanted .institution-select2').val(null).trigger('change');
        $('#filterWanted .contact-select2').val(null).trigger('change');

        $("#filterWanted input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterWanted input[name=filter_animal_option]").trigger('change');

        $(this).find('form').trigger('reset');
    });

    $("#filterWanted #resetBtn").click(function() {
        $('#filterWanted .animal-select2').val(null).trigger('change');

        $('#filterWanted input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterWanted input[name=empty_institution]:checkbox').trigger('change');

        $('#filterWanted input[name=empty_client]:checkbox').prop('checked', false);
        $('#filterWanted input[name=empty_client]:checkbox').trigger('change');

        $('#filterWanted .institution-select2').val(null).trigger('change');
        $('#filterWanted .contact-select2').val(null).trigger('change');

        $("#filterWanted input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterWanted input[name=filter_animal_option]").trigger('change');

        $("#filterWanted").find('form').trigger('reset');
    });

    $('#filterWanted input[name=empty_client]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterWanted .contact-select2").val(null).trigger('change');
            $("#filterWanted .contact-select2").prop('disabled', true);
        }
        else
            $("#filterWanted .contact-select2").prop('disabled', false);
    });

    $('#editSelectedRecords').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        /*$(this).find('form')[0].reset();*/
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
            $.ajax({
                type:'POST',
                url:"{{ route('wanted.editSelectedRecords') }}",
                data:{
                    items: ids,
                    origin: $('#editSelectedRecords [name=edit_selection_origin]').val(),
                    age_group: $('#editSelectedRecords [name=edit_selection_age_group]').val(),
                    add_to_wanted_lists: $('#editSelectedRecords [name=selectionAddToWantedLists]').val(),
                    remove_from_wanted_lists: $('#editSelectedRecords [name=selectionRemoveFromWantedLists]').val(),
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#filterWanted input[name=filter_animal_option]').change(function() {
        var checkedOption = $('#filterWanted input[name=filter_animal_option]:checked').val();

        if (checkedOption == 'by_name') {
            $("#filterWanted [name=filter_animal_name]").val('');
            $("#filterWanted [name=filter_animal_name]").prop('disabled', false);
            $("#filterWanted .animal-select2").prop('disabled', true);
            $("#filterWanted .animal-select2").val(null).trigger('change');
        }
        else if (checkedOption == 'by_id') {
            $("#filterWanted [name=filter_animal_name]").val('');
            $("#filterWanted [name=filter_animal_name]").prop('disabled', true);
            $("#filterWanted .animal-select2").prop('disabled', false);
        }
        else {
            $("#filterWanted [name=filter_animal_name]").val('');
            $("#filterWanted [name=filter_animal_name]").prop('disabled', true);
            $("#filterWanted .animal-select2").prop('disabled', true);
            $("#filterWanted .animal-select2").val(null).trigger('change');
        }
    });

    //Select2 animal selection
    $('[name=filter_animal_id]').on('change', function () {
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
                    $('[name=filter_animal_id]').append(newOption);
                }
            });
        }
    });

    //Select2 filter institution
    $('[name=filter_institution_id]').on('change', function () {
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
                    $('[name=filter_institution_id]').append(newOption);
                }
            });
        }
    });

    $('#filterWanted input[name=empty_institution]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterWanted .institution-select2").val(null).trigger('change');
            $("#filterWanted .institution-select2").prop('disabled', true);
        }
        else
            $("#filterWanted .institution-select2").prop('disabled', false);
    });

    $('#filterWanted input[name=empty_client]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterWanted .contact-select2").val(null).trigger('change');
            $("#filterWanted .contact-select2").prop('disabled', true);
        }
        else
            $("#filterWanted .contact-select2").prop('disabled', false);
    });

    $("#filter_animal_class").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getOrdersByClass') }}",
            data:{value: value},
            success:function(data){
                $("#filter_animal_order").empty();
                $('#filter_animal_order').append('<option value="">- select -</option>');
                $.each(data.orders, function(key, value) {
                    $('#filter_animal_order').append('<option value="'+ value +'">'+ key +'</option>');
                });
                $("#filter_animal_order").change();
            }
        });
    });

    $("#filter_animal_order").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getFamiliesByOrder') }}",
            data:{value: value},
            success:function(data){
                $("#filter_animal_family").empty();
                $('#filter_animal_family').append('<option value="">- select -</option>');
                $.each(data.families, function(key, value) {
                    $('#filter_animal_family').append('<option value="'+ value +'">'+ key +'</option>');
                });
                $("#filter_animal_family").change();
            }
        });
    });

    $("#filter_animal_family").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getGenusByFamily') }}",
            data:{value: value},
            success:function(data){
                $("#filter_animal_genus").empty();
                $('#filter_animal_genus').append('<option value="">- select -</option>');
                $.each(data.genuss, function(key, value) {
                    $('#filter_animal_genus').append('<option value="'+ value +'">'+ key +'</option>');
                });
            }
        });
    });

   $('#printOptions').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countWantedVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $("#printOptionsDialog [name=print_document_type],[name=print_prices],[name=print_language],[name=print_pictures],[name=print_wanted],[name=print_stuffed]").prop('checked', false);
        $("#printOptionsDialog [name=print_wanted_list]").val('');
        $("#printOptionsDialog [name=print_wanted_list]").prop('disabled', true);
        $("#printOptionsDialog [name=filter_client_id]").html('');
        $("#printOptionsDialog .print_client").addClass('d-none');

        $('#printOptionsDialog').modal('show');
    });

    $('#printOptionsDialog').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#printOptionsDialog [name=export_option]:checked').val();

        var ids = [];
        if(export_option == "selection") {
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }
        else if (export_option == "page") {
            $(".selector").each(function(){
                ids.push($(this).val());
            });
        }

        if (ids.length == $('#countWantedTotal').val()) {
            export_option = "all";
            ids = [];
        }

        var document_type = $('#printOptionsDialog [name=print_document_type]:checked').val();
        var prices = $('#printOptionsDialog [name=print_prices]:checked').val();
        var language = $('#printOptionsDialog [name=print_language]:checked').val();
        var pictures = $('#printOptionsDialog [name=print_pictures]:checked').val();
        var stuffed = $('#printOptionsDialog [name=print_stuffed]:checked').val();
        var wanted_list = $('#printOptionsDialog [name=print_wanted_list]').val();
        var filter_client_id = $('#printOptionsDialog [name=filter_client_id]').val();

        if (export_option != 'all' && ids.length == 0)
            alert("There are not records to export.");
        else if (document_type == null || prices == null || language == null || pictures == null)
            alert("The options: Document, Prices, Language and Pictures, must be marked.");
        else if (export_option != 'all' && ids.length > 300)
            alert("You cannot print more than 300 records. Please select HTML file and option 'All records'.");
        else {
            var printOptionsDialogButton = $("#printOptionsDialogButton").html();
            $("#printOptionsDialogButton").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'GET',
                url:"{{ route('wanted.printWantedList') }}",
                data:{
                    export_option: export_option,
                    document_type: document_type,
                    prices: prices,
                    language: language,
                    pictures: pictures,
                    wanted_list: wanted_list,
                    items: ids,
                    print_stuffed: stuffed,
                    filter_client_id: filter_client_id
                },
                success: function(response){
                    if (response.success) {
                        $('#printOptionsDialog').modal('hide');

                        $.NotificationApp.send("Success message!", "Wanted data was downloaded", 'top-right', '#fff', 'success');

                        var link = document.createElement('a');

                        link.href = window.URL = response.url;

                        link.download = response.fileName;

                        link.click();
                    }
                    else
                        alert(response.message);
                },complete: function() {
                    $("#printOptionsDialogButton").html(printOptionsDialogButton);
                },
            });
        }
    });

    $('#selectionAddToWantedLists').multiselect({
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

    $('#selectionRemoveFromWantedLists').multiselect({
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

    $('#exportWantedRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countWantedVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportWanteds').modal('show');
    });

    $('#exportWanteds').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportWanteds [name=export_option]:checked').val();

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
            var url = "{{route('wanted.export')}}?items=" + ids;
            window.location = url;

            $('#exportWanteds').modal('hide');
        }
    });

});

</script>

@endsection
