@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('areas.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add area
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-globe-americas mr-2"></i> {{ __('Areas') }}</h1>
  <p class="text-white">Group regions together in areas</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($areas->isEmpty())
      <div class="table-responsive">
        <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Short name</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $areas as $area )
            <tr data-url="{{ route('areas.show', [$area->id]) }}">
              <td>{{ $area->name }}</td>
              <td>{{ $area->short_cut }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p> No areas are added yet </p>

      @endunless
    </div>
  </div>


@endsection

