@extends('layouts.admin')

@section('header-content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                @if (Auth::user()->hasPermission('standard-wanted.read'))
                    <a href="{{ route('our-wanted.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Add
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterOurWanted">
                    <i class="fas fa-fw fa-search"></i> Filter
                </button>
                <a href="{{ route('our-wanted.showAll') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-window-restore"></i> Show all
                </a>
                @if (Auth::user()->hasPermission('standard-wanted.update'))
                    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                        <i class="fas fa-fw fa-edit"></i> Edit selection
                    </button>
                @endif
                @if (Auth::user()->hasPermission('standard-wanted.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn  btn-light">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
                @if (Auth::user()->hasPermission('standard-wanted.read-lists'))
                    <button id="printOptions" type="button" class="btn btn-light" data-toggle="modal" data-target="#printOptionsDialog">
                        <i class="fas fa-fw fa-print"></i> Print
                    </button>
                @endif
                @if (Auth::user()->hasPermission('standard-wanted.export-survey'))
                    <a id="exportStandardWantedRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportStandardWanteds">
                        <i class="fas fa-fw fa-save"></i> Export
                    </a>
                @endif
            </div>

            <h1 class="h1 text-white"><i class="fas fa-fw fa-hand-paper mr-2"></i> {{ __('Our wanted') }}</h1>
            <p class="text-white">All our wanted animals.</p>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 d-inline-flex">
            <label class="text-white" style="font-size: 16px;">Order by:</label>&nbsp;
            {!! Form::open(['id' => 'standardWantedOrderByForm', 'route' => 'our-wanted.orderBy', 'method' => 'GET']) !!}
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
        @if (Auth::user()->hasPermission('standard-wanted.read-lists'))
            <div class="col-md-6 text-right">
                <div class="d-inline-flex">
                    <label class="text-white" style="font-size: 18px;">Our wanted lists:</label>&nbsp;
                    <div id="selectOurWantedList">
                        <div class="d-inline-flex">
                            {!! Form::open(['id' => 'selectOurWantedListForm', 'route' => 'our-wanted.selectOurWantedList']) !!}
                            <select class="custom-select custom-select-sm w-auto" id="ourWantedListsTopSelect" name="ourWantedListsTopSelect">
                                <option @if(!isset($ourWantedListSelected)) selected @endif value="0">--Select list--</option>
                                @foreach ($ourWantedLists as $ourWantedList)
                                    <option value="{{ $ourWantedList->id }}" @if(isset($ourWantedListSelected) && $ourWantedList->id == $ourWantedListSelected->id) selected @endif>{{ $ourWantedList->name }}</option>
                                @endforeach
                            </select>
                            {!! Form::close() !!}
                        </div>
                        @if (Auth::user()->hasPermission('standard-wanted.create-lists'))
                            &nbsp;
                            <button type="button" id="addOurWantedList" class="btn btn-sm btn-light">
                                <i class="fas fa-fw fa-plus"></i>
                            </button>&nbsp;
                            <button type="button" id="deleteOurWantedList" class="btn btn-sm btn-light">
                                <i class="fas fa-fw fa-window-close"></i>
                            </button>
                        @endif
                    </div>
                    <div id="newOurWantedList" class="d-none">
                        <input type="text" id="ourWantedListName" class="form-control form-control-sm pt-0 w-auto d-inline-flex">&nbsp;
                        <button type="button" id="saveOurWantedList" class="btn btn-sm btn-light">
                            <i class="fas fa-fw fa-save"></i>
                        </button>&nbsp;
                        <button type="button" id="discardOurWantedList" class="btn btn-sm btn-light">
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
                    <input type="hidden" id="countOurWantedVisible" value="{{ ($ourWanteds->count() > 0) ? $ourWanteds->count() : 0 }}" />
                    <input type="hidden" id="countOurWantedTotal" value="{{ $ourWanteds->total() }}" />
                </div>
                <div class="d-flex align-items-center">
                    <span class="ml-3 mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('our-wanted.removeFromOurWantedSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
            </div>
            <div class="d-flex align-items-center">
                Page: {{$ourWanteds->currentPage()}} | Records:&nbsp;
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'our-wanted.recordsPerPage', 'method' => 'GET']) !!}
                    {!! Form::text('recordsPerPage', $ourWanteds->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
                &nbsp;| Total: {{$ourWanteds->total()}}
            </div>
        </div>
    </div>
</div>

@unless($ourWanteds->isEmpty())
    <div class="table-responsive mb-2">
        <table class="table table-striped table-sm mb-0" width="100%" cellspacing="0">
            <thead>
                <tr class="text-center">
                    <th class="border-top-0" style="width: 3%"></th>
                    <th class="border-top-0" style="width: 8%"></th>
                    <th class="border-top-0" style="width: 20%">Species</th>
                    <th class="border-top-0" style="width: 13%">Looking for</th>
                    <th class="border-top-0" style="width: 13%">Origin</th>
                    <th class="border-top-0" style="width: 13%">Age</th>
                    <th class="border-top-0" style="width: 15%">Wanted to</th>
                    <th class="border-top-0" style="width: 15%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ourWanteds as $ourWanted)
                    <tr>
                        <td class="pr-0">
                            <input type="checkbox" class="selector" value="{{ $ourWanted->id }}" />

                            @if (Auth::user()->hasPermission('standard-wanted.read'))
                                <a href="{{ route('our-wanted.show', [$ourWanted->id]) }}" class="ml-1" title="Show standard wanted"><i class="fas fa-search"></i></a>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($ourWanted->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$ourWanted->animal->id.'/'.$ourWanted->animal->catalog_pic))
                                <img src="{{ asset('storage/animals_pictures/'.$ourWanted->animal->id.'/'.$ourWanted->animal->catalog_pic) }}" class="rounded" style="max-width:70px;" alt="" />
                            @else
                                <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                            @endif
                        </td>
                        <td>
                            <span class="card-title mb-0">{{ ($ourWanted->animal != null) ? $ourWanted->animal->common_name : '' }}</span>
                            <span><em>({{ ($ourWanted->animal != null) ? $ourWanted->animal->scientific_name : '' }})</em></span>
                        </td>
                        <td class="text-center">
                            {{ $ourWanted->looking_field }}
                        </td>
                        <td class="text-center">
                            {{ $ourWanted->origin_field }}
                        </td>
                        <td class="text-center">
                            {{ $ourWanted->age_field }}
                        </td>
                        <td>
                            @foreach($ourWanted->area_regions as $area_region)
                                <label class="checkbox-inline ml-2">
                                    {{$area_region->short_cut}}
                                </label>
                            @endforeach
                        </td>
                        <td>
                            {{ $ourWanted->remarks }}
                            @if ($ourWanted->intern_remarks != null)
                                <div class="self-cursor">(<i class="fas fa-fw fa-info" title="Internal remarks: {{ $ourWanted->intern_remarks }}"></i>)</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{$ourWanteds->links()}}
@else
    <p> No standard wanted are added yet </p>
@endunless

@include('our_wanted.filter_modal', ['modalId' => 'filterOurWanted'])

@include('our_wanted.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

@include('print_dialogs.print_modal_small', ['modalId' => 'printOptionsDialog'])

@include('export_excel.export_options_modal', ['modalId' => 'exportStandardWanteds'])

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
        $('#standardWantedOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#standardWantedOrderByForm').submit();
    });

    function strpad00(s) {
        s = s + '';
        if (s.length === 1) s = '0'+s;
        return s;
    }

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
                url:"{{ route('our-wanted.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#addOurWantedList').on('click', function () {

        $('#newOurWantedList').removeClass("d-none");

        var d = new Date();
        var strDate = d.getFullYear() + "-" + strpad00((d.getMonth()+1))  + "-" + d.getDate();

        $('#ourWantedListName').val('List '+strDate);

        $('#selectOurWantedList').hide();
    });

    $('#discardOurWantedList').on('click', function () {
        $('#newOurWantedList').addClass("d-none");
        $('#selectOurWantedList').show();
    });

    $('#saveOurWantedList').on('click', function () {

        if($('#ourWantedListName').val().trim() == '') {
            alert("You must enter a name for the wanted list.");
            return;
        }

        $.ajax({
            type:'POST',
            url:"{{ route('our-wanted.saveOurWantedList') }}",
            data:{name: $('#ourWantedListName').val()},
            success:function(data){
                location.reload();
            }
        });
    });

    $('#deleteOurWantedList').on('click', function () {

        if($('#ourWantedListsTopSelect').val().trim() == '') {
            alert("You must select a wanted list to delete.");
            return;
        }

        $.ajax({
            type:'POST',
            url:"{{ route('our-wanted.deleteOurWantedList') }}",
            data:{id: $('#ourWantedListsTopSelect').val()},
            success:function(data){
                location.reload();
            }
        });
    });

    $('#ourWantedListsTopSelect').on('change', function () {
        $('#selectOurWantedListForm').submit();
    });

    $('#filterOurWanted').on('shown.bs.modal', function () {
        $("#filterOurWanted .animal-select2").val(null).trigger('change');

        $("#filterOurWanted input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterOurWanted input[name=filter_animal_option]").trigger('change');

        $("#filterOurWanted [name=filter_areas_empty]").prop('checked', false);
        $("#filterOurWanted [name=filter_areas_empty]").trigger('change');
    });

    $('#filterOurWanted').on('hidden.bs.modal', function () {
        $("#filterOurWanted .animal-select2").val(null).trigger('change');

        $("#filterOurWanted input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterOurWanted input[name=filter_animal_option]").trigger('change');

        $("#filterOurWanted [name=filter_areas_empty]").prop('checked', false);
        $("#filterOurWanted [name=filter_areas_empty]").trigger('change');

        $(this).find('form').trigger('reset');
    });

    $("#filterOurWanted #resetBtn").click(function() {
        $("#filterOurWanted .animal-select2").val(null).trigger('change');

        $("#filterOurWanted input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterOurWanted input[name=filter_animal_option]").trigger('change');

        $("#filterOurWanted [name=filter_areas_empty]").prop('checked', false);
        $("#filterOurWanted [name=filter_areas_empty]").trigger('change');

        $("#filterOurWanted").find('form').trigger('reset');
    });

    $('#filterOurWanted input[name=filter_animal_option]').change(function() {
        var checkedOption = $('#filterOurWanted input[name=filter_animal_option]:checked').val();

        if (checkedOption == 'by_name') {
            $("#filterOurWanted [name=filter_animal_name]").val('');
            $("#filterOurWanted [name=filter_animal_name]").prop('disabled', false);
            $("#filterOurWanted .animal-select2").prop('disabled', true);
            $("#filterOurWanted .animal-select2").val(null).trigger('change');
        }
        else if (checkedOption == 'by_id') {
            $("#filterOurWanted [name=filter_animal_name]").val('');
            $("#filterOurWanted [name=filter_animal_name]").prop('disabled', true);
            $("#filterOurWanted .animal-select2").prop('disabled', false);
        }
        else {
            $("#filterOurWanted [name=filter_animal_name]").val('');
            $("#filterOurWanted [name=filter_animal_name]").prop('disabled', true);
            $("#filterOurWanted .animal-select2").prop('disabled', true);
            $("#filterOurWanted .animal-select2").val(null).trigger('change');
        }
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

        var areas = [];
        $('#editSelectedRecords [name=edit_selection_area_id]:checked').each(function(){
            areas.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to edit.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('our-wanted.editSelectedRecords') }}",
                data:{
                    items: ids,
                    origin: $('#editSelectedRecords [name=edit_selection_origin]').val(),
                    age_group: $('#editSelectedRecords [name=edit_selection_age_group]').val(),
                    areas: areas,
                    add_to_wanted_lists: $('#editSelectedRecords [name=selectionAddToWantedLists]').val(),
                    remove_from_wanted_lists: $('#editSelectedRecords [name=selectionRemoveFromWantedLists]').val()
                },
                success:function(data){
                    location.reload();
                }
            });
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
        var count_page_records = $('#countOurWantedVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $("#printOptionsDialog [name=print_document_type],[name=print_language],[name=print_pictures]").prop('checked', false);

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

        if (ids.length == $('#countOurWantedTotal').val()) {
            export_option = "all";
            ids = [];
        }

        var document_type = $('#printOptionsDialog [name=print_document_type]:checked').val();
        var language = $('#printOptionsDialog [name=print_language]:checked').val();
        var pictures = $('#printOptionsDialog [name=print_pictures]:checked').val();

        if (export_option != 'all' && ids.length == 0)
            alert("There are not records to export.");
        else if (document_type == null || language == null || pictures == null)
            alert("The options: Document, Language and Pictures, must be marked.");
        else if (export_option != 'all' && ids.length > 300)
            alert("You cannot print more than 300 records. Please select HTML file and option 'All records'.");
        else {
            $.ajax({
                type:'GET',
                url:"{{ route('our-wanted.printOurWantedList') }}",
                data:{
                    export_option: export_option,
                    document_type: document_type,
                    language: language,
                    pictures: pictures,
                    items: ids
                },
                success: function(response){
                    if (response.success) {
                        $('#printOptionsDialog').modal('hide');

                        var link = document.createElement('a');

                        link.href = window.URL = response.url;

                        link.download = response.fileName;

                        link.click();
                    }
                    else
                        alert(response.message);
                }
            });
        }
    });

    $('#exportStandardWantedRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countOurWantedVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportStandardWanteds').modal('show');
    });

    $('#exportStandardWanteds').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportStandardWanteds [name=export_option]:checked').val();

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
            var url = "{{route('our-wanted.export')}}?items=" + ids;
            window.location = url;

            $('#exportStandardWanteds').modal('hide');
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

    $("#filterOurWanted [name=filter_areas_empty]").change( function() {
        if($(this).prop('checked')) {
            $('#filterOurWanted #filter-areas').find(':checkbox').each(function() {
                $(this).prop('checked', false);
                $(this).prop('disabled', true);
            });
        }
        else {
            $('#filterOurWanted #filter-areas').find(':checkbox').each(function () {
                $(this).prop('checked', false);
                $(this).prop('disabled', false);
            });
        }
    });
});

</script>

@endsection
