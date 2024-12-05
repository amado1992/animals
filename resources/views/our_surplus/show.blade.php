@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('our-surplus.index') }}">Standard surplus</a></li>
    <li class="breadcrumb-item active">{{ $ourSurplus->animal->common_name }}</li>
</ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-9">
        <div class="d-flex align-items-center">
            @if ($ourSurplus->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$ourSurplus->animal->id.'/'.$ourSurplus->animal->catalog_pic))
                <img src="{{ asset('storage/animals_pictures/'.$ourSurplus->animal->id.'/'.$ourSurplus->animal->catalog_pic) }}" class="float-left mr-4 rounded" style="max-width:70px;" alt="" />
            @else
                @if(!empty($ourSurplus->animal->imagen_first))
                    <img src="{{ asset('storage/animals_pictures/'.$ourSurplus->animal->id.'/'.$ourSurplus->animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                @else
                    <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                @endif
            @endif
            <div class="d-flex align-items-center text-white">
                <h1 class="h1">{{ $ourSurplus->availability_field }} | {{$ourSurplus->animal->common_name}}</h1>
                <span>&nbsp;-&nbsp;{{ $ourSurplus->animal->scientific_name }}</span>
                <span class="ml-5">{{ ($ourSurplus->region != null) ? 'ex ' . $ourSurplus->region->name : '' }}</span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('main-content')

<div class="card shadow mb-2">
    <div class="card-header d-inline-flex justify-content-between">
        <h4>Standard surplus details</h4>
        <div class="d-flex">
            @if (Auth::user()->hasPermission('standard-surplus.update'))
                <a href="{{ route('our-surplus.edit', [$ourSurplus->id]) }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-fw fa-edit"></i> Edit
                </a>
            @endif

            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    @if (Auth::user()->hasPermission('animals.read'))
                    <a class="dropdown-item" href="{{ route('animals.show', [$ourSurplus->animal_id]) }}">View Animal</a>
                    @endif
                    @if (Auth::user()->hasPermission('standard-surplus.delete'))
                        {!! Form::open(['method' => 'DELETE', 'route' => ['our-surplus.destroy', $ourSurplus->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                            <a href="#" class="dropdown-item text-danger" onclick="$(this).closest('form').submit();">Delete standard surplus</a>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>

        </div>
    </div>
    <div class="card-body" style="overflow-x: auto; overflow-y: hidden;">
        <table class="table-sm table-bordered w-100">
            <thead>
                <tr class="align-top text-center">
                    <th></th>
                    <th colspan="6">PRICES</th>
                    <th colspan="6">INFORMATION</th>
                    <th colspan="3">REMARKS</th>
                </tr>
                <tr class="table-secondary text-center">
                    <th style="width: 75px;">Availability</th>
                    <th style="width: 25px;"></th>
                    <th style="width: 25px;"></th>
                    <th style="width: 200px;">M</th>
                    <th style="width: 200px;">F</th>
                    <th style="width: 200px;">U</th>
                    <th style="width: 200px;">P</th>
                    <th style="width: 180px;">Location</th>
                    <th style="width: 40px;">Public</th>
                    <th style="width: 55px;">Origin</th>
                    <th style="width: 80px;">Age group</th>
                    <th style="width: 60px;">Size</th>
                    <th style="width: 100px;">Areas to offer</th>
                    <th style="width: 170px;">Remarks</th>
                    <th style="width: 170px;">Internal remarks</th>
                    <th style="width: 170px;">Special conditions</th>
                    <th style="width: 170px;">Update date</th>
                </tr>
            </thead>
            <tbody>
                <tr class="align-top">
                    <td class="text-center">{{ $ourSurplus->availability_field }}</td>
                    <td>
                        @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                            <div class="mb-1">Costs:</div>
                        @endif
                        @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                            Sales:
                        @endif
                    </td>
                    <td>
                        @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                            <div class="mb-1">{{ $ourSurplus->cost_currency }}</div>
                        @endif
                        @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                            {{ $ourSurplus->sale_currency }}
                        @endif
                    </td>
                    <td class="text-right" data-idSurplus="{{ $ourSurplus->id }}">
                        @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePrecies" name="costPriceM" value="{{ number_format($ourSurplus->costPriceM, 2, '.', '') }}">
                        @endif
                        @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePreciesSale" name="salePriceM" value="{{ number_format($ourSurplus->salePriceM, 2, '.', '') }}">
                        @endif
                    </td>
                    <td class="text-right" data-idSurplus="{{ $ourSurplus->id }}">
                        @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePrecies" name="costPriceF" value="{{ number_format($ourSurplus->costPriceF, 2, '.', '') }}">
                        @endif
                        @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePreciesSale" name="salePriceF" value="{{ number_format($ourSurplus->salePriceF, 2, '.', '') }}">
                        @endif
                    </td>
                    <td class="text-right" data-idSurplus="{{ $ourSurplus->id }}">
                        @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePrecies" name="costPriceU" value="{{ number_format($ourSurplus->costPriceU, 2, '.', '') }}">
                        @endif
                        @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePreciesSale" name="salePriceU" value="{{ number_format($ourSurplus->salePriceU, 2, '.', '') }}">
                        @endif
                    </td>
                    <td class="text-right" data-idSurplus="{{ $ourSurplus->id }}">
                        @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePrecies" name="costPriceP" value="{{ number_format($ourSurplus->costPriceP, 2, '.', '') }}">
                        @endif
                        @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                            <input type="text" class="input-group input-group-sm bordered updatePreciesSale" name="salePriceP" value="{{ number_format($ourSurplus->salePriceP, 2, '.', '') }}">
                        @endif
                    </td>
                    <td>
                        @if ($ourSurplus->region != null)
                            {{ $ourSurplus->region->name }}
                        @elseif ($ourSurplus->area_region != null)
                            {{ $ourSurplus->area_region->name }}
                        @endif
                    </td>
                    <td class="text-center text-danger">{{ ($ourSurplus->is_public) ? 'YES' : 'NO' }}</td>
                    <td class="text-center" data-idSurplus="{{ $ourSurplus->id }}">
                        {!! Form::select('origin', $origin,  $ourSurplus->origin, ['class' => 'mb1 updateOrigin', 'placeholder' => '- select -']) !!}
                    </td>
                    <td>
                        {{ $ourSurplus->age_field }}
                        {!! ($ourSurplus->bornYear != null) ? ' / ' . $ourSurplus->bornYear : '' !!}
                    </td>
                    <td>{{ $ourSurplus->size_field }}</td>
                    <td>
                        @foreach($ourSurplus->area_regions as $area_region)
                            {{ $area_region->short_cut }}<br>
                        @endforeach
                    </td>
                    <td>{{ $ourSurplus->remarks ?: '' }}</td>
                    <td>{{ $ourSurplus->intern_remarks ?: '' }}</td>
                    <td>{{ $ourSurplus->special_conditions ?: '' }}</td>
                    <td>{{ $ourSurplus->updated_at ?: '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow mb-2">
    <div class="card-header d-inline-flex justify-content-between">
        <h4>{{ $animalRelatedSurplus->total() }} related suppliers</h4>
    </div>
    <div class="card-body">
        @unless($animalRelatedSurplus->isEmpty())
        <div class="row">
            <div class="col-md-6 d-inline-flex">
                <label style="font-size: 16px;">Order by:</label>&nbsp;
                <form method="GET" action="{{ route('our-surplus.show' , [$ourSurplus->id]) }}" id="standardSurplusOrderByForm">
                    <select class="custom-select custom-select-sm w-auto" id="orderByField" name="orderByField">
                        <option value="created_at" @if(isset($orderByField) && $orderByField == "created_at") selected @endif>Create Date</option>
                        <option value="name" @if(isset($orderByField) && $orderByField == "name") selected @endif>Name</option>
                    </select>
                    <select id="orderByDirection" name="orderByDirection" class="custom-select custom-select-sm w-auto">
                        <option @if(!isset($orderByDirection)) selected @endif value="desc">Descending</option>
                        <option @if(isset($orderByDirection) && $orderByDirection == 'asc') selected @endif value="asc">Ascending</option>
                    </select>
                </form>
            </div>
            <div class="col-md-6" style="text-align: right;">
                @if (Auth::user()->hasPermission('surplus-suppliers.delete'))
                    <button type="button" id="deleteSelectedItems" class="btn btn-secondary mr-2">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                @endif
            </div>
        </div>
        <hr>
        <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
            <table class="table-sm table-bordered w-100">
                <thead>
                    <tr class="align-top text-center">
                        <th></th>
                        <th colspan="3">QUANTITY</th>
                        <th colspan="6">PRICES</th>
                        <th colspan="5">INFORMATION</th>
                        <th colspan="3">REMARKS</th>
                    </tr>
                    <tr class="table-secondary text-center">
                        <th></th>
                        <th style="width: 25px;">M</th>
                        <th style="width: 25px;">F</th>
                        <th style="width: 25px;">U</th>
                        <th style="width: 25px;"></th>
                        <th style="width: 25px;"></th>
                        <th style="width: 60px;">M</th>
                        <th style="width: 60px;">F</th>
                        <th style="width: 60px;">U</th>
                        <th style="width: 60px;">P</th>
                        <th style="width: 180px;">Supplier</th>
                        <th style="width: 100px;">Level</th>
                        <th style="width: 180px;">Location</th>
                        <th style="width: 55px;">Origin</th>
                        <th style="width: 80px;">Age group</th>
                        <th style="width: 60px;">Size</th>
                        <th style="width: 170px;">Remarks</th>
                        <th style="width: 170px;">Internal remarks</th>
                        <th style="width: 170px;">Special conditions</th>
                        <th style="width: 380px;">Created Date</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($animalRelatedSurplus as $relatedSurplus)
                    <tr class="align-top">
                        <td><input type="checkbox" class="selector" value="{{ $relatedSurplus->id }}" /></td>
                        <td class="text-center">{{ $relatedSurplus->quantityM >= 0 ? $relatedSurplus->quantityM : 'x' }}</td>
                        <td class="text-center">{{ $relatedSurplus->quantityF >= 0 ? $relatedSurplus->quantityF : 'x' }}</td>
                        <td class="text-center">{{ $relatedSurplus->quantityU >= 0 ? $relatedSurplus->quantityU : 'x' }}</td>
                        <td>
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                Costs:<br>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                Sales:
                            @endif
                        </td>
                        <td>
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                {{ $relatedSurplus->cost_currency }}<br>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                {{ $relatedSurplus->sale_currency }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                {{ number_format($relatedSurplus->costPriceM) }}.00<br>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                {{ number_format($relatedSurplus->salePriceM) }}.00
                            @endif
                        </td>
                        <td class="text-right">
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                {{ number_format($relatedSurplus->costPriceF) }}.00<br>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                {{ number_format($relatedSurplus->salePriceF) }}.00
                            @endif
                        </td>
                        <td class="text-right">
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                {{ number_format($relatedSurplus->costPriceU) }}.00<br>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                {{ number_format($relatedSurplus->salePriceU) }}.00
                            @endif
                        </td>
                        <td class="text-right">
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-cost-prices'))
                                {{ number_format($relatedSurplus->costPriceP) }}.00<br>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.see-sale-prices'))
                                {{ number_format($relatedSurplus->salePriceP) }}.00
                            @endif
                        </td>
                        <td>
                            @if ($relatedSurplus->organisation != null)
                                {{ $relatedSurplus->organisation->name  }}<br>
                                {{ (!empty($relatedSurplus->organisation->city)) ? $relatedSurplus->organisation->city . ', ' : '' }} {{($relatedSurplus->organisation->country) ? $relatedSurplus->organisation->country->name : ''}}
                            @elseif ($relatedSurplus->contact != null)
                                {{ $relatedSurplus->contact->full_name }}<br>
                                {{ ($relatedSurplus->contact->city) ? $relatedSurplus->contact->city . ', ' : '' }}{{ ($relatedSurplus->contact->country) ? $relatedSurplus->contact->country->name : '' }}
                            @endif
                        </td>
                        <td>
                            @if ($relatedSurplus->organisation != null)
                                {{ $relatedSurplus->organisation->level ?? ""  }}<br>
                            @endif
                        </td>
                        <td>
                            {!! ($relatedSurplus->country != null) ? $relatedSurplus->country->name . '<br>' : '' !!}
                            {{ ($relatedSurplus->area_region != null) ? $relatedSurplus->area_region->name : '' }}
                        </td>
                        <td class="text-center">{{ $relatedSurplus->origin_field }}</td>
                        <td>
                            {{ $relatedSurplus->age_field }}
                            {!! ($relatedSurplus->bornYear != null) ? ' / ' . $relatedSurplus->bornYear : '' !!}
                        </td>
                        <td>{{ $relatedSurplus->size_field }}</td>
                        <td>{{ $relatedSurplus->remarks ?: '' }}</td>
                        <td>{{ $relatedSurplus->intern_remarks ?: '' }}</td>
                        <td>{{ $relatedSurplus->special_conditions ?: '' }}</td>
                        <td class="text-center">{{ $relatedSurplus->created_at ?: '' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $animalRelatedSurplus->links() }}
        @else
            This animal has not related surpluses
        @endunless
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between">
                <h5>General Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if (Auth::user()->hasPermission('standard-surplus.upload-files'))
                        <div class="col-md-12">
                            <div class="mb-3">
                                <form action="{{ route('our-surplus.upload') }}" class="dropzone" id="upload-dropzone">
                                    @csrf
                                    <input type="hidden" name="relatedSurplusurplusId" value="{{ $ourSurplus->id }}" />
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row scrroll_style">
                    @foreach($ourSurplus->surplus_pictures as $doc)
                        @php
                            $file = pathinfo($doc);
                        @endphp
                        @if ($file['extension'] == "jpeg" || $file['extension'] == "jpg" || $file['extension'] == "png" || $file['extension'] == "gif")
                            <div class="col-lg-2 col-sm-6 mb-1">
                                <div class="thumbnail">
                                    <div class="thumb" style="overflow: hidden; height: 95px;">
                                        <a href="{{Storage::url('oursurplus_docs/'.$ourSurplus->id.'/'.$file['basename'])}}" data-lightbox="1">
                                            <img src="{{Storage::url('oursurplus_docs/'.$ourSurplus->id.'/'.$file['basename'])}}" alt="" style="width: 100% !important; height: 100% !important; object-fit: cover;" class="img-fluid img-thumbnail">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if (Auth::user()->hasPermission('standard-surplus.delete-files'))
                                <a href="{{ route('our-surplus.delete_file', [$ourSurplus->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')" style="margin: -9px 0px 0 -17px; z-index: 100; font-size: 16px;"><i class="mdi mdi-close-circle"></i></a>
                            @endif
                        @endif
                    @endforeach

                    @foreach($ourSurplus->surplus_pictures as $doc)
                        @php
                            $file = pathinfo($doc);
                        @endphp
                        <div class="col-lg-12 col-sm-12 mb-1 mt-2">
                            @if ($file['extension'] != "jpeg" && $file['extension'] != "jpg" && $file['extension'] != "png" && $file['extension'] != "gif")
                                <img src="/img/file-icons/file.svg" height="25" alt="icon" class="me-2">
                                <a href="{{Storage::url('oursurplus_docs/'.$ourSurplus->id.'/'.$file['basename'])}}" class="text-dark" target="_blank">{{$file['basename']}}</a>
                                @if (Auth::user()->hasPermission('standard-surplus.delete-files'))
                                    <a href="{{ route('our-surplus.delete_file', [$ourSurplus->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')" style="color: red;">(x)</a>,
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Picture oursurpluslist</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            @if (!empty($ourSurplus->oursurplus_pictures_catalog))
                                @foreach($ourSurplus->oursurplus_pictures_catalog as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if ($file['extension'] == "jpeg" || $file['extension'] == "jpg" || $file['extension'] == "png" || $file['extension'] == "gif")
                                        <div class="col-lg-12 col-sm-6 mb-1">
                                            <div class="thumbnail">
                                                <div class="thumb" style="overflow: hidden; height: 146px;">
                                                    <a href="{{Storage::url('oursurplus_pictures/'.$ourSurplus->id.'/'.$file['basename'])}}" data-lightbox="1">
                                                        <img src="{{Storage::url('oursurplus_pictures/'.$ourSurplus->id.'/'.$file['basename'])}}" alt="" style="width: 100% !important; height: 100% !important; object-fit: cover;" class="img-fluid img-thumbnail">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @if (Auth::user()->hasPermission('surplus-suppliers.delete-files'))
                                            <a href="{{ route('our-surplus.delete_file_catalog', [$ourSurplus->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')" style="margin: -9px 0px 0 -17px; z-index: 100; font-size: 16px;"><i class="mdi mdi-close-circle"></i></a>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <div class="col-lg-12 col-sm-6 mb-1">
                                    <div class="thumbnail">
                                        <div class="thumb" style="overflow: hidden; height: 146px;">
                                            <img src="/img/catalog_default.jpg" alt="" style="width: 100% !important; height: 100% !important; object-fit: cover;" class="img-fluid img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="col-md-9">
                        @if (Auth::user()->hasPermission('standard-surplus.upload-files'))
                            <div class="mb-3">
                                <form action="{{ route('our-surplus.uploadPicture') }}" class="dropzone" id="upload-dropzone-catalog">
                                    @csrf
                                    <input type="hidden" name="ourSurplusId" value="{{ $ourSurplus->id }}" />
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
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
            $('#standardSurplusOrderByForm').submit();
        });

        $('#orderByDirection').on('change', function () {
            $('#standardSurplusOrderByForm').submit();
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
                    url:"{{ route('surplus.deleteItems') }}",
                    data:{items: ids},
                    success:function(data){
                        location.reload();
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
            var id = parent.attr("data-idSurplus");
            $.ajax({
                type:'POST',
                url:"{{ route('our-surplus.updatePrecies') }}",
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
                        if(data.cost != 0){
                            if(field === "costPriceM"){
                                parent.find("[name=salePriceM]").val(data.cost)
                            }
                            if(field === "costPriceF"){
                                parent.find("[name=salePriceF]").val(data.cost)
                            }
                            if(field === "costPriceU"){
                                parent.find("[name=salePriceU]").val(data.cost)
                            }
                            if(field === "costPriceP"){
                                parent.find("[name=salePriceP]").val(data.cost)
                            }
                        }
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                    }
                },
                complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        });

        $(".updatePreciesSale").on("change", function(){
            var sender = $(this);
            var field = sender.attr("name");
            var value = sender.val();
            var parent = sender.parent("td");
            var id = parent.attr("data-idSurplus");
            $.ajax({
                type:'POST',
                url:"{{ route('our-surplus.updatePreciesSale') }}",
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
        $(".updateOrigin").on("change", function(){
            var sender = $(this);
            var origin = sender.val();
            var id = sender.parent("td").attr("data-idSurplus");
            $.ajax({
                type:'POST',
                url:"{{ route('our-surplus.updateOrigin') }}",
                data:{
                    id: id,
                    origin: origin
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
        if ($('#upload-dropzone-catalog').length) {
            // Now that the DOM is fully loaded, create the dropzone, and setup the
            // event listeners
            var surplusDropzone = new Dropzone("#upload-dropzone-catalog", {
                maxFilesize: 10 // 10mb
                /*autoProcessQueue: false*/
            });
            surplusDropzone.on("complete", function(file) {
                surplusDropzone.removeFile(file);
                if(file.xhr.responseText){
                    var error = JSON.parse(file.xhr.responseText);
                    if(error.errors){
                        $.each( error.errors, function( key, value ) {
                            $.NotificationApp.send("Error message!", value, 'top-right', '#fff', 'error');
                        });
                    }else{
                        $.NotificationApp.send("Success message!", "File uploaded successfully", 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }

    });

</script>

@endsection
