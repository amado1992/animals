@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        @if (Auth::user()->hasPermission('crates.create'))
            <a href="{{ route('crates.create') }}" class="btn btn-light">
                <i class="fas fa-fw fa-plus"></i> Add
            </a>
        @endif
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterCrates">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('crates.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        @if (Auth::user()->hasPermission('crates.delete'))
            <button type="button" id="deleteSelectedItems" class="btn btn-light">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </button>
        @endif
        @if (Auth::user()->hasPermission('crates.export-survey'))
            <a id="exportCratesRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportCrates">
                <i class="fas fa-fw fa-save"></i> Export
            </a>
        @endif
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-box-open mr-2"></i> {{ __('Crates') }}</h1>
    <p class="text-white">Crates are boxes in which animals are transported</p>

    <div class="d-flex flex-row justify-content-between items-center text-white mb-2">
        <div class="d-flex align-items-center">
            <label class="text-sm pr-2 pt-1">Order by:</label>&nbsp;
            {!! Form::open(['id' => 'cratesOrderByForm', 'route' => 'crates.orderBy', 'method' => 'GET']) !!}
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
            Page: {{$crates->currentPage()}} | Records:&nbsp;
            {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'crates.recordsPerPage', 'method' => 'GET']) !!}
                {!! Form::text('recordsPerPage', $crates->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
            {!! Form::close() !!}
            &nbsp;| Total: {{$crates->total()}}
        </div>
    </div>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row items-center mb-2">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('crates.removeFromCrateSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($crates->isEmpty())
      <div class="table-responsive">
        <table class="table table-hover clickable table-bordered">
          <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                <th>Name</th>
                <th>Size (l x w x h)</th>
                <th class="text-right">Vol.weight</th>
                <th>IATA Code</th>
                <th>Currency</th>
                <th>Cost price</th>
                <th>Sale price</th>
                <th>Quantity</th>
                <th># animals</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $crates as $crate )
            <tr data-url="{{ route('crates.show', [$crate->id]) }}">
                <td class="no-click">
                    <div class="d-flex flex-row">
                        <input type="checkbox" class="selector" value="{{ $crate->id }}" />
                    </div>
                </td>
                <td>{{ $crate->name }}</a></td>
                <td>{{ $crate->length }} x {{ $crate->wide }} x {{ $crate->height }}</td>
                <td class="text-right">{{ number_format($crate->weight, 2, '.', '') }}</td>
                <td>{{ $crate->iata_code }}</td>
                <td>{{ $crate->currency }}</td>
                <td @if ($crate->cost_price_changed) class='text-danger' @endif>{{ number_format($crate->cost_price, 2, '.', '') }}</td>
                <td @if ($crate->sale_price_changed) class='text-danger' @endif>{{ number_format($crate->sale_price, 2, '.', '') }}</td>
                <td>{{ $crate->animal_quantity }}</td>
                <td>{{ $crate->animals->count() }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $crates->links() }}
      @else

        <p> No crates found. </p>

      @endunless
    </div>
  </div>

  @include('crates.filter_modal', ['modalId' => 'filterCrates'])

  @include('export_excel.export_options_modal', ['modalId' => 'exportCrates'])

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
        $('#cratesOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#cratesOrderByForm').submit();
    });

    $('#filterCrates').on('hidden.bs.modal', function () {
        $("#filterCrates .animal-select2").val(null).trigger('change');
        $(this).find('form').trigger('reset');
    });

    //Select2 animal selection
    $('[name=filter_crate_animal_id]').on('change', function () {
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
                    $('[name=filter_crate_animal_id]').append(newOption);
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
                url:"{{ route('crates.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#exportCratesRecords').on('click', function () {
        var table = $('.datatable').DataTable();

        var count_selected_records = $(":checked.selector").length;
        var count_page_records = table.rows().count();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportCrates').modal('show');
    });

    $('#exportCrates').on('submit', function (event) {
        event.preventDefault();

        var table = $('.datatable').DataTable();

        var export_option = $('#exportCrates [name=export_option]:checked').val();

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
            var url = "{{route('crates.export')}}?items=" + ids;
            window.location = url;

            $('#exportCrates').modal('hide');
        }
    });

</script>

@endsection
