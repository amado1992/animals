@extends('layouts.admin')

@section('header-content')

    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                @if (Auth::user()->hasPermission('classifications.read'))
                    <a href="{{ route('animals.manage_classifications') }}" class="btn btn-dark">Manage classifications</a>
                @endif
                @if (Auth::user()->hasPermission('animals.create'))
                    <a href="{{ route('animals.create') }}" class="btn btn-light">
                        <i class="fas fa-fw fa-plus"></i> Add
                    </a>
                @endif
                <button id="filterAnimalsBtn" type="button" class="btn btn-light" data-toggle="modal" data-target="#filterAnimals">
                    <i class="fas fa-fw fa-search"></i> Filter
                </button>
                <a href="{{ route('animals.showAll') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-window-restore"></i> Show all
                </a>
                @if (Auth::user()->hasPermission('animals.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn btn-light">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
                @if (Auth::user()->hasPermission('animals.export-survey'))
                    <a id="exportAnimalsRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportAnimals">
                        <i class="fas fa-fw fa-save"></i> Export
                    </a>
                @endif
                @if (Auth::user()->hasRole(['admin', 'transport', 'rossmery']))
                    <a href="#" class="btn btn-light" data-toggle="modal" data-target="#cratesForSpeciesGroup">
                        <i class="fas fa-fw fa-box-open"></i> Assign crates
                    </a>
                @endif
            </div>

            <h1 class="h1 text-white"><i class="fas fa-fw fa-paw mr-2"></i> {{ __('Animals') }}</h1>
            <p class="text-white">Manage all animals which will be traded by Zoo Services</p>
        </div>
    </div>

    <div class="d-flex flex-row justify-content-between items-center text-white mb-2">
        <div class="d-flex align-items-center">
            <label class="mr-1">Order by:</label>
            {!! Form::open(['id' => 'animalsOrderByForm', 'route' => 'animals.orderBy', 'method' => 'GET']) !!}
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
            Page: {{$animals->currentPage()}} | Records:&nbsp;
            {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'animals.recordsPerPage', 'method' => 'GET']) !!}
                {!! Form::text('recordsPerPage', $animals->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
            {!! Form::close() !!}
            &nbsp;| Total: {{$animals->total()}}
        </div>
    </div>
@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row items-center mb-2">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('animals.removeFromAnimalsSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($animals->isEmpty())

      <div class="table-responsive mb-2">
        <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
                <th style="width: 4%;">
                    <input type="checkbox" id="selectAll" name="selectAll" />
                    <input type="hidden" id="countAnimalsVisible" value="{{ ($animals->count() > 0) ? $animals->count() : 0 }}" />
                </th>
                <th style="width: 10%;"></th>
                <th style="width: 9%;">Code</th>
                <th style="width: 18%;">Scientific name</th>
                <th style="width: 17%;">Common name</th>
                <th style="width: 17%;">Spanish name</th>
                <th style="width: 17%;">Chinese name</th>
                <th style="width: 6%;">Class</th>
                <th style="width: 6%;">Order</th>
                <th style="width: 6%;">Family</th>
                <th style="width: 6%;">Genus</th>
                <th style="width: 5%;">Iata</th>
            </tr>
          </thead>
          <tbody>
            @foreach($animals as $animal)
            <tr @if (Auth::user()->hasPermission('animals.update')) data-url="{{ route('animals.show', [$animal->id]) }}" @endif>
                <td class="no-click">
                    <input type="checkbox" class="selector" value="{{ $animal->id }}" />
                </td>
                <td class="text-center">
                    @if ($animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$animal->id.'/'.$animal->catalog_pic))
                        <img src="{{ asset('storage/animals_pictures/'.$animal->id.'/'.$animal->catalog_pic) }}" class="rounded" style="max-width:70px;" alt="" />
                    @else
                        @if(!empty($animal->imagen_first))
                            <img src="{{ asset('storage/animals_pictures/'.$animal->id.'/'.$animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                        @else
                            <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                        @endif
                    @endif
                </td>
                <td>{{ $animal->code_number }}</td>
                <td>{{ $animal->scientific_name }}</td>
                <td>{{ $animal->common_name }}</td>
                <td>{{ $animal->spanish_name }}</td>
                <td>{{ $animal->chinese_name ?? "" }}</td>
                @if ($animal->classification != null)
                    <td>{{ $animal->classification->class->common_name }}</td>
                    <td>{{ $animal->classification->order->common_name }}</td>
                    <td>{{ $animal->classification->family->common_name }}</td>
                    <td>{{ $animal->classification->common_name }}</td>
                @else
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                @endif
                <td>{{ $animal->iata_code }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $animals->links() }}
      @else

        <p> No animals found. </p>

      @endunless
    </div>
  </div>

  @include('animals.filter_modal', ['modalId' => 'filterAnimals'])

  @include('export_excel.export_options_modal', ['modalId' => 'exportAnimals'])

  @include('animals.assign_crates_modal', ['modalId' => 'cratesForSpeciesGroup', 'animalId' => 0])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);

        /*$('.animalDatatable').DataTable({
            "dom": '<"top"i>rt<"clear">',
            "scrollX": true
        });*/
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
        $('#animalsOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#animalsOrderByForm').submit();
    });

    $('#filterAnimalsBtn').on('click', function () {
        $('#filterAnimals').modal('show');

        $("#filterAnimals input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterAnimals input[name=filter_animal_option]").trigger('change');

        $("#filterAnimals .animal-select2").val(null).trigger('change');

        $("#filterAnimals input[name=filter_in_standard_list]").change();
    });

    $('#filterAnimals input[name=filter_in_standard_list]:radio').change(function () {
        var inStandardList = $('#filterAnimals input[name=filter_in_standard_list]:checked').val();

        if(inStandardList == 'yes')
            $("#filterAnimals [name=filter_in_standard_list_public]").prop('disabled', false);
        else {
            $("#filterAnimals [name=filter_in_standard_list_public]").prop('checked', false);
            $("#filterAnimals [name=filter_in_standard_list_public]").prop('disabled', true);
        }
    });

    $('#filterAnimals input[name=filter_animal_option]').change(function() {
        var checkedOption = $('#filterAnimals input[name=filter_animal_option]:checked').val();

        if (checkedOption == 'by_name') {
            $("#filterAnimals [name=filter_animal_name]").prop('disabled', false);
            $("#filterAnimals .animal-select2").prop('disabled', true);
            $("#filterAnimals .animal-select2").val(null).trigger('change');
        }
        else {
            $("#filterAnimals [name=filter_animal_name]").prop('disabled', true);
            $("#filterAnimals .animal-select2").prop('disabled', false);
        }
    });

    $("#filterAnimals #resetBtn").click(function() {
        $("#filterAnimals input[name=filter_animal_option][value=by_id]").prop('checked', true);
        $("#filterAnimals input[name=filter_animal_option]").trigger('change');

        $("#filterAnimals .animal-select2").val(null).trigger('change');

        $("#filterAnimals").find('form').trigger('reset');
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

    $("#filter_class_id").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getOrdersByClass') }}",
            data:{value: value},
            success:function(data){
                $("#filter_order_id").empty();
                $('#filter_order_id').append('<option value="">- select -</option>');
                $.each(data.orders, function(key, value) {
                    $('#filter_order_id').append('<option value="'+ value +'">'+ key +'</option>');
                });
                $("#filter_order_id").change();
            }
        });
    });

    $("#filter_order_id").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getFamiliesByOrder') }}",
            data:{value: value},
            success:function(data){
                $("#filter_family_id").empty();
                $('#filter_family_id').append('<option value="">- select -</option>');
                $.each(data.families, function(key, value) {
                    $('#filter_family_id').append('<option value="'+ value +'">'+ key +'</option>');
                });
                $("#filter_family_id").change();
            }
        });
    });

    $("#filter_family_id").change(function() {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('classifications.getGenusByFamily') }}",
            data:{value: value},
            success:function(data){
                $("#filter_genus_id").empty();
                $('#filter_genus_id').append('<option value="">- select -</option>');
                $.each(data.genuss, function(key, value) {
                    $('#filter_genus_id').append('<option value="'+ value +'">'+ key +'</option>');
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
                url:"{{ route('animals.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#exportAnimalsRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countAnimalsVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportAnimals').modal('show');
    });

    $('#exportAnimals').on('submit', function (event) {
        event.preventDefault();

        var table = $('.datatable').DataTable();

        var export_option = $('#exportAnimals [name=export_option]:checked').val();

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
            var url = "{{route('animals.export')}}?items=" + ids;
            window.location = url;

            $('#exportAnimals').modal('hide');
        }
    });

    $('#cratesForSpeciesGroup').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#cratesForSpeciesGroup [name=iata_code]').on('change', function () {
        $.ajax({
            type:'POST',
            url:"{{ route('crates.getCratesByIata') }}",
            data:{
                iata: $('#cratesForSpeciesGroup [name=iata_code]').val()
            },
            success:function(data){
                $("#cratesForSpeciesGroup #crates_list").empty();
                $.each(data.crates, function(key, value) {
                    $('#cratesForSpeciesGroup #crates_list').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#cratesForSpeciesGroup').on('submit', function (event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        var crates_list = $("#cratesForSpeciesGroup #crates_list").val();

        if(ids.length == 0)
            alert("There are not species selected.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('animals.assignCratesToSpeciesGroup') }}",
                data:{
                    ids: ids,
                    crates_list: crates_list
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
