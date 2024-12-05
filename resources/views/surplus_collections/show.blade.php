@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('surplus-collection.index') }}">Collections</a></li>
    <li class="breadcrumb-item active">{{ $surplus->animal->common_name }}</li>
</ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-9">
        <div class="d-flex align-items-center">
            @if ($surplus->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic))
                <img src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic) }}" class="float-left mr-4 rounded" style="max-width:70px;" alt="" />
            @else
                <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="float-left mr-4 rounded" style="max-width: 70px;" alt="" />
            @endif
            <div class="d-flex align-items-center">
                <h1 class="h1 text-white">{{$surplus->animal->common_name}}</h1>
                <span class="text-white">&nbsp;-&nbsp;{{ $surplus->animal->scientific_name }}</span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('main-content')

<div class="row">
    <div class="col-md-8">

        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <h4>Surplus details</h4>
                <div class="d-flex">
                    @if (Auth::user()->hasPermission('surplus-suppliers.update'))
                        <a href="{{ route('surplus-collection.edit', [$surplus->id]) }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-fw fa-edit"></i> Edit
                        </a>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            @if (Auth::user()->hasPermission('animals.read'))
                            <a class="dropdown-item" href="{{ route('animals.show', [$surplus->animal_id]) }}">View Animal</a>
                            @endif
                            @if (Auth::user()->hasPermission('surplus-suppliers.delete'))
                                {!! Form::open(['method' => 'DELETE', 'route' => ['surplus-collection.destroy', $surplus->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                    <a href="#" class="dropdown-item text-danger" onclick="$(this).closest('form').submit();">Delete surplus</a>
                                {!! Form::close() !!}
                            @endif
                            <a class="dropdown-item" href="{{ route('surplus-collection.askMoreSurplusDetails', $surplus->id) }}">Ask more details about surplus</a>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="border-top-0">Origin</th>
                            <th class="border-top-0">Age group</th>
                            <th class="border-top-0">Yr of birth</th>
                            <th class="border-top-0">Size</th>
                            <th class="border-top-0">Area</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $surplus->origin_field }}</td>
                            <td>{{ $surplus->age_field ?: '-' }}</td>
                            <td>{{ $surplus->bornYear ?: '-' }}</td>
                            <td>{{ $surplus->size ?: '-' }}</td>
                            <td>{{ ($surplus->area_region != null) ? $surplus->area_region->name : '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <h4>{{ $animalRelatedSurplus->total() }} related surpluses collection</h4>
            </div>
            <div class="card-body">
                @unless($animalRelatedSurplus->isEmpty())
                <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                    <table class="table table-hover clickable table-bordered">
                        <thead>
                            <tr>
                                <th class="align-top">Origin</th>
                                <th class="align-top">Age group</th>
                                <th class="align-top">Yr of birth</th>
                                <th class="align-top">Size</th>
                                <th class="align-top">Continent</th>
                                <th class="align-top">Location</th>
                                <th class="align-top">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($animalRelatedSurplus as $s)
                            @if($s->id !== $surplus->id)
                            <tr data-url="{{ route('surplus.show', [$s->id]) }}">
                                <td>{{ $s->origin_field }}</td>
                                <td>{{ $s->age_field }}</td>
                                <td>{{ $s->bornYear }}</td>
                                <td>{{ $s->size }}</td>
                                <td>{{ $s->location }}</td>
                                <td>@if($s->contact && $s->contact->organisation) {{ Str::limit($s->contact->organisation->city, 15) }}, {{ $s->contact->organisation->country->country_code }} @else - @endif</td>
                                <td>{{ $s->created_at->toDateString() }}</td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $animalRelatedSurplus->links() }}
                @else
                    This animal doesn't occur in more surplus of surpliers
                @endunless
            </div>
        </div>

    </div>

    <div class="col-md-4">

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Surplier</h5>
                <div class="d-flex">
                    @if($surplus->organisation)
                        <a href="{{ route('organisations.show', [$surplus->organisation_id]) }}" class="btn btn-sm btn-dark">
                            <i class="fas fa-fw fa-search"></i> Show
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    @if($surplus->organisation)
                        <tr>
                            <td class="border-top-0"><b>Institution</b></td>
                            <td class="border-top-0">{{ $surplus->organisation->name }}</td>
                        </tr>
                        <tr>
                            <td><b>Address</b></td>
                            <td>{{ $surplus->organisation->address }}, {{ $surplus->organisation->city }}</td>
                        </tr>
                        <tr>
                            <td><b>Country</b></td>
                            <td>{{ $surplus->organisation->country->name }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="2" class="border-top-0 text-danger"><b>No institution defined.</b></td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Extra information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td class="border-top-0"><b>Created at</b></td>
                        <td class="border-top-0">{{ ($surplus->created_at) ? $surplus->created_at->toDateTimeString() : '' }}</td>
                    </tr>
                    <tr>
                        <td><b>Remarks</b></td>
                        <td>{{ $surplus->remarks ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><b>Internal remarks</b></td>
                        <td>{{ $surplus->intern_remarks ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><b>Special conditions</b></td>
                        <td>{{ $surplus->special_conditions ?: '-' }}</td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Images</h5>
                @if (Auth::user()->hasPermission('surplus-suppliers.upload-files'))
                    <div class="d-inline-block">
                        <a href="#" class="btn btn-dark btn-sm" title="Upload file" id="uploadFile" data-toggle="modal" data-id="{{ $surplus->id }}">
                            <i class="fas fa-upload" title="Upload file"></i>
                            Upload
                        </a>
                    </div>
                @endif
            </div>
            <div class="card-body">
                @if($surplus->docs)
                    @foreach($surplus->docs as $doc)
                        @php
                            $file = pathinfo($doc);
                        @endphp
                        <a href="{{Storage::url('surpluses_docs/'.$surplus->id.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                        @if (Auth::user()->hasPermission('surplus-suppliers.delete-files'))
                            <a href="{{ route('surplus-collection.delete_file', [$surplus->id, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')" style="color: red;">(x)</a>,
                        @endif
                    @endforeach
                @else
                    No images found for this surplus
                @endif
            </div>
        </div>
    </div>
</div>

@include('uploads.upload_modal', ['modalId' => 'uploadSurplusFile', 'route' => 'surplus.upload'])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /* Upload crate file */
        $('body').on('click', '#uploadFile', function () {
            var surplusId = $(this).data('id');

            $('#uploadSurplusFile').modal('show');
            $('#uploadSurplusFile [name=id]').val(surplusId);
        });

    });

</script>

@endsection
