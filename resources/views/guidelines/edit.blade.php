@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit guideline</h1>

            {!! Form::model($guideline, ['method' => 'PATCH', 'route' => ['guidelines.update', $guideline->id], 'files' => 'true' ] ) !!}

            @include('guidelines._form', ['submitButtonText' => 'Edit guideline'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
