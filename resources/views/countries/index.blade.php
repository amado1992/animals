@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('countries.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add country
      </a>
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterCountries">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('countries.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-flag mr-2"></i> {{ __('Countries') }}</h1>
  <p class="text-white">List of all countries to geographically organise activities</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('countries.removeFromCountrySession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($countries->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered datatable" width="100%" cellspacing="0">
            <thead>
                <tr>
                <th>Name</th>
                <th>Region</th>
                <th>Country code</th>
                <th>Phone code</th>
                <th>Language</th>
                <th>#Institutions</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $countries as $country )
                <tr data-url="{{ route('countries.show', [$country->id]) }}">
                <td>{{ $country->name }}</td>
                <td>{{ $country->region->name }}</td>
                <td>{{ $country->country_code }}</td>
                <td>{{ $country->phone_code }}</td>
                <td>{{ $country->language }}</td>
                <td>{{ $country->organisations->count() }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
      @else
        <p> No countries are added yet </p>
      @endunless
    </div>
  </div>

@include('countries.filter_modal', ['modalId' => 'filterCountries'])

@endsection

