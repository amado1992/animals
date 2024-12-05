@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create new wanted</h1>

          {!! Form::open(['route' => 'wanted.store', 'id' => 'wantedForm']) !!}

              @include('wanted._form', ['submitButtonText' => 'Create wanted'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

