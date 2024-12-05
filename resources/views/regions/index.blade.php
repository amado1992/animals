@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('regions.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add region
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-globe-americas mr-2"></i> {{ __('Regions') }}</h1>
  <p class="text-white">Group countries together in regions</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($regions->isEmpty())
      <div class="table-responsive">
        <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Short name</th>
              <th>Area</th>
              <th>No. of countries</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $regions as $region )
            <tr data-url="{{ route('regions.show', [$region->id]) }}">
              <td>{{ $region->name }}</td>
              <td>{{ $region->short_cut }}</td>
              <td>{{ $region->area_region->name }}</td>
              <td>{{ $region->countries->count() }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p> No regions are added yet </p>

      @endunless
    </div>
  </div>


@endsection

