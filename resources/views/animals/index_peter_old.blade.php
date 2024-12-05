@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
    <a href="{{ route('classifications.index') }}" class="btn btn-dark">
        Manage classifications
      </a>

      <a href="{{ route('animals.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add new animal
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-paw mr-2"></i> {{ __('Animals') }}</h1>
  <p class="text-white">Manage all animals which will be traded by Zoo Services</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
      @unless($animals->isEmpty())

      <div class="table-responsive">
        <table class="table table-bordered datatable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Common name</th>
              <th>Scientific name</th>
              <th>Classification</th>
              <th>Code</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $animals as $animal )
            <tr>
              <td><a href="{{ route('animals.show', [$animal->id]) }}">{{ $animal->common_name }}</a></td>
              <td>{{ $animal->scientific_name }}</td>
              <td>{{ $animal->classification->common_name }}</td>
              <td>{{ $animal->code_number }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

      <p> No animals are added yet </p>

      @endunless
    </div>
  </div>


@endsection

