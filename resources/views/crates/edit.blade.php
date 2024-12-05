@extends('layouts.admin')

@section('main-content')

<div class="row">
  <div class="col-md-6 offset-md-3">
    <div class="card shadow mb-4">
        <div class="card-body">

          <h1 class="mb-4">Edit crate</h1>

          {!! Form::model($crate, ['method' => 'PATCH', 'route' => ['crates.update', $crate->id], 'id' => 'crateForm'] ) !!}

              @include('crates._form', ['cancelLink' => route('crates.show', [$crate->id]), 'submitButtonText' => 'Edit crate'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection
