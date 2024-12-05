@extends('layouts.admin')

@section('header-content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                @if (Auth::user()->hasPermission('standard-surplus.create'))
                    <a href="{{ route('our-surplus.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Add
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterOurSurplus">
                    <i class="fas fa-fw fa-search"></i> Filter
                </button>
                <a href="{{ route('our-surplus.showAll') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-window-restore"></i> Show all
                </a>
                @if (Auth::user()->hasPermission('standard-surplus.update'))
                    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                        <i class="fas fa-fw fa-edit"></i> Edit selection
                    </button>
                @endif
                @if (Auth::user()->hasPermission('standard-surplus.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn btn-light">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
                @if (Auth::user()->hasPermission('standard-surplus.read-lists'))
                    <button id="printOptions" type="button" class="btn btn-light" data-toggle="modal" data-target="#printOptionsDialog">
                        <i class="fas fa-fw fa-print"></i> Print
                    </button>
                @endif
                @if (Auth::user()->hasPermission('standard-surplus.export-survey'))
                    <a id="exportStockRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportStockList">
                        <i class="fas fa-fw fa-save"></i> Export
                    </a>
                @endif
            </div>

            <h1 class="h1 text-white"><i class="fas fa-fw fa-hand-paper mr-2"></i> {{ __('Stock-standardprices') }}</h1>
            <p class="text-white">Products-animals that we have available.</p>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 d-inline-flex">
            <label class="text-white" style="font-size: 16px;">Order by:</label>&nbsp;
            {!! Form::open(['id' => 'standardSurplusOrderByForm', 'route' => 'our-surplus.orderBy', 'method' => 'GET']) !!}
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
        @if (Auth::user()->hasPermission('standard-surplus.read-lists'))
        <div class="col-md-6 text-right">
            <div class="d-inline-flex">
                <label class="text-white" style="font-size: 18px;">Our surplus lists:</label>&nbsp;
                <div id="selectOurSurplusList">
                    <div class="d-inline-flex">
                        {!! Form::open(['id' => 'selectOurSurplusListForm', 'route' => 'our-surplus.selectOurSurplusList']) !!}
                        <select class="custom-select custom-select-sm w-auto" id="ourSurplusListsTopSelect" name="ourSurplusListsTopSelect">
                            <option @if(!isset($ourSurplusListSelected)) selected @endif value="0">--Select list--</option>
                            @foreach ($ourSurplusLists as $ourSurplusList)
                                <option value="{{ $ourSurplusList->id }}" @if(isset($ourSurplusListSelected) && $ourSurplusList->id == $ourSurplusListSelected->id) selected @endif>{{ $ourSurplusList->name }}</option>
                            @endforeach
                        </select>
                        {!! Form::close() !!}
                    </div>
                    @if (Auth::user()->hasPermission('standard-surplus.create-lists'))
                        &nbsp;
                        <button type="button" id="addOurSurplusList" class="btn btn-sm btn-light">
                            <i class="fas fa-fw fa-plus"></i>
                        </button>&nbsp;
                        <button type="button" id="deleteOurSurplusList" class="btn btn-sm btn-light">
                            <i class="fas fa-fw fa-window-close"></i>
                        </button>
                    @endif
                </div>
                <div id="newOurSurplusList" class="d-none">
                    <input type="text" id="ourSurplusListName" class="form-control form-control-sm pt-0 w-auto d-inline-flex">&nbsp;
                    <button type="button" id="saveOurSurplusList" class="btn btn-sm btn-light">
                        <i class="fas fa-fw fa-save"></i>
                    </button>&nbsp;
                    <button type="button" id="discardOurSurplusList" class="btn btn-sm btn-light">
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
                    <input type="hidden" id="countRecordsOnPage" value="{{ ($ourSurpluses->count() > 0) ? $ourSurpluses->count() : 0 }}" />
                    <input type="hidden" id="countRecordsTotal" value="{{ $ourSurpluses->total() }}" />
                </div>
                <div class="d-flex align-items-center">
                    <span class="ml-3 mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('our-surplus.removeFromOurSurplusSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
            </div>
            <div class="d-flex align-items-center">
                Page: {{$ourSurpluses->currentPage()}} | Records:&nbsp;
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'our-surplus.recordsPerPage', 'method' => 'GET']) !!}
                    {!! Form::text('recordsPerPage', $ourSurpluses->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
                &nbsp;| Total: {{$ourSurpluses->total()}}
            </div>
        </div>
    </div>
</div>

@foreach ($ourSurpluses as $ourSurplus)
    <div class="card shadow mb-1">
        <div class="card-body">

            <div class="d-flex">

                <div class="justify-content-start" style="width: 8%">
                    <div class="mr-3">
                        <input type="checkbox" class="selector selector-thesurplus" value="{{ $ourSurplus->id }}" />

                        @if (Auth::user()->hasPermission('standard-surplus.read'))
                            <a href="{{ route('our-surplus.show', [$ourSurplus->id]) }}" title="Show standard surplus" class="ml-1"><i class="fas fa-search"></i></a>
                        @endif

                        @if (Auth::user()->hasPermission('surplus-suppliers.read'))
                            <a id="showRelatedSurplusSuppliers" href="#" title="Show related surplus of suppliers" class="ml-1" data-toggle="modal" data-id="{{ $ourSurplus->id }}" data-target="#relatedSurplusSuppliers"><i class="fas fa-list"></i></a><br />
                        @endif

                        <span class="mt-3" style="font-size: 0.7rem;">{{ ($ourSurplus->updated_at) ? $ourSurplus->updated_at->toDateString() : '' }}</span>
                        @if(!empty($ourSurplus->catalog_pic) && empty($filter_imagen_species))
                            <img src="{{ asset('storage/oursurplus_pictures/'.$ourSurplus->id.'/'.$ourSurplus->catalog_pic) }}" class="rounded mt-2" style="max-width:70px;" alt="" />
                        @else
                            @if ($ourSurplus->origin_field != "stuffed" && $ourSurplus->origin_field != "Stuffed" && empty($filter_upload_images))
                                @if ($ourSurplus->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$ourSurplus->animal->id.'/'.$ourSurplus->animal->catalog_pic))
                                    <img src="{{ asset('storage/animals_pictures/'.$ourSurplus->animal->id.'/'.$ourSurplus->animal->catalog_pic) }}" class="rounded mt-2" style="max-width:70px;" alt="" />
                                @else
                                    @if(!empty($ourSurplus->animal->imagen_first))
                                        <img src="{{ asset('storage/animals_pictures/'.$ourSurplus->animal->id.'/'.$ourSurplus->animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                                    @else
                                        <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>

                <div class="w-100">

                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h3>{{ ($ourSurplus->animal != null) ? $ourSurplus->animal->common_name : '' }}</h3>&nbsp;-&nbsp;
                            <span><em>{{ ($ourSurplus->animal != null) ? $ourSurplus->animal->scientific_name : '' }}</em></span>
                            <span class="ml-5">{{ ($ourSurplus->region != null) ? $ourSurplus->region->name : '' }} {{ $ourSurplus->origin_field }}</span>
                            <span class="ml-5">
                                <b>Areas to offer:&nbsp;</b>
                                @foreach($ourSurplus->area_regions as $area_region)
                                    {{$area_region->short_cut}}
                                @endforeach
                            </span>
                        </div>
                        <div>
                            <span class="text-danger ml-5">
                                @if ($ourSurplus->is_public)
                                    Published
                                @else
                                    Not published
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-start align-items-center mb-3">

                        <div style="flex-grow: 1" class="d-flex justify-content-start">
                            @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices') || Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                                <div class="mr-2" style="min-width: 350px; width: 48%;">
                                    <table class="table table-striped table-sm mb-0 text-center" >
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="pr-1">Curr</th>
                                                <th class="pr-2">M</th>
                                                <th class="pr-2">F</th>
                                                <th class="pr-2">U</th>
                                                <th class="pr-2">P</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                                                <tr>
                                                    <td style="width: 40px;"><b>Costs</b></td>
                                                    <td class="pr-1">{{ $ourSurplus->cost_currency }}</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->costPriceM) }}.00</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->costPriceF) }}.00</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->costPriceU) }}.00</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->costPriceP) }}.00</td>
                                                </tr>
                                            @endif
                                            @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                                                <tr>
                                                    <td style="width: 40px;"><b>Sales</b></td>
                                                    <td class="pr-1">{{ $ourSurplus->sale_currency }}</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->salePriceM) }}.00</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->salePriceF) }}.00</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->salePriceU) }}.00</td>
                                                    <td class="pr-2">{{ number_format($ourSurplus->salePriceP) }}.00</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <div style="min-width: 300px; width: 38%;">
                                <table class="table table-striped table-sm mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th class="pr-2">Age</th>
                                            <th class="pr-2">Size</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="pr-2">{{ $ourSurplus->age_field }}</td>
                                            <td class="pr-2">{{ $ourSurplus->size_field }}</td>
                                        </tr>
                                        <tr>
                                            @if( $ourSurplus->special_conditions )
                                                <td class="pr-2">
                                                    <div class="alert alert-primary mb-0" style="padding: 0px;">
                                                        <b>Special conditions</b>
                                                        {{ $ourSurplus->special_conditions }}
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endforeach
<div class="row mt-2">
    <div class="col-md-12">
        {{$ourSurpluses->links()}}
    </div>
</div>

@include('our_surplus.filter_modal', ['modalId' => 'filterOurSurplus'])

@include('our_surplus.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

@include('print_dialogs.print_modal_full_options', ['modalId' => 'printOptionsDialog', 'route' => route('our-surplus.printOurSurplusList') ])

@include('print_dialogs.print_modal_select_first_three_animals', ['modalId' => 'selectFirstThreeAnimalsModal'])

@include('our_surplus.related_surplus_suppliers_modal', ['modalId' => 'relatedSurplusSuppliers'])

@include('export_excel.export_options_modal', ['modalId' => 'exportStockList'])

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $(':checkbox:checked').prop('checked', false);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector-thesurplus").prop('checked', this.checked);
    });

    function strpad00(s) {
        s = s + '';
        if (s.length === 1) s = '0'+s;
        return s;
    }

    $('#orderByField').on('change', function () {
        $('#standardSurplusOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#standardSurplusOrderByForm').submit();
    });

    $('#deleteSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selector-thesurplus").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('our-surplus.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#addOurSurplusList').on('click', function () {
        $('#newOurSurplusList').removeClass("d-none");

        var d = new Date();
        var strDate = d.getFullYear() + "-" + strpad00((d.getMonth()+1))  + "-" + d.getDate();

        $('#ourSurplusListName').val('List '+strDate);

        $('#selectOurSurplusList').hide();
    });

    $('#discardOurSurplusList').on('click', function () {
        $('#newOurSurplusList').addClass("d-none");
        $('#selectOurSurplusList').show();
    });

    $('#saveOurSurplusList').on('click', function () {
        if($('#ourSurplusListName').val().trim() == '') {
            alert("You must enter a name for the surplus list.");
            return;
        }

        $.ajax({
            type:'POST',
            url:"{{ route('our-surplus.saveOurSurplusList') }}",
            data:{name: $('#ourSurplusListName').val()},
            success:function(data){
                location.reload();
            }
        });
    });

    $('#deleteOurSurplusList').on('click', function () {
        if($('#ourSurplusListsTopSelect').val().trim() == '') {
            alert("You must select a surplus list to delete.");
            return;
        }

        $.ajax({
            type:'POST',
            url:"{{ route('our-surplus.deleteOurSurplusList') }}",
            data:{id: $('#ourSurplusListsTopSelect').val()},
            success:function(data){
                location.reload();
            }
        });
    });

    $('#ourSurplusListsTopSelect').on('change', function () {
        $('#selectOurSurplusListForm').submit();
    });

    $('#filterOurSurplus').on('shown.bs.modal', function () {
        $("#filterOurSurplus .animal-select2").val(null).trigger('change');

        $("#filterOurSurplus input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterOurSurplus input[name=filter_animal_option]").trigger('change');

        $("#filterOurSurplus [name=filter_areas_empty]").prop('checked', false);
        $("#filterOurSurplus [name=filter_areas_empty]").trigger('change');
    });

    $('#filterOurSurplus').on('hidden.bs.modal', function () {
        $("#filterOurSurplus .animal-select2").val(null).trigger('change');

        $("#filterOurSurplus input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterOurSurplus input[name=filter_animal_option]").trigger('change');

        $("#filterOurSurplus [name=filter_areas_empty]").prop('checked', false);
        $("#filterOurSurplus [name=filter_areas_empty]").trigger('change');

        $(this).find('form').trigger('reset');
    });

    $("#filterOurSurplus #resetBtn").click(function() {
        $("#filterOurSurplus .animal-select2").val(null).trigger('change');

        $("#filterOurSurplus input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterOurSurplus input[name=filter_animal_option]").trigger('change');

        $("#filterOurSurplus [name=filter_areas_empty]").prop('checked', false);
        $("#filterOurSurplus [name=filter_areas_empty]").trigger('change');

        $("#filterOurSurplus").find('form').trigger('reset');
    });

    $('#filterOurSurplus input[name=filter_animal_option]').change(function() {
        var checkedOption = $('#filterOurSurplus input[name=filter_animal_option]:checked').val();

        if (checkedOption == 'by_name') {
            $("#filterOurSurplus [name=filter_animal_name]").val('');
            $("#filterOurSurplus [name=filter_animal_name]").prop('disabled', false);
            $("#filterOurSurplus .animal-select2").prop('disabled', true);
            $("#filterOurSurplus .animal-select2").val(null).trigger('change');
        }
        else if (checkedOption == 'by_id') {
            $("#filterOurSurplus [name=filter_animal_name]").val('');
            $("#filterOurSurplus [name=filter_animal_name]").prop('disabled', true);
            $("#filterOurSurplus .animal-select2").prop('disabled', false);
        }
        else {
            $("#filterOurSurplus [name=filter_animal_name]").val('');
            $("#filterOurSurplus [name=filter_animal_name]").prop('disabled', true);
            $("#filterOurSurplus .animal-select2").prop('disabled', true);
            $("#filterOurSurplus .animal-select2").val(null).trigger('change');
        }
    });

    $('#editSelectedRecords').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        /*$(this).find('form')[0].reset();*/
    });

    $('#sendEditSelectionForm').on('click', function(event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector-thesurplus").each(function(){
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
                url:"{{ route('our-surplus.editSelectedRecords') }}",
                data:{
                    items: ids,
                    availability: $('#editSelectedRecords [name=edit_selection_availability]').val(),
                    is_public: $('#editSelectedRecords [name=edit_selection_is_public]').val(),
                    origin: $('#editSelectedRecords [name=edit_selection_origin]').val(),
                    age_group: $('#editSelectedRecords [name=edit_selection_age_group]').val(),
                    cost_currency: $('#editSelectedRecords [name=edit_selection_cost_currency]').val(),
                    sale_currency: $('#editSelectedRecords [name=edit_selection_sale_currency]').val(),
                    areas: areas,
                    add_to_stock_lists: $('#editSelectedRecords [name=selectionAddToStockLists]').val(),
                    remove_from_stock_lists: $('#editSelectedRecords [name=selectionRemoveFromStockLists]').val()
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

    $('#selectionAddToStockLists').multiselect({
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

    $('#selectionRemoveFromStockLists').multiselect({
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

    $('body').on('click', '#showRelatedSurplusSuppliers', function () {
        var ourSurplusId= $(this).data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('our-surplus.searchRelatedSurplusSuppliers') }}",
            data: {
                id: ourSurplusId,
            },
            success:function(data) {
                if(data.success) {
                    $("#surplusSuppliers > tbody").empty();

                    $.each(data.surpluses, function(key, item) {
                        $("#relatedSurplusSuppliers #surplusSuppliers").append('<tr>' +
                            '<td style="text-align: center;">' + item.updated_date + '</td>' +
                            '<td>' + item.institution + '<br>' + ((item.contact) ? item.contact.email : '-') + '</td>' +
                            '<td style="text-align: center;">' + ((item.country) ? item.country.name : '-') + '</td>' +
                            '<td style="text-align: center;">' + ((item.quantityM < 0) ? 'x' : item.quantityM) + '</td>' +
                            '<td style="text-align: center;">' + ((item.quantityF < 0) ? 'x' : item.quantityF) + '</td>' +
                            '<td style="text-align: center;">' + ((item.quantityU < 0) ? 'x' : item.quantityU) + '</td>' +
                            '<td style="text-align: center;">' + item.cost_currency + '</td>' +
                            '<td style="text-align: center;">' + item.costPriceM.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + item.costPriceF.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + item.costPriceU.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + item.costPriceP.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + item.origin + '</td>' +
                            '<td style="text-align: center;">' + item.age + '</td>' +
                            '<td style="text-align: center;">' + ((item.bornYear) ? item.bornYear : '') + '</td>' +
                            '<td style="text-align: center;">' + item.size + '</td>' +
                            '<td>' + ((item.remarks) ? item.remarks : '') + '</td>' +
                        '</tr>');
                    });

                    $('#relatedSurplusSuppliers').modal('show');
                }
                else
                    alert("This standard surplus has not surplus of suppliers related.");
            }
        });
    });

    $('#exportStockRecords').on('click', function () {
        var count_selected_records = $(":checked.selector-thesurplus").length;
        var count_page_records = $('#countRecordsOnPage').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportStockList').modal('show');
    });

    $('#exportStockList').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportStockList [name=export_option]:checked').val();

        var ids = [];
        if(export_option == "selection") {
            $(":checked.selector-thesurplus").each(function(){
                ids.push($(this).val());
            });
        }
        else {
            $(".selector-thesurplus").each(function(){
                ids.push($(this).val());
            });
        }

        if(ids.length == 0)
            alert("There are not records to export.");
        else {
            var url = "{{route('our-surplus.export')}}?items=" + ids;
            window.location = url;

            $('#exportStockList').modal('hide');
        }
    });

    $("#filterOurSurplus [name=filter_areas_empty]").change( function() {
        if($(this).prop('checked')) {
            $('#filterOurSurplus #filter-areas').find(':checkbox').each(function() {
                $(this).prop('checked', false);
                $(this).prop('disabled', true);
            });
        }
        else {
            $('#filterOurSurplus #filter-areas').find(':checkbox').each(function () {
                $(this).prop('checked', false);
                $(this).prop('disabled', false);
            });
        }
    });
    $("#printOptionsDialog [name=print_stuffed]").change( function() {
        if($(this).val() == "yes") {
            $("#printOptionsDialog .print_client").removeClass("d-none");
        }
        else {
            $("#printOptionsDialog .print_client").addClass("d-none");
        }
    });
});

</script>

<script src="{{ asset('js/jquery-print-dialog-full.js?v=1706781260') }}"></script>

@endsection
