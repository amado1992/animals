@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create a new crate</h1>

          {!! Form::open(['route' => 'crates.store', 'id' => 'crateForm']) !!}

              @include('crates._form', ['cancelLink' => route('crates.index'), 'submitButtonText' => 'Create crate'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection
