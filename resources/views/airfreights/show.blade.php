@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('airfreights.index') }}">Airfreights</a></li>
    <li class="breadcrumb-item active">{{ ($airfreight->from_continent != null) ? $airfreight->from_continent->name : "" }} - {{ ($airfreight->to_continent != null) ? $airfreight->to_continent->name : "" }}</li>
</ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-9">
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center text-white">
                <h1 class="h1">{{ ($airfreight->from_continent != null) ? $airfreight->from_continent->name : "" }} - {{ ($airfreight->to_continent != null) ? $airfreight->to_continent->name : "" }}</h1>
            </div>
        </div>
    </div>
</div>

@endsection

@section('main-content')

<div class="card shadow mb-2">
    <div class="card-header d-inline-flex justify-content-between">
        <h4>Standard airfreights details</h4>
        <div class="d-flex">
            @if (Auth::user()->hasPermission('airfreights.update'))
                <a href="{{ route('airfreights.edit', [$airfreight->id]) }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-fw fa-edit"></i> Edit
                </a>
            @endif
        </div>
    </div>
    <div class="card-body" style="overflow-x: auto; overflow-y: hidden;">
        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Offered date</th>
                    <th>Departure continent</th>
                    <th>Arrival continent</th>
                    <th>Type</th>
                    <th>Curr</th>
                    <th>Cost vol kg</th>
                    <th>Cost lowerdeck</th>
                    <th>Cost maindeck</th>
                    <th>Documents</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ ($airfreight->offered_date != null) ? date('Y-m-d', strtotime($airfreight->offered_date)) : '' }}</td>
                    <td>{{ ($airfreight->from_continent != null) ? $airfreight->from_continent->name : "" }}</td>
                    <td>{{ ($airfreight->to_continent != null) ? $airfreight->to_continent->name : "" }}</td>
                    <td>{{ ucfirst($airfreight->type) }}</td>
                    <td>{{ $airfreight->currency }}</td>
                    <td data-idAirfreight="{{ $airfreight->id }}"><input type="text" class="input-group input-group-sm bordered updatePrecies" name="volKg_weight_cost" value="{{ number_format($airfreight->volKg_weight_cost, 2, '.', '') }}"></td>
                    <td data-idAirfreight="{{ $airfreight->id }}"><input type="text" class="input-group input-group-sm bordered updatePrecies" name="lowerdeck_cost" value="{{ number_format($airfreight->lowerdeck_cost, 2, '.', '') }}"></td>
                    <td data-idAirfreight="{{ $airfreight->id }}"><input type="text" class="input-group input-group-sm bordered updatePrecies" name="maindeck_cost" value="{{ number_format($airfreight->maindeck_cost, 2, '.', '') }}"></td>
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
                    <td>{{ $airfreight->created_at }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow mb-2">
    <div class="card-header d-inline-flex justify-content-between">
        <h4>{{ $airfreightsRelated->total() }} related airfreights</h4>
    </div>
    <div class="card-body">
        @unless($airfreightsRelated->isEmpty())
        <div class="row">
            <div class="col-md-6 d-inline-flex">
                <label style="font-size: 16px;">Order by:</label>&nbsp;
                <form method="GET" action="{{ route('airfreights.show' , [$airfreight->id]) }}" id="standardAirfreightsByForm">
                    <select class="custom-select custom-select-sm w-auto" id="orderByField" name="orderByField">
                        <option value="created_at" @if(isset($orderByField) && $orderByField == "created_at") selected @endif>Create Date</option>
                    </select>
                    <select id="orderByDirection" name="orderByDirection" class="custom-select custom-select-sm w-auto">
                        <option @if(!isset($orderByDirection)) selected @endif value="desc">Descending</option>
                        <option @if(isset($orderByDirection) && $orderByDirection == 'asc') selected @endif value="asc">Ascending</option>
                    </select>
                </form>
            </div>
            <div class="col-md-6" style="text-align: right;">
                @if (Auth::user()->hasPermission('airfreights.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn btn-secondary mr-2">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
            </div>
        </div>
        <hr>
        <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
            <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" name="selectAll" />
                        </th>
                        <th>Offered date</th>
                        <th>Departure continent</th>
                        <th>Arrival continent</th>
                        <th>Type</th>
                        <th>Curr</th>
                        <th>Cost vol kg</th>
                        <th>Cost lowerdeck</th>
                        <th>Cost maindeck</th>
                        <th>Documents</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $airfreightsRelated as $airfreight )
                        <tr>
                            <td class="no-click">
                                <div class="d-flex flex-row">
                                    <input type="checkbox" class="selector mr-2" value="{{ $airfreight->id }}" />
                                </div>
                            </td>
                            <td>{{ ($airfreight->offered_date != null) ? date('Y-m-d', strtotime($airfreight->offered_date)) : '' }}</td>
                            <td>{{ ($airfreight->from_continent != null) ? $airfreight->from_continent->name : "" }}</td>
                            <td>{{ ($airfreight->to_continent != null) ? $airfreight->to_continent->name : "" }}</td>
                            <td>{{ ucfirst($airfreight->type) }}</td>
                            <td>{{ $airfreight->currency }}</td>
                            <td>{{ number_format($airfreight->cost_volKg, 2, '.', '') }}</td>
                            <td>{{ number_format($airfreight->lowerdeck_cost, 2, '.', '') }}</td>
                            <td>{{ number_format($airfreight->pallet_cost_value, 2, '.', '') }}</td>
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
                            <td>{{ $airfreight->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $airfreightsRelated->links() }}
        @else
            This airfreights has not related
        @endunless
    </div>
</div>
@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#orderByField').on('change', function () {
            $('#standardAirfreightsByForm').submit();
        });

        $('#orderByDirection').on('change', function () {
            $('#standardAirfreightsByForm').submit();
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
                var deleteSelectedItems = $("#deleteSelectedItems").html();
                $("#deleteSelectedItems").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
                $.ajax({
                    type:'POST',
                    url:"{{ route('airfreights.deleteItems') }}",
                    data:{items: ids},
                    success:function(data){
                        if(data.message){
                            $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                        }else{
                            location.reload();
                        }
                    },complete(){
                        $("#deleteSelectedItems").html(deleteSelectedItems);
                    }
                });
            }
        });

        $(".updatePrecies").on("change", function(){
            var sender = $(this);
            var field = sender.attr("name");
            var value = sender.val();
            var parent = sender.parent("td");
            var id = parent.attr("data-idAirfreight");
            $.ajax({
                type:'POST',
                url:"{{ route('airfreights.updatePrecies') }}",
                data:{
                    id: id,
                    field: field,
                    value: value
                },
                beforeSend: function() {
                    sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
                },
                success:function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                    }
                },
                complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        });

    });

</script>

@endsection
