@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('animals.index') }}">Animals</a></li>
    <li class="breadcrumb-item active">{{ $animal->common_name }}</li>
</ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-9">
        <div class="d-flex align-items-center">
            @if ($animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$animal->id.'/'.$animal->catalog_pic))
                <img src="{{ asset('storage/animals_pictures/'.$animal->id.'/'.$animal->catalog_pic) }}" class="float-left mr-4 rounded" style="max-width:70px;" alt="" />
            @else
                @if(!empty($animal->imagen_first))
                    <img src="{{ asset('storage/animals_pictures/'.$animal->id.'/'.$animal->imagen_first["name"]) }}" class="rounded" style="max-width:70px;" alt="" />
                @else
                    <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="rounded" style="max-width: 70px;" alt="" />
                @endif
            @endif
            <h1 class="h1 text-white">{{$animal->common_name}}</h1>
        </div>
    </div>
</div>

@endsection

@section('main-content')

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <h4>Animal details</h4>
                <div class="d-flex">
                    @if (Auth::user()->hasPermission('animals.update'))
                        <a href="{{ route('animals.edit', [$animal->id]) }}" class="btn btn-dark">
                            <i class="fas fa-fw fa-edit"></i> Edit
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table class="table">
                            <tr>
                                <td class="border-top-0"><b>Scientific name</b></td>
                                <td class="border-top-0">{{$animal->scientific_name}}</td>
                            </tr>
                            <tr>
                                <td><b>Spanish name</b></td>
                                <td>{{$animal->spanish_name}}</td>
                            </tr>
                            <tr>
                                <td><b>Chinese name</b></td>
                                <td>{{$animal->chinese_name ?? ""}}</td>
                            </tr>
                            <tr>
                                <td><b>Taxonomy</b></td>
                                @if ($animal->classification != null)
                                    <td>
                                        Class: {{ $animal->classification->class->common_name }}<br />
                                        Order: {{ $animal->classification->order->common_name }}<br />
                                        Family: {{ $animal->classification->family->common_name }}<br />
                                        Genus: {{ $animal->classification->common_name }}
                                    </td>
                                @else
                                    <td>
                                        Class:<br />
                                        Order:<br />
                                        Family:<br />
                                        Genus:
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </div>
                    <div class="col">
                        <table class="table">
                            <tr>
                                <td class="border-top-0"><b>CITES</b></td>
                                <td class="border-top-0">
                                    @if($animal->cites_global) Global: {{$animal->cites_global->key}} @endif
                                    @if($animal->cites_europe) Europe: {{$animal->cites_europe->key}} @endif
                                </td>
                            </tr>
                            <tr>
                                <td><b>IATA code</b></td>
                                <td>{{$animal->iata_code}}</td>
                            </tr>
                            <tr>
                                <td><b>Average body weight</b></td>
                                <td>{{ $animal->body_weight != 0 ? $animal->body_weight . ' kg' : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <h4>In our surplus</h4>
                <div class="d-inline-block">
                    @if (Auth::user()->hasPermission('standard-surplus.read'))
                        <a href="{{ route('our-surplus.filterOurSurplus', ['filter_animal_id' => $animal->id]) }}" class="btn btn-dark">
                            <i class="fas fa-fw fa-search"></i> Show
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @unless($animal->our_surpluses->isEmpty())
                <div class="table-responsive">
                    <table class="table table-hover clickable table-bordered">
                        <thead>
                            <tr>
                                <th colspan="3" class="align-top text-center">Quantity</th>
                                <th rowspan="2" class="align-top">Origin</th>
                                <th rowspan="2" class="align-top">Age group</th>
                                <th rowspan="2" class="align-top">Continent</th>
                                <th colspan="5" class="align-top text-center">Sales price</th>
                            </tr>
                            <tr class="table-secondary">
                                <th class="text-center">M</th>
                                <th class="text-center">F</th>
                                <th class="text-center">U</th>
                                <th></th>
                                <th class="text-center">M</th>
                                <th class="text-center">F</th>
                                <th class="text-center">U</th>
                                <th class="text-center">P</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($animal->our_surpluses as $surplus)
                            <tr data-url="{{ route('our-surplus.edit', [$surplus->id]) }}">
                                <td class="text-center">{{ $surplus->male_quantity }}</td>
                                <td class="text-center">{{ $surplus->female_quantity }}</td>
                                <td class="text-center">{{ $surplus->unknown_quantity }}</td>
                                <td>{{ $surplus->origin_field }}</td>
                                <td>{{ $surplus->age_field }}</td>
                                <td>{{ ($surplus->region) ? $surplus->region->name : '' }}</td>
                                <td>{{ $surplus->sale_currency }}</td>
                                <td class="text-center">{{ number_format($surplus->salePriceM, 2, '.', '') }}</td>
                                <td class="text-center">{{ number_format($surplus->salePriceF, 2, '.', '') }}</td>
                                <td class="text-center">{{ number_format($surplus->salePriceU, 2, '.', '') }}</td>
                                <td class="text-center">{{ number_format($surplus->salePriceP, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    Animal has no record in our surplus
                @endunless
            </div>
        </div>

    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Animal images</h5>
                @if (empty($animal->catalog_pic) && empty($animal->imagen_first))
                    <div class="invalid-feedback-tooltips" style="margin: -12px 0 -11px 0 !important;">
                        <span id="invalid-canonical_name" role="alert">Remember that it is necessary to insert the <br> <strong>Picture Catalog</strong> of the species.
                        <div class="invalid-arrow">
                        </div>
                    </div>
                @endif
                <div class="d-inline-flex">
                    @if (Auth::user()->hasPermission('animals.upload-files'))
                        <a href="#" id="updateMain" class="btn btn-sm btn-dark mr-2"><i class="fas fa-fw fa-image"></i> Main Picture</a>
                    @endif
                    @if (Auth::user()->hasPermission('animals.upload-files'))
                        <a href="#" id="uploadPicture" data-toggle="modal" data-id="{{$animal->id}}" class="btn btn-sm btn-dark"><i class="fas fa-fw fa-plus"></i> Upload</a>
                    @endif
                    @if (Auth::user()->hasPermission('animals.delete-files') && count($files_processed) > 0)
                        <a href="#" id="deleteSelectedPictures" class="btn btn-sm btn-dark ml-2"><i class="fas fa-fw fa-window-close"></i> Delete</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @unless(count($files_processed) == 0)
                <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 20px;"><input type="checkbox" id="selectAllPictures" name="selectAllPictures" /></th>
                            <th></th>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Dimensions</th>
                            <th>Date uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files_processed as $file)
                            @if ($file['name'] == $animal->catalog_pic)
                                <tr>
                                    <td>
                                        @if ($file['name'])
                                            <input type="checkbox" class="selector-picture" value="{{ $file['name'] }}" />
                                        @endif
                                    </td>
                                    <td>
                                        <img src="{{ asset('storage/animals_pictures/'.$animal->id.'/'.$file['name']) }}" class="rounded" style="max-width:30px;" alt="" />
                                    </td>
                                    <td>
                                        <a href="{{Storage::url('animals_pictures/'.$animal->id."/".$file['name'])}}" target="_blank" class="@if ($file['name'] == $animal->catalog_pic) text-danger @endif">
                                            {{$file['name']}}
                                        </a>
                                    </td>
                                    <td>
                                        {{ FileSizeHelper::bytesToHuman(Storage::size('public/animals_pictures/'.$animal->id."/".$file['name'])) }}
                                    </td>
                                    <td>
                                        {{$file['dimension']}}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified('public/animals_pictures/'.$animal->id."/".$file['name']))->toDateTimeString() }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach($files_processed as $key => $file)
                            @if ($file['name'] != $animal->catalog_pic)
                                <tr>
                                    <td>
                                        @if ($file['name'])
                                            <input type="checkbox" class="selector-picture" value="{{ $file['name'] }}" />
                                        @endif
                                    </td>
                                    <td>
                                        <img src="{{ asset('storage/animals_pictures/'.$animal->id.'/'.$file['name']) }}" class="rounded" style="max-width:30px;" alt="" />
                                    </td>
                                    <td>
                                        <a href="{{Storage::url('animals_pictures/'.$animal->id."/".$file['name'])}}" target="_blank" class="@if (empty($animal->catalog_pic) && $key == 0) text-danger @endif">
                                            {{$file['name']}}
                                        </a>
                                    </td>
                                    <td>
                                        {{ FileSizeHelper::bytesToHuman(Storage::size('public/animals_pictures/'.$animal->id."/".$file['name'])) }}
                                    </td>
                                    <td>
                                        {{$file['dimension']}}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified('public/animals_pictures/'.$animal->id."/".$file['name']))->toDateTimeString() }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    </table>
                </div>
                @else
                    <p> No pictures found. </p>
                @endunless
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Crates for this animal</h5>
                @if (Auth::user()->hasPermission('animals.update') && !Auth::user()->hasRole('office'))
                    <div class="d-inline-flex">
                        <a href="#" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#cratesForSpecies">
                            <i class="fas fa-fw fa-plus"></i> Add
                        </a>
                        @unless($animal->crates->isEmpty())
                            <a href="#" id="deleteSelectedCrates" class="btn btn-sm btn-dark ml-2">
                                <i class="fas fa-fw fa-window-close"></i> Remove
                            </a>
                        @endunless
                    </div>
                @endif
            </div>
            <div class="card-body">
                @unless($animal->crates->isEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 20px;"><input type="checkbox" id="selectAllCrates" name="selectAllCrates" /></th>
                            <th>Name</th>
                            <th>Dimensions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($animal->crates as $crate)
                        <tr>
                            <td><input type="checkbox" class="selector-crate" value="{{ $crate->id }}" /></td>
                            <td>
                                {{ $crate->name }}
                            </td>
                            <td>
                                {{ $crate->full_dimensions }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                @else

                    <p> No crates assigned. </p>

                @endunless
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Zootierlist</h5>
            </div>
            <div class="card-body">
				{!! Form::hidden('animal_id', $animal->id, ['class' => 'form-control', 'id' => 'animal_id']) !!}
				<div class="mb-3">
					{!! Form::label('ztl_class', 'Class') !!}
					<div class="input-group">
						{!! Form::text('ztl_class', $animal->ztl_class, ['class' => 'form-control ztl_inputclass']) !!}
						<div style="display: flex; align-items: center; padding: 0.375rem 0.75rem;">
						   <i id="ztl_class_icon" class="fa fa-lg"></i>
						</div>
					</div>
				</div>
				<div class="mb-3">
					{!! Form::label('ztl_order', 'Order') !!}
					<div class="input-group">
						{!! Form::text('ztl_order', $animal->ztl_order, ['class' => 'form-control ztl_inputclass']) !!}
						<div style="display: flex; align-items: center; padding: 0.375rem 0.75rem;">
						   <i id="ztl_order_icon" class="fa fa-lg"></i>
						</div>
					</div>
				</div>
				<div class="mb-3">
					{!! Form::label('ztl_family', 'Family') !!}
					<div class="input-group">
						{!! Form::text('ztl_family', $animal->ztl_family, ['class' => 'form-control ztl_inputclass']) !!}
						<div style="display: flex; align-items: center; padding: 0.375rem 0.75rem;">
						   <i id="ztl_family_icon" class="fa fa-lg"></i>
						</div>
					</div>
				</div>
				<div class="mb-3">
					{!! Form::label('ztl_article', 'Article') !!}
					<div class="input-group">
						{!! Form::text('ztl_article', $animal->ztl_article, ['class' => 'form-control ztl_inputclass']) !!}
						<div style="display: flex; align-items: center; padding: 0.375rem 0.75rem;">
						   <i id="ztl_article_icon" class="fa fa-lg"></i>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>

@include('animals.upload_pictures_modal', ['modalId' => 'uploadAnimalPicture'])

@include('animals.assign_crates_modal', ['modalId' => 'cratesForSpecies', 'animalId' => $animal->id])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        $(':checkbox.selector-picture').prop('checked', false);
        $(':checkbox.selector-crate').prop('checked', false);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	window.addEventListener('keyup', function(e) {
	 if (e.target.classList.contains('ztl_inputclass')) {
        const value = e.target.value;
		var icon_name = e.target.id + '_icon';

		if (isOnlyNumbers(value))
		{
			document.getElementById(icon_name).classList.remove('fa-times-circle');
			document.getElementById(icon_name).classList.add('fa-check');
			document.getElementById(icon_name).style.color = 'green';
			updateZootierListeAjax(e.target.id, value, icon_name);
		}
		else
		{
			document.getElementById(icon_name).classList.remove('fa-check');
			document.getElementById(icon_name).classList.add('fa-times-circle');
			document.getElementById(icon_name).style.color = 'red';
		}
    }
	});

	function isOnlyNumbers(val) {
		return /^[0-9]+$/.test(val)
	}

	function updateZootierListeAjax(column, value, icon_name)
	{
		var idAnimal = $('#animal_id').val();
		$.ajax({
			type:'POST',
			url:"{{ route('animals.updateZootierListe') }}",
			data:{
				idAnimal: idAnimal,
				column: column,
				value: value
			},
			success:function(data){
				if (data.success)
				{
					document.getElementById(icon_name).classList.remove('fa-check');
					document.getElementById(icon_name).classList.add('fa-save');
					document.getElementById(icon_name).style.color = 'blue';
				}
            },
			error: function (request, error) {
				document.getElementById(icon_name).classList.remove('fa-save');
				document.getElementById(icon_name).classList.add('fa-times-circle');
				document.getElementById(icon_name).style.color = 'red';
				alert("Something went wrong trying to save the value.");
			}
		});
	}

    $('#selectAllPictures').on('change', function () {
        $(":checkbox.selector-picture").prop('checked', this.checked);
    });

    $('#selectAllCrates').on('change', function () {
        $(":checkbox.selector-crate").prop('checked', this.checked);
    });

    /* Upload picture */
    $('body').on('click', '#uploadPicture', function () {
        var animalId = $(this).data('id');

        $('#uploadAnimalPicture').modal('show');
        $('#uploadAnimalPicture [name=id]').val(animalId);
    });

    $('#deleteSelectedPictures').on('click', function () {
        var animalId = $('#uploadPicture').data('id');

        var file_names = [];
        $(":checked.selector-picture").each(function(){
            file_names.push($(this).val());
        });

        if(file_names.length == 0)
            alert("You must select pictures to delete.");
        else if(confirm("Are you sure that you want to delete the selected pictures?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('animals.deletePictures') }}",
                data:{
                    id: animalId,
                    items: file_names
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#updateMain').on('click', function () {
        var animalId = $('#uploadPicture').data('id');

        var file_names = [];
        $(":checked.selector-picture").each(function(){
            file_names.push($(this).val());
        });

        if(file_names.length != 1)
            alert("Only one image can be selected as the main image.");
        else if(confirm("Are you sure that you want to update main the selected picture?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('animals.updateMainImage') }}",
                data:{
                    id: animalId,
                    items: file_names
                },
                success:function(data){
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                    }else{
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                        location.reload();
                    }
                }
            });
        }
    });

    $('#uploadAnimalPicture').on('submit', function (event) {
        event.preventDefault();

        var form = document.forms.namedItem("uploadAnimalPicture");
        var formdata = new FormData(form);

        $.ajax({
            type:'POST',
            url:"{{ route('animals.uploadPicture') }}",
            contentType: false,
            data: formdata,
            processData: false,
            success: function(data) {
                if(data.success) {
                    form.reset();
                    location.reload();
                }
                /*else {
                    $(".alert-danger").find("ul").html('');
                    $.each(data.errors, function(key, value) {
                        $(".alert-danger").show();
                        $(".alert-danger").find("ul").append('<li>'+value+'</li>');
                    });
                }*/
            },
            error: function(xhr, status, errorThrown) {
                var errors = xhr.responseJSON.errors;
                $(".alert-danger").find("ul").html('');
                $.each(errors, function(key, value) {
                    $(".alert-danger").show();
                    $(".alert-danger").find("ul").append('<li>'+value+'</li>');
                });
            }
        });
    });

    $('#cratesForSpecies').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#cratesForSpecies [name=iata_code]').on('change', function () {
        $.ajax({
            type:'POST',
            url:"{{ route('crates.getCratesByIata') }}",
            data:{
                iata: $('#cratesForSpecies [name=iata_code]').val()
            },
            success:function(data){
                $("#cratesForSpecies #crates_list").empty();
                $.each(data.crates, function(key, value) {
                    $('#cratesForSpecies #crates_list').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#deleteSelectedCrates').on('click', function () {
        var animalId = $('#uploadPicture').data('id');

        var selected_crates = [];
        $(":checked.selector-crate").each(function(){
            selected_crates.push($(this).val());
        });

        if(selected_crates.length == 0)
            alert("You must select crates to remove.");
        else if(confirm("Are you sure that you want to delete the selected crates for this species?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('animals.removeSpeciesCrates') }}",
                data:{
                    id: animalId,
                    items: selected_crates
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection
