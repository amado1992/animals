@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('classifications.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add new classification
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-sitemap mr-2"></i> {{ __('Classifications') }}</h1>
  <p class="text-white">Classifications are the the relative levels of a group of animals</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        @unless($families->isEmpty())
            <div class="list-group mb-5 list-group-root well">
                @include('components.nestedList', ['list' => $families, 'depth' => 1])
            </div>
        @else
            <p> No classifications are added yet </p>
        @endunless
    </div>
</div>

@endsection

