@extends('layouts.admin')

@section('header-content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                @if (Auth::user()->hasPermission('contacts.create'))
                    <a href="{{ route('contacts.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Create
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterContacts">
                    <i class="fas fa-fw fa-filter"></i> Filter
                </button>
                <a href="{{ route('contacts.doublesView') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-filter"></i> Find doubles
                </a>
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
                @if (Auth::user()->hasRole('admin'))
                    <a id="exportContactRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportContacts">
                        <i class="fas fa-fw fa-save"></i> Export
                    </a>
                @endif
            </div>

            <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Contacts') }}</h1>
            <p class="text-white">Here you can manage all contacts of Zoo Services</p>
        </div>
    </div>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row justify-content-between items-center mb-3">

            <div class="d-flex align-items-center">
                <span class="mr-1">Filtered on:</span>
                @foreach ($filterData as $key => $value)
                    <a href="{{ route('contacts.removeFromContactSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                @endforeach
            </div>

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
        </div>

        @unless($contacts->isEmpty())
            <div class="table-responsive mb-2">
              <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" name="selectAll" />
                            <input type="hidden" id="countContactsVisible" value="{{ ($contacts->count() > 0) ? $contacts->count() : 0 }}" />
                        </th>
                        <th>Name</th>
                        <th>Institution</th>
                        <th>Type</th>
                        <th>Email address</th>
                        <th>Mailing category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $contacts as $contact )
                    <tr data-url="{{ route('contacts.show', [$contact->id]) }}" @if ($contact->deleted_at != null)
                        style="text-decoration: line-through;"
                    @endif>
                        <td class="no-click">
                            @if (Auth::user()->hasRole('admin'))
                                <input type="checkbox" class="selector" value="{{ $contact->id }}" />
                            @endif
                        </td>
                        <td>{{ $contact->title }} {{ $contact->name }}</td>
                        <td>@if($contact->organisation) {{ $contact->organisation->name }} @else - @endif</td>
                        <td>@if($contact->organisation) {{ $contact->organisation->type->label }} @else - @endif</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ ($contact->mailing_category != null) ? $mailing_categories[$contact->mailing_category] : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
            {{$contacts->links()}}
        @else

            <p> No contacts are added yet </p>

        @endunless
        </div>
  </div>

  @include('contacts.filter_modal', ['modalId' => 'filterContacts'])

  @include('contacts.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

  @include('export_excel.export_options_modal', ['modalId' => 'exportContacts'])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
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

    $("#filterContacts #resetBtn").click(function() {
        $("#filterContacts input[name=filter_name]").prop('disabled', false);
        $("#filterContacts input[name=filter_email]").prop('disabled', false);
        $("#filterContacts [name=filter_institution_type]").prop('disabled', false);
        $("#filterContacts input[name=filter_institution_name]").prop('disabled', false);
        $("#filterContacts").find('form').trigger('reset');
    });

    $('#editSelectedRecords').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
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
                url:"{{ route('contacts.editSelectedRecords') }}",
                data:{
                    items: ids,
                    title: $('#editSelectedRecords [name=title]').val(),
                    first_name: $('#editSelectedRecords [name=first_name]').val(),
                    last_name: $('#editSelectedRecords [name=last_name]').val(),
                    institution_id: $('#editSelectedRecords [name=institution_id]').val(),
                    institution_type: $('#editSelectedRecords [name=organisation_type]').val(),
                    level: $('#editSelectedRecords [name=level]').val(),
                    mailing_category: $('#editSelectedRecords [name=mailing_category]').val()
                },
                success:function(data){
                    location.reload();
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

    $('#exportContacts').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportContacts [name=export_option]:checked').val();

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
            var url = "{{route('contacts.export')}}?items=" + ids;
            window.location = url;

            $('#exportContacts').modal('hide');
        }
    });

</script>

@endsection
