@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('airports.create') }}" class="btn btn-light">
          <i class="fas fa-fw fa-plus"></i> Add airport
      </a>
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterAirports">
          <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('airports.showAll') }}" class="btn btn-light">
          <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-luggage-cart mr-2"></i> {{ __('Airports') }}</h1>
  <p class="text-white">List of all airports in the world</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex flex-row align-items-center mb-3">
            <span class="mr-1">Filtered on:</span>
            @foreach ($filterData as $key => $value)
                <a href="{{ route('airports.removeFromAirportSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>

      @unless($airports->isEmpty())
        <div class="table-responsive">
            <table class="table clickable table-hover table-bordered datatable" width="100%" cellspacing="0">
            <thead>
                <tr>
                <th>Name</th>
                <th>City</th>
                <th>Country</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $airports as $airport )
                <tr data-url="{{ route('airports.show', [$airport->id]) }}">
                <td>{{ $airport->name }}</td>
                <td>{{ $airport->city }}</td>
                <td>{{ $airport->country->name }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
      @else
        <p> No airports are added yet </p>
      @endunless
    </div>
  </div>

  @include('airports.filter_modal', ['modalId' => 'filterAirports'])

@endsection

