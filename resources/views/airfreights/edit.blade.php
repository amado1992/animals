@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit airfreight</h1>

          {!! Form::model($airfreight, ['method' => 'PATCH', 'route' => ['airfreights.update', $airfreight->id], 'id' => 'airfreightForm'] ) !!}

              @include('airfreights._form', ['submitButtonText' => 'Edit crate'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

