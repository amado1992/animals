@extends('layouts.admin')

@section('header-content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                @if (Auth::user()->hasPermission('surplus-suppliers.read'))
                    <a href="{{ route('surplus-collection.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Add
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterSurplusCollection">
                    <i class="fas fa-fw fa-search"></i> Filter
                </button>
                <a href="{{ route('surplus-collection.showAll') }}" class="btn btn-light">
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
                    @if (Auth::user()->hasPermission('contacts.contacts-address-list'))
                        <a id="createMailingAddressList" href="#" class="btn btn-light" data-toggle="modal" data-target="#createAddressList">
                            <i class="fas fa-fw fa-save"></i> Address list
                        </a>
                    @endif
            </div>

            <h1 class="h1 text-white"><i class="fas fa-fw fa-hand-paper mr-2"></i> {{ __('Collections') }}</h1>
            <p class="text-white">All surplus collections</p>
        </div>
    </div>

    <div class="d-flex flex-row justify-content-between items-center text-white mb-2">
        <div class="d-flex align-items-center">
            <label style="font-size: 16px;">Order by:</label>&nbsp;
            {!! Form::open(['id' => 'surplusOrderByForm', 'route' => 'surplus-collection.orderBy', 'method' => 'GET']) !!}
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
            Page: {{$surpluses->currentPage()}} | Records:&nbsp;
            @if (Auth::user()->hasPermission('surplus-suppliers.see-all-surplus'))
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'surplus-collection.recordsPerPage', 'method' => 'GET']) !!}
                    {!! Form::text('recordsPerPage', $surpluses->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
            @else
                {{$surpluses->count()}}
            @endif
            &nbsp;| Total: {{$surpluses->total()}}
        </div>
    </div>
@endsection

@section('main-content')

<div class="card shadow mb-1">
    <div class="card-body">
        <div class="d-flex flex-row items-center">
            <div class="d-flex align-items-center">
                <input type="checkbox" id="selectAll" name="selectAll" />&nbsp;Select all
                <input type="hidden" id="countSurplusVisible" value="{{ ($surpluses->count() > 0) ? $surpluses->count() : 0 }}" />
                <input type="hidden" id="countSurplusTotal" value="{{ $surpluses->total() }}" />
            </div>
            <div class="d-flex align-items-center">
                <span class="ml-3 mr-1">Filtered on:</span>
                @foreach ($filterData as $key => $value)
                    <a href="{{ route('surplus-collection.removeFromSurplusSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-2">
    <div class="card-body">
        @unless($surpluses->isEmpty())
            <div class="table-responsive mb-2">
                <table class="table table-striped table-sm mb-0" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th class="border-top-0" style="width: 3%"></th>
                            <th class="border-top-0" style="width: 8%"></th>
                            <th class="border-top-0" style="width: 16%">Species</th>
                            <th class="border-top-0" style="width: 16%">Supplier institution</th>
                            <th class="border-top-0" style="width: 7%">Country</th>
                            <th class="border-top-0" style="width: 7%">Area region</th>
                            <th class="border-top-0" style="width: 7%">Origin</th>
                            <th class="border-top-0" style="width: 7%">Age</th>
                            <th class="border-top-0" style="width: 7%">Born year</th>
                            <th class="border-top-0" style="width: 7%">Size</th>
                            <th class="border-top-0" style="width: 7%">Level</th>
                            <th class="border-top-0" style="width: 15%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surpluses as $surplus)
                            <tr>
                                <td class="pr-0">
                                    <input type="checkbox" class="selector" value="{{ $surplus->id }}" />

                                    @if (Auth::user()->hasPermission('surplus-suppliers.read'))
                                        <a href="{{ route('surplus-collection.show', [$surplus->id]) }}" class="ml-1" title="Show surplus"><i class="fas fa-search"></i></a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($surplus->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic))
                                        <img src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic) }}" class="rounded" style="max-width:70px;" alt="" />
                                    @else
                                        <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                                    @endif
                                </td>
                                <td>
                                    <span class="card-title mb-0">{{ ($surplus->animal != null) ? $surplus->animal->common_name : '' }}</span>
                                    <span><em>({{ ($surplus->animal != null) ? $surplus->animal->scientific_name : '' }})</em></span>
                                </td>
                                <td>
                                    @if ($surplus->organisation != null)
                                        <span class="card-title mb-0">{{ $surplus->organisation->name }} <a href="mailto:{{ $surplus->organisation->email }}"><u><em>({{ $surplus->organisation->email }})</em></u></a></span>
                                    @else
                                        <span class="card-title mb-0 text-danger">INSTITUTION NOT DEFINED</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ ($surplus->country) ? $surplus->country->name : '' }}
                                </td>
                                <td class="text-center">
                                    {{ ($surplus->country) ? $surplus->country->region->area_region->name : '' }}
                                </td>
                                <td class="text-center">
                                    {{ $surplus->origin_field }}
                                </td>
                                <td class="text-center">
                                    {{ $surplus->age_field }}
                                </td>
                                <td class="text-center">
                                    {{ $surplus->bornYear }}
                                </td>
                                <td class="text-center">
                                    {{ $surplus->size_field }}
                                </td>
                                <td class="text-center">
                                    @if (!empty($surplus->organisation->level))
                                        {{ $surplus->organisation->level ?? "" }}
                                    @endif
                                </td>
                                <td>
                                    {{ $surplus->remarks }}
                                    @if ($surplus->intern_remarks != null)
                                        <div>(<i class="fas fa-fw fa-info" title="Internal remarks: {{ $surplus->intern_remarks }}"></i>)</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$surpluses->links()}}
        @else
            <p> No surplus collections are added yet </p>
        @endunless
    </div>
</div>

@include('surplus_collections.filter_modal', ['modalId' => 'filterSurplusCollection'])

@include('surplus_collections.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

@include('print_dialogs.print_modal_small', ['modalId' => 'printOptionsDialog'])

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
                url:"{{ route('surplus-collection.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#filterSurplusCollection').on('shown.bs.modal', function () {
        $("#filterSurplusCollection .animal-select2").val(null).trigger('change');

        $('#filterSurplusCollection input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterSurplusCollection input[name=empty_institution]:checkbox').trigger('change');

        $('#filterSurplusCollection input[name=empty_contact]:checkbox').prop('checked', false);
        $('#filterSurplusCollection input[name=empty_contact]:checkbox').trigger('change');

        $("#filterSurplusCollection .institution-select2").val(null).trigger('change');
        $("#filterSurplusCollection .contact-select2").val(null).trigger('change');

        $("#filterSurplusCollection input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterSurplusCollection input[name=filter_animal_option]").trigger('change');
    });

    $('#filterSurplusCollection').on('hidden.bs.modal', function () {
        $("#filterSurplusCollection .animal-select2").val(null).trigger('change');

        $('#filterSurplusCollection input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterSurplusCollection input[name=empty_institution]:checkbox').trigger('change');

        $('#filterSurplusCollection input[name=empty_contact]:checkbox').prop('checked', false);
        $('#filterSurplusCollection input[name=empty_contact]:checkbox').trigger('change');

        $("#filterSurplusCollection .institution-select2").val(null).trigger('change');
        $("#filterSurplusCollection .contact-select2").val(null).trigger('change');

        $("#filterSurplusCollection input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterSurplusCollection input[name=filter_animal_option]").trigger('change');

        $(this).find('form').trigger('reset');
    });

    $("#filterSurplusCollection #resetBtn").click(function() {
        $("#filterSurplusCollection .animal-select2").val(null).trigger('change');

        $('#filterSurplusCollection input[name=empty_institution]:checkbox').prop('checked', false);
        $('#filterSurplusCollection input[name=empty_institution]:checkbox').trigger('change');

        $('#filterSurplusCollection input[name=empty_contact]:checkbox').prop('checked', false);
        $('#filterSurplusCollection input[name=empty_contact]:checkbox').trigger('change');

        $("#filterSurplusCollection .institution-select2").val(null).trigger('change');
        $("#filterSurplusCollection .contact-select2").val(null).trigger('change');

        $("#filterSurplusCollection input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterSurplusCollection input[name=filter_animal_option]").trigger('change');

        $("#filterSurplusCollection").find('form').trigger('reset');
    });

    $('#filterSurplusCollection input[name=filter_animal_option]').change(function() {
        var checkedOption = $('#filterSurplusCollection input[name=filter_animal_option]:checked').val();

        if (checkedOption == 'by_name') {
            $("#filterSurplusCollection [name=filter_animal_name]").val('');
            $("#filterSurplusCollection [name=filter_animal_name]").prop('disabled', false);
            $("#filterSurplusCollection .animal-select2").prop('disabled', true);
            $("#filterSurplusCollection .animal-select2").val(null).trigger('change');
        }
        else if (checkedOption == 'by_id') {
            $("#filterSurplusCollection [name=filter_animal_name]").val('');
            $("#filterSurplusCollection [name=filter_animal_name]").prop('disabled', true);
            $("#filterSurplusCollection .animal-select2").prop('disabled', false);
        }
        else {
            $("#filterSurplusCollection [name=filter_animal_name]").val('');
            $("#filterSurplusCollection [name=filter_animal_name]").prop('disabled', true);
            $("#filterSurplusCollection .animal-select2").prop('disabled', true);
            $("#filterSurplusCollection .animal-select2").val(null).trigger('change');
        }
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
                url:"{{ route('surplus-collection.editSelectedRecords') }}",
                data:{
                    items: ids,
                    institution_id: $('#editSelectedRecords [name=edit_selection_supplier_id]').val(),
                    origin: $('#editSelectedRecords [name=edit_selection_origin]').val(),
                    age_group: $('#editSelectedRecords [name=edit_selection_age_group]').val(),
                    supplier_level: $('#editSelectedRecords [name=edit_selection_supplier_level]').val(),
                    surplus_status: $('#editSelectedRecords [name=edit_selection_surplus_status]').val(),
                },
                success:function(data){
                    $('#editSelectedRecords').find('form').trigger('reset');
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

    $('#filterSurplusCollection input[name=empty_institution]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterSurplusCollection .institution-select2").val(null).trigger('change');
            $("#filterSurplusCollection .institution-select2").prop('disabled', true);
        }
        else
            $("#filterSurplusCollection .institution-select2").prop('disabled', false);
    });

    $('#filterSurplusCollection input[name=empty_contact]:checkbox').change(function () {
        if($(this).is(':checked')) {
            $("#filterSurplusCollection .contact-select2").val(null).trigger('change');
            $("#filterSurplusCollection .contact-select2").prop('disabled', true);
        }
        else
            $("#filterSurplusCollection .contact-select2").prop('disabled', false);
    });

    $('#printOptions').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countSurplusVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $("#printOptionsDialog [name=print_document_type],[name=print_language],[name=print_pictures]").prop('checked', false);

        $('#printOptionsDialog #allRecordsOption').addClass("d-none");

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

        if (ids.length == $('#countSurplusTotal').val()) {
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
        else {
            $.ajax({
                type:'GET',
                url:"{{ route('surplus-collection.printSurplusList') }}",
                data: {
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

    $('#exportSurpluses').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countSurplusVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportSurplusList').modal('show');
    });

    $('#exportSurplusList').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportSurplusList [name=export_option]:checked').val();

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
            var url = "{{route('surplus.export')}}?items=" + ids;
            window.location = url;

            $('#exportSurplusList').modal('hide');
        }
    });

    $('#createMailingAddressList').on('click', function (event) {
        event.preventDefault();
        var url = "{{route('surplus-collection.createSurplusCollectionAddressList')}}";
        window.location = url;
    });

});

</script>

@endsection
