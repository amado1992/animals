@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        @if (Auth::user()->hasPermission('airfreights.create'))
            <a href="{{ route('airfreights.create') }}" class="btn btn-light">
                <i class="fas fa-fw fa-plus"></i> Add
            </a>
        @endif
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterAirfreights">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('airfreights.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        @if (Auth::user()->hasPermission('airfreights.delete'))
            <button type="button" id="deleteSelectedItems" class="btn btn-light">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </button>
        @endif
        <a id="exportAirfreightRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportAirfreights">
            <i class="fas fa-fw fa-save"></i> Export
        </a>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-box-open mr-2"></i> {{ __('Airfreight') }}</h1>
    <p class="text-white">Airfreights are the freight prices between origin and delivery country.</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-2">
            <div class="d-flex flex-row align-items-center">
                <span class="mr-1">Filtered on:</span>
                @foreach ($filterData as $key => $value)
                    <a href="{{ route('airfreights.removeFromAirfreightSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                @endforeach
            </div>
            <div class="d-flex align-items-center">
                Page: {{$airfreights->currentPage()}} | Records:&nbsp;
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'airfreights.recordsPerPage', 'method' => 'GET']) !!}
                    {!! Form::text('recordsPerPage', $airfreights->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
                &nbsp;| Total: {{$airfreights->total()}}
            </div>
        </div>

      @unless($airfreights->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll" name="selectAll" />
                        <input type="hidden" id="countAirfreightVisible" value="{{ ($airfreights->count() > 0) ? $airfreights->count() : 0 }}" />
                    </th>
                    <th>Offered date</th>
                    <th>Departure continent</th>
                    <th>Arrival continent</th>
                    <th>Curr</th>
                    <th>Cost vol kg</th>
                    <th>Cost lowerdeck</th>
                    <th>Cost maindeck</th>
                    <th>Transport agent</th>
                    <th>Remarks</th>
                    <th>Documents</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $airfreights as $airfreight )
                <tr @if (Auth::user()->hasPermission('airfreights.update')) data-url="{{ route('airfreights.show', [$airfreight->id]) }}" @endif>
                    <td class="no-click">
                        <div class="d-flex flex-row">
                            <input type="checkbox" class="selector mr-2" value="{{ $airfreight->id }}" />
                            @if (Auth::user()->hasPermission('airfreights.upload-files'))
                                <a href="#" title="Upload file" id="uploadFile" data-toggle="modal" data-id="{{ $airfreight->id }}">
                                    <i class="fas fa-file-upload"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                    <td>{{ ($airfreight->offered_date != null) ? date('Y-m-d', strtotime($airfreight->offered_date)) : '' }}</td>
                    <td>{{ ($airfreight->from_continent != null) ? $airfreight->from_continent->name : "" }}</td>
                    <td>{{ ($airfreight->to_continent != null) ? $airfreight->to_continent->name : "" }}</td>
                    <td>{{ $airfreight->currency }}</td>
                    <td>{{ number_format($airfreight->volKg_weight_cost, 2, '.', '') }}</td>
                    <td>{{ number_format($airfreight->lowerdeck_cost, 2, '.', '') }}</td>
                    <td>{{ number_format($airfreight->maindeck_cost, 2, '.', '') }}</td>
                    <td>{{ ($airfreight->agent) ? $airfreight->agent->email : '' }}</td>
                    <td>{{ $airfreight->remarks }}</td>
                    <td>
                        @foreach(Storage::allFiles('public/airfreights_docs/'.$airfreight->id) as $doc)
                            @php
                                $file = pathinfo($doc);
                            @endphp
                            <div class="d-inline-flex">
                                @if (Auth::user()->hasPermission('airfreights.delete-files'))
                                    <a href="{{ route('airfreights.delete_file', [$airfreight->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                @endif
                                <a href="{{Storage::url('airfreights_docs/'.$airfreight->id.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                            </div><br>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
            {{$airfreights->links()}}
        </div>
      @else

        <p> No airfreights found. </p>

      @endunless
    </div>
  </div>

  @include('airfreights.filter_modal', ['modalId' => 'filterAirfreights'])

  @include('uploads.upload_modal', ['modalId' => 'uploadAirfreightFile', 'route' => 'airfreights.upload'])

  @include('export_excel.export_options_modal', ['modalId' => 'exportAirfreights'])

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
                url:"{{ route('airfreights.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    if (data.message.trim().length > 0)
                        alert(data.message);
                    location.reload();
                }
            });
        }
    });

    /* Upload airfreight file */
    $('body').on('click', '#uploadFile', function () {
        var airfreightId = $(this).data('id');

        $('#uploadAirfreightFile').modal('show');
        $('#uploadAirfreightFile [name=id]').val(airfreightId);
    });

    $('#filterAirfreights').on('hidden.bs.modal', function () {
        $('#filterAirfreights .contact-select2').val(null).trigger('change');
        $(this).find('form').trigger('reset');
    });

    $('#exportAirfreightRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countAirfreightVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportAirfreights').modal('show');
    });

    $('#exportAirfreights').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportAirfreights [name=export_option]:checked').val();

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
            var url = "{{route('airfreights.export')}}?items=" + ids;
            window.location = url;

            $('#exportAirfreights').modal('hide');
        }
    });

</script>

@endsection

