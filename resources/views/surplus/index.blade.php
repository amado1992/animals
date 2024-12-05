@extends('layouts.admin')

@section('header-content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                @if (Auth::user()->hasPermission('surplus-suppliers.read'))
                    <a href="{{ route('surplus.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Add
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterSurplus">
                    <i class="fas fa-fw fa-search"></i> Filter
                </button>
                <a href="{{ route('surplus.showAll') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-window-restore"></i> Show all
                </a>
                @if (Auth::user()->hasPermission('surplus-suppliers.update'))
                    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                        <i class="fas fa-fw fa-edit"></i> Edit selection
                    </button>
                @endif
                @if (Auth::user()->hasPermission('surplus-suppliers.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn btn-light">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
                @if (Auth::user()->hasPermission('surplus-suppliers.read-lists'))
                    <button id="printOptions" type="button" class="btn btn-light" data-toggle="modal" data-target="#printOptionsDialog">
                        <i class="fas fa-fw fa-print"></i> Print
                    </button>
                @endif
                @if (Auth::user()->hasPermission('surplus-suppliers.export-survey'))
                    <a id="exportSurpluses" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportSurplusList">
                        <i class="fas fa-fw fa-save"></i> Export
                    </a>
                @endif
            </div>

            <h1 class="h1 text-white"><i class="fas fa-fw fa-hand-paper mr-2"></i> {{ __('Surplus') }}</h1>
            <p class="text-white">All surplus offered by our suppliers</p>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 d-inline-flex">
            <label class="text-white" style="font-size: 16px;">Order by:</label>&nbsp;
            {!! Form::open(['id' => 'surplusOrderByForm', 'route' => 'surplus.orderBy', 'method' => 'GET']) !!}
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
                    <label class="text-white" style="font-size: 18px;">Surplus lists:</label>&nbsp;
                    <div id="selectSurplusList">
                        <div class="d-inline-flex">
                            {!! Form::open(['id' => 'selectSurplusListForm', 'route' => 'surplus.selectSurplusList']) !!}
                            <select class="custom-select custom-select-sm w-auto" id="surplusListsTopSelect" name="surplusListsTopSelect">
                                <option @if(!isset($surplusListSelected)) selected @endif value="0">--Select list--</option>
                                @foreach ($surplusLists as $surplusList)
                                    <option value="{{ $surplusList->id }}" @if(isset($surplusListSelected) && $surplusList->id == $surplusListSelected->id) selected @endif>{{ $surplusList->name }}</option>
                                @endforeach
                            </select>
                            {!! Form::close() !!}
                        </div>
                        @if (Auth::user()->hasPermission('surplus-suppliers.create-lists'))
                            &nbsp;
                            <button type="button" id="addSurplusList" class="btn btn-sm btn-light">
                                <i class="fas fa-fw fa-plus"></i>
                            </button>&nbsp;
                            <button type="button" id="deleteSurplusList" class="btn btn-sm btn-light">
                                <i class="fas fa-fw fa-window-close"></i>
                            </button>
                        @endif
                    </div>
                    <div id="newSurplusList" class="d-none">
                        <input type="text" id="surplusListName" class="form-control form-control-sm pt-0 w-auto d-inline-flex">&nbsp;
                        <button type="button" id="saveSurplusList" class="btn btn-sm btn-light">
                            <i class="fas fa-fw fa-save"></i>
                        </button>&nbsp;
                        <button type="button" id="discardSurplusList" class="btn btn-sm btn-light">
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
                    <input type="hidden" id="countRecordsOnPage" value="{{ ($surpluses->count() > 0) ? $surpluses->count() : 0 }}" />
                    <input type="hidden" id="countRecordsTotal" value="{{ $surpluses->total() }}" />
                </div>
                <div class="d-flex align-items-center">
                    <span class="ml-3 mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('surplus.removeFromSurplusSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
            </div>
            <div class="d-flex align-items-center">
                Page: {{$surpluses->currentPage()}} | Records:&nbsp;
                @if (Auth::user()->hasPermission('surplus-suppliers.see-all-surplus'))
                    {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'surplus.recordsPerPage', 'method' => 'GET']) !!}
                        {!! Form::text('recordsPerPage', $surpluses->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                    {!! Form::close() !!}
                @else
                    {{$surpluses->count()}}
                @endif
                &nbsp;| Total: {{$surpluses->total()}}
            </div>
        </div>
    </div>
</div>

@foreach ($surpluses as $surplus)
    <div class="card shadow mb-1">
        <div class="card-body">

            <div class="d-flex">
                <div class="justify-content-start" style="width: 8%">
                    <div class="mr-3">
                        <input type="checkbox" class="selector selector-thesurplus" value="{{ $surplus->id }}" />

                        @if (Auth::user()->hasPermission('surplus-suppliers.read'))
                            <a href="{{ route('surplus.show', [$surplus->id]) }}" class="ml-1" title="Show surplus"><i class="fas fa-search"></i></a>
                        @endif

                        @if (Auth::user()->hasPermission('standard-surplus.read'))
                            <a id="showRelatedStandardSurplus" href="#" title="Show related standard surplus" class="ml-1" data-toggle="modal" data-id="{{ $surplus->id }}" data-target="#relatedStandardSurplus"><i class="fas fa-list"></i></a>
                        @endif

                        @if (Auth::user()->hasPermission('surplus-suppliers.update'))
                            <a href="{{ route('surplus.edit', [$surplus->id]) }}?url=index" title="Edit standard surplus" class="ml-1"><i class="fas fa-fw fa-edit"></i></a><br />
                        @endif

                        @if(!empty($surplus->surplus_lists()) && !empty($surplus->catalog_pic) && empty($filter_imagen_species))
                            <img src="{{ asset('storage/surpluses_pictures/'.$surplus->id.'/'.$surplus->catalog_pic) }}" class="rounded mt-2" style="max-width:70px;" alt="" />
                        @else
                            @if ($surplus->origin_field != "stuffed" && $surplus->origin_field != "Stuffed" && empty($filter_upload_images))
                                @if ($surplus->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic))
                                    <img src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic) }}" class="rounded mt-2" style="max-width:70px;" alt="" />
                                @else
                                    @if(!empty($surplus->animal->imagen_first))
                                        <img src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                                    @else
                                        <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>

                <div class="w-100">
                    <div class="row mb-2">
                        <div class="col-md-5 d-flex align-items-center">
                            <h4>{{ ($surplus->animal != null) ? $surplus->animal->common_name : '' }}</h4>&nbsp;-&nbsp;
                            <span><em>{{ ($surplus->animal != null) ? $surplus->animal->scientific_name : '' }}</em></span>
                        </div>
                        <div class="col-md-6">
                            @if ($surplus->organisation != null)
                                <span class="card-title mb-0"><strong style="margin: 0 20px 0 0;">{{ ucfirst($surplus->organisation->name) }}</strong> {{ ($surplus->country != null) ? $surplus->country->region->name . ', ' . $surplus->country->name : '' }}</span><br>
                                @if($surplus->organisation->contacts)<span><em>
                                    @if(!empty($surplus->organisation->contacts[0]->first_name))
                                        ({{ ucfirst($surplus->organisation->contacts[0]->first_name) }} {{ ucfirst($surplus->organisation->contacts[0]->last_name) }})
                                    @else
                                        @if (!empty($surplus->organisation->contacts[0]->email))
                                            ({{ $surplus->organisation->contacts[0]->email }})
                                        @else
                                            @if (!empty($surplus->organisation->email))
                                                ({{ $surplus->organisation->email }})
                                            @endif
                                        @endif
                                    @endif</em></span>
                                    @if(!empty($surplus->organisation->contacts[0]->email))
                                        <a href="mailto: {{ $surplus->organisation->contacts[0]->email }}"><i class="fas fa-envelope"></i></a>
                                    @else
                                        @if (!empty($surplus->organisation->email))
                                            <a href="mailto: {{ $surplus->organisation->email }}"><i class="fas fa-envelope"></i></a>
                                        @endif
                                    @endif
                                @endif
                            @else
                                <span class="card-title mb-0 text-danger">INSTITUTION NOT DEFINED</span>
                            @endif
                        </div>
                        <div class="col-md-1">
                            @if ($surplus->organisation != null)
                                @if (!empty($surplus->organisation->level))
                                    <span class="level-{{$surplus->organisation->level ?? ""}}" style="padding: 0 11px 0 11px;"><strong>Level:</strong> {{ $surplus->organisation->level }}</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-start align-items-center mb-3">

                        <div style="flex-grow: 1" class="d-flex justify-content-start">

                            <div class="mr-2" style="min-width: 100px; width: 14%;">
                                <table class="table table-striped table-sm mb-0 text-center" >
                                    <thead>
                                        <tr>
                                            <th style="width: 33%">M</th>
                                            <th style="width: 33%">F</th>
                                            <th style="width: 33%">U</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $surplus->male_quantity }}</td>
                                            <td>{{ $surplus->female_quantity }}</td>
                                            <td>{{ $surplus->unknown_quantity }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                                        @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                            <tr>
                                                <td style="width: 40px;"><b>Costs</b></td>
                                                <td class="pr-1">{{ $surplus->cost_currency }}</td>
                                                <td class="pr-2">{{ number_format($surplus->costPriceM) }}.00</td>
                                                <td class="pr-2">{{ number_format($surplus->costPriceF) }}.00</td>
                                                <td class="pr-2">{{ number_format($surplus->costPriceU) }}.00</td>
                                                <td class="pr-2">{{ number_format($surplus->costPriceP) }}.00</td>
                                            </tr>
                                        @endif
                                        @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                            <tr>
                                                <td style="width: 40px;"><b>Sales</b></td>
                                                <td class="pr-1">{{ $surplus->sale_currency }}</td>
                                                <td class="pr-2">{{ number_format($surplus->salePriceM) }}.00</td>
                                                <td class="pr-2">{{ number_format($surplus->salePriceF) }}.00</td>
                                                <td class="pr-2">{{ number_format($surplus->salePriceU) }}.00</td>
                                                <td class="pr-2">{{ number_format($surplus->salePriceP) }}.00</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div style="min-width: 300px; width: 38%;">
                                <table class="table table-striped table-sm mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th class="pr-2">Origin</th>
                                            <th class="pr-2">Age</th>
                                            <th class="pr-2">Born year</th>
                                            <th class="pr-2">Size</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="pr-2">{{ $surplus->origin_field }}</td>
                                            <td class="pr-2">{{ $surplus->age_field }}</td>
                                            <td class="pr-2">{{ $surplus->bornYear }}</td>
                                            <td class="pr-2">{{ $surplus->size_field }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            @if( $surplus->remarks )
                                <div class="alert alert-primary mb-0">
                                    <b>Remarks</b><br />
                                    {{ $surplus->remarks }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if( $surplus->special_conditions )
                                <div class="alert alert-primary mb-0">
                                    <b>Special conditions</b><br />
                                    {{ $surplus->special_conditions }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if( $surplus->intern_remarks )
                                <div class="alert alert-primary mb-0">
                                    <b>Internal remarks</b><br />
                                    {{ $surplus->intern_remarks }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 d-flex">
                            <b>Created:</b>
                            <div class="ml-2">
                                {{ ($surplus->created_at) ? $surplus->updated_at->toDateString() : '' }}
                            </div>
                        </div>
                        <div class="col-md-2 d-flex">
                            <b>To members:</b>
                            <div class="ml-2">
                                @if($surplus->to_members) yes @else no @endif
                            </div>
                        </div>
                        <div class="col-md-2 d-flex">
                            <b>Status:</b>
                            <div class="ml-2">
                                {{ ($surplus->surplus_status != null) ? $surplus_status[$surplus->surplus_status] : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endforeach
@if (Auth::user()->hasPermission('surplus-suppliers.see-all-surplus'))
    <div class="row mt-2">
        <div class="col-md-12">
            {{$surpluses->links()}}
        </div>
    </div>
@endif

@include('surplus.filter_modal', ['modalId' => 'filterSurplus'])

@include('surplus.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

@include('print_dialogs.print_modal_full_options', ['modalId' => 'printOptionsDialog', 'route' => route('surplus.printSurplusList') ])

@include('print_dialogs.print_modal_select_first_three_animals', ['modalId' => 'selectFirstThreeAnimalsModal'])

@include('surplus.related_standard_surplus_modal', ['modalId' => 'relatedStandardSurplus'])

@include('export_excel.export_options_modal', ['modalId' => 'exportSurplusList'])

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    var emailPreviewEditor = '';

    var config = {
    // Define the toolbar groups as it is a more accessible solution.
    toolbarGroups: [{
            "name": "document",
            "groups": ["mode"]
            },
            {
            "name": "basicstyles",
            "groups": ["basicstyles"]
            },
            {
            "name": "links",
            "groups": ["links"]
            },
            {
            "name": "paragraph",
            "groups": ["list", "align"]
            },
            {
            "name": "insert",
            "groups": ["insert"]
            },
            {
            "name": "styles",
            "groups": ["styles"]
            },
            {
            "name": "colors",
            "groups": ["colors"]
            }
        ],
        extraPlugins: 'stylesheetparser',
        height: 200,
        // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
    };

    $(':checkbox:checked.selector').prop('checked', false);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    function strpad00(s) {
        s = s + '';
        if (s.length === 1) s = '0'+s;
        return s;
    }

    $('#orderByField').on('change', function () {
        $('#surplusOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#surplusOrderByForm').submit();
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
                url:"{{ route('surplus.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#addSurplusList').on('click', function () {
        $('#newSurplusList').removeClass("d-none");

        var d = new Date();
        var strDate = d.getFullYear() + "-" + strpad00((d.getMonth()+1))  + "-" + d.getDate();

        $('#surplusListName').val('List '+strDate);

        $('#selectSurplusList').hide();
    });

    $('#discardSurplusList').on('click', function () {
        $('#newSurplusList').addClass("d-none");
        $('#selectSurplusList').show();
    });

    $('#saveSurplusList').on('click', function () {
        if($('#surplusListName').val().trim() == '') {
            alert("You must enter a name for the surplus list.");
            return;
        }

        $.ajax({
                type:'POST',
                url:"{{ route('surplus.saveSurplusList') }}",
                data:{name: $('#surplusListName').val()},
                success:function(data){
                    location.reload();
                }
            });
    });

    $('#deleteSurplusList').on('click', function () {
        if($('#surplusListsTopSelect').val().trim() == '') {
            alert("You must select a surplus list to delete.");
            return;
        }

        $.ajax({
                type:'POST',
                url:"{{ route('surplus.deleteSurplusList') }}",
                data:{id: $('#surplusListsTopSelect').val()},
                success:function(data){
                    location.reload();
                }
            });
    });

    $('#surplusListsTopSelect').on('change', function () {
        $('#selectSurplusListForm').submit();
    });

    $('#filterSurplus').on('hidden.bs.modal', function () {
        $("#filterSurplus .animal-select2").val(null).trigger('change');

        $('#filterSurplus input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterSurplus input[name=empty_institution]:checkbox').trigger('change');

        $('#filterSurplus input[name=empty_contact]:checkbox').prop('checked', false);
        $('#filterSurplus input[name=empty_contact]:checkbox').trigger('change');

        $("#filterSurplus .institution-select2").val(null).trigger('change');
        $("#filterSurplus .contact-select2").val(null).trigger('change');

        $("#filterSurplus input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterSurplus input[name=filter_animal_option]").trigger('change');

        $(this).find('form').trigger('reset');
    });

    $('#filterSurplus').on('shown.bs.modal', function () {
        $("#filterSurplus .animal-select2").val(null).trigger('change');

        $('#filterSurplus input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterSurplus input[name=empty_institution]:checkbox').trigger('change');

        $('#filterSurplus input[name=empty_contact]:checkbox').prop('checked', false);
        $('#filterSurplus input[name=empty_contact]:checkbox').trigger('change');

        $("#filterSurplus .institution-select2").val(null).trigger('change');
        $("#filterSurplus .contact-select2").val(null).trigger('change');

        $("#filterSurplus input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterSurplus input[name=filter_animal_option]").trigger('change');
    });

    $("#filterSurplus #resetBtn").click(function() {
        $("#filterSurplus .animal-select2").val(null).trigger('change');

        $('#filterSurplus input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterSurplus input[name=empty_institution]:checkbox').trigger('change');

        $('#filterSurplus input[name=empty_contact]:checkbox').prop('checked', false);
        $('#filterSurplus input[name=empty_contact]:checkbox').trigger('change');

        $("#filterSurplus .institution-select2").val(null).trigger('change');
        $("#filterSurplus .contact-select2").val(null).trigger('change');

        $("#filterSurplus input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterSurplus input[name=filter_animal_option]").trigger('change');

        $('#filterSurplus').find('form').trigger('reset');
    });

    $('#editSelectedRecords').on('hidden.bs.modal', function () {
        $("#editSelectedRecords .institution-select2").val(null).trigger('change');
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
                url:"{{ route('surplus.editSelectedRecords') }}",
                data:{
                    items: ids,
                    institution_id: $('#editSelectedRecords [name=edit_selection_supplier_id]').val(),
                    origin: $('#editSelectedRecords [name=edit_selection_origin]').val(),
                    age_group: $('#editSelectedRecords [name=edit_selection_age_group]').val(),
                    cost_currency: $('#editSelectedRecords [name=edit_selection_cost_currency]').val(),
                    sale_currency: $('#editSelectedRecords [name=edit_selection_sale_currency]').val(),
                    supplier_level: $('#editSelectedRecords [name=edit_selection_supplier_level]').val(),
                    to_members: $('#editSelectedRecords [name=edit_selection_to_members]').val(),
                    surplus_status: $('#editSelectedRecords [name=edit_selection_surplus_status]').val(),
                    add_to_surplus_lists: $('#editSelectedRecords [name=selectionAddToSurplusLists]').val(),
                    remove_from_surplus_lists: $('#editSelectedRecords [name=selectionRemoveFromSurplusLists]').val(),
                    institution_level: $('#editSelectedRecords [name=edit_selection_organisation_level]').val()
                },
                success:function(data){
                    $('#editSelectedRecords').find('form').trigger('reset');
                    location.reload();
                }
            });
        }
    });

    $('#filterSurplus input[name=filter_animal_option]').change(function() {
        var checkedOption = $('#filterSurplus input[name=filter_animal_option]:checked').val();

        if (checkedOption == 'by_name') {
            $("#filterSurplus [name=filter_animal_name]").val('');
            $("#filterSurplus [name=filter_animal_name]").prop('disabled', false);
            $("#filterSurplus .animal-select2").prop('disabled', true);
            $("#filterSurplus .animal-select2").val(null).trigger('change');
        }
        else if (checkedOption == 'by_id') {
            $("#filterSurplus [name=filter_animal_name]").val('');
            $("#filterSurplus [name=filter_animal_name]").prop('disabled', true);
            $("#filterSurplus .animal-select2").prop('disabled', false);
        }
        else {
            $("#filterSurplus [name=filter_animal_name]").val('');
            $("#filterSurplus [name=filter_animal_name]").prop('disabled', true);
            $("#filterSurplus .animal-select2").prop('disabled', true);
            $("#filterSurplus .animal-select2").val(null).trigger('change');
        }
    });

    //Select2 filter animal
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

    //Select2 institution selection
    $('[name=edit_selection_supplier_id]').on('change', function () {
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
                    $('[name=edit_selection_supplier_id]').append(newOption);
                }
            });
        }
    });

    $('#filterSurplus input[name=empty_institution]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterSurplus .institution-select2").val(null).trigger('change');
            $("#filterSurplus .institution-select2").prop('disabled', true);
        }
        else
            $("#filterSurplus .institution-select2").prop('disabled', false);
    });

    $('#filterSurplus input[name=empty_contact]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterSurplus .contact-select2").val(null).trigger('change');
            $("#filterSurplus .contact-select2").prop('disabled', true);
        }
        else
            $("#filterSurplus .contact-select2").prop('disabled', false);
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

    $('#selectionAddToSurplusLists').multiselect({
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

    $('#selectionRemoveFromSurplusLists').multiselect({
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

    $('body').on('click', '#showRelatedStandardSurplus', function () {
        var surplusId= $(this).data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('surplus.searchRelatedStandardSurplus') }}",
            data: {
                id: surplusId,
            },
            success:function(data) {
                if(data.success) {
                    $("#standardSurpluses > tbody").empty();

                    $.each(data.stdSurpluses, function(key, item) {
                        $("#relatedStandardSurplus #standardSurpluses").append('<tr>' +
                            '<td style="text-align: center;">' + item.updated_date + '</td>' +
                            '<td style="text-align: center;">' + item.availability + '</td>' +
                            '<td style="text-align: center;">' + item.sale_currency + '</td>' +
                            '<td style="text-align: center;">' + item.salePriceM.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + item.salePriceF.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + item.salePriceU.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + item.salePriceP.toFixed(2) + '</td>' +
                            '<td style="text-align: center;">' + ((item.region) ? item.region.name : '') + '</td>' +
                            '<td style="text-align: center;">' + item.origin + '</td>' +
                            '<td style="text-align: center;">' + item.age + '</td>' +
                            '<td style="text-align: center;">' + ((item.bornYear) ? item.bornYear : '') + '</td>' +
                            '<td style="text-align: center;">' + item.size + '</td>' +
                            '<td>' + ((item.remarks) ? item.remarks : '') + '</td>' +
                        '</tr>');
                    });

                    $('#relatedStandardSurplus').modal('show');
                }
                else
                    alert("This surplus has not standard surpluses related.");
            }
        });
    });

    $('#exportSurpluses').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countRecordsOnPage').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportSurplusList').modal('show');
    });

    $('#exportSurplusList').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportSurplusList [name=export_option]:checked').val();
        var orderByField = $('#orderByField').val();
        var orderByDirection = $('#orderByDirection').val();

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
            var url = "{{route('surplus.export')}}?items=" + ids + "&orderByField=" + orderByField + "&orderByDirection=" + orderByDirection;
            window.location = url;

            $('#exportSurplusList').modal('hide');
        }
    });

});

</script>

<script src="{{ asset('js/jquery-print-dialog-full.js?v=1706781260') }}"></script>

@endsection
