@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add a origin</h1>

          {!! Form::open(['route' => 'origins.store']) !!}

              @include('origins._form', ['submitButtonText' => 'Add origin'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

