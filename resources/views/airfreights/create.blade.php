@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create a new airfreight</h1>

          {!! Form::open(['route' => 'airfreights.store', 'id' => 'airfreightForm']) !!}

              @include('airfreights._form', ['submitButtonText' => 'Create airfreight'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

