@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      @if (Auth::user()->hasPermission('codes.create'))
        <a href="{{ route('codes.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterCode">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('codes.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('codes.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
      @if (Auth::user()->hasPermission('codes.export-survey'))
        <a id="exportCodesRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportCodes">
            <i class="fas fa-fw fa-save"></i> Export
        </a>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Codes') }}</h1>
  <p class="text-white">List of all codes</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('codes.removeFromCodeSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($codes->isEmpty())
      <div class="table-responsive">
        <table class="table clickable table-hover table-bordered datatable" width="100%" cellspacing="0">
          <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                <th>Site name</th>
                <th>Url</th>
                <th>Remarks</th>
                <th>Login username</th>
                <th>Login password</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $codes as $code )
            <tr class="@if ($code->only_for_john) text-danger @endif" @if (Auth::user()->hasPermission('codes.update')) data-url="{{ route('codes.edit', [$code->id]) }}" @endif>
                <td class="no-click">
                    <input type="checkbox" class="selector" value="{{ $code->id }}" />
                </td>
                <td>{{ $code->siteName }}</td>
                <td class="no-click"><a href="//{{ $code->siteUrl }}" target="_blank"><u>{{ $code->siteUrl }}</u></a></td>
                <td>{{ $code->siteRemarks }}</td>
                <td>{{ $code->loginUsername }}</td>
                <td>{{ ($code->loginPassword != null) ? Crypt::decryptString($code->loginPassword) : '' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p> No codes are added yet </p>

      @endunless
    </div>
  </div>

  @include('codes._modal', ['modalId' => 'filterCode'])

  @include('export_excel.export_options_modal', ['modalId' => 'exportCodes'])
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
                url:"{{ route('codes.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#exportCodesRecords').on('click', function () {
        var table = $('.datatable').DataTable();

        var count_selected_records = $(":checked.selector").length;
        var count_page_records = table.rows().count();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportCodes').modal('show');
    });

    $('#exportCodes').on('submit', function (event) {
        event.preventDefault();

        var table = $('.datatable').DataTable();

        var export_option = $('#exportCodes [name=export_option]:checked').val();

        var ids = [];
        if(export_option == "selection") {
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }
        else {
            table.rows().every( function (rowIdx) {
                var row = $(this.node());
                ids.push(row.find('input').val());
            });
        }

        if(ids.length == 0)
            alert("There are not records to export.");
        else {
            var url = "{{route('codes.export')}}?items=" + ids;
            window.location = url;

            $('#exportCodes').modal('hide');
        }
    });

</script>

@endsection
