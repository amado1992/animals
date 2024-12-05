@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('our-wanted.index') }}">Our wanted</a></li>
    <li class="breadcrumb-item active">{{ $ourWanted->animal->common_name }}</li>
</ol>
@endsection

@section('header-content')

<div class="row mb-2">
    <div class="col-md-9">
        <div class="d-flex align-items-center">
            @if ($ourWanted->animal->catalog_pic != null && Storage::exists('public/animals_pictures/'.$ourWanted->animal->id.'/'.$ourWanted->animal->catalog_pic))
                <img src="{{ asset('storage/animals_pictures/'.$ourWanted->animal->id.'/'.$ourWanted->animal->catalog_pic) }}" class="float-left mr-4 rounded" style="max-width:70px;" alt="" />
            @else
                <img src="{{ asset('storage/animals_pictures/image_not_available.png') }}" class="float-left mr-4 rounded" style="max-width: 70px;" alt="" />
            @endif
            <div class="d-flex align-items-center">
                <h1 class="h1 text-white">{{ $ourWanted->animal->common_name }}</h1>
                <span class="text-white">&nbsp;-&nbsp;{{ $ourWanted->animal->scientific_name }}</span>
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
                <h4>Our wanted details</h4>
                <div class="d-flex">
                    @if (Auth::user()->hasPermission('standard-wanted.update'))
                        <a href="{{ route('our-wanted.edit', [$ourWanted->id]) }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-fw fa-edit"></i> Edit
                        </a>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            @if (Auth::user()->hasPermission('animals.read'))
                                <a class="dropdown-item" href="{{ route('animals.show', [$ourWanted->animal_id]) }}">View Animal</a>
                            @endif

                            @if (Auth::user()->hasPermission('standard-wanted.delete'))
                                {!! Form::open(['method' => 'DELETE', 'route' => ['our-wanted.destroy', $ourWanted->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                    <a href="#" class="dropdown-item text-danger" onclick="$(this).closest('form').submit();">Delete standard wanted</a>
                                {!! Form::close() !!}
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="border-top-0">Looking for</th>
                            <th class="border-top-0">Origin</th>
                            <th class="border-top-0">Age group</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $ourWanted->looking_field }}</td>
                            <td>{{ $ourWanted->origin_field }}</td>
                            <td>{{ $ourWanted->age_field }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <h4>{{ $animalRelatedWanted->total() }} related wanted records</h4>
            </div>
            <div class="card-body">
                @unless($animalRelatedWanted->isEmpty())
                <div class="table-responsive">
                    <table class="table table-hover clickable table-bordered">
                        <thead>
                            <tr>
                                <th class="border-top-0">Client</th>
                                <th class="border-top-0">Looking for</th>
                                <th class="border-top-0">Origin</th>
                                <th class="border-top-0">Age group</th>
                                <th class="border-top-0">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($animalRelatedWanted as $wanted)
                            <tr data-url="{{ route('wanted.show', [$wanted->id]) }}">
                                <td>
                                    <span class="card-title mb-0">{{ ($wanted->client != null) ? $wanted->client->full_name : '' }}</span><br>
                                    <span><em>({{ ($wanted->client != null) ? $wanted->client->email : '' }})</em></span>
                                </td>
                                <td>{{ $wanted->looking_field }}</td>
                                <td>{{ $wanted->origin_field }}</td>
                                <td>{{ $wanted->age_field }}</td>
                                <td>{{ $wanted->created_at->toDateString() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $animalRelatedWanted->links() }}
                @else
                    This animal doesn't occur in wanted of clients
                @endunless
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Extra information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td class="border-top-0"><b>Created at</b></td>
                        <td class="border-top-0">{{ ($ourWanted->created_at) ? $ourWanted->created_at->toDateTimeString() : '' }}</td>
                    </tr>
                    <tr>
                        <td><b>Areas</b></td>
                        <td>
                            @foreach($ourWanted->area_regions as $area_region)
                                {{ $area_region->short_cut }}<br>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td><b>Remarks</b></td>
                        <td>{{ $ourWanted->remarks ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><b>Internal remarks</b></td>
                        <td>{{ $ourWanted->intern_remarks ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
@endsection
