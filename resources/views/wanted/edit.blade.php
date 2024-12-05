@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit wanted</h1>

          {!! Form::model($wanted, ['method' => 'PATCH', 'route' => ['wanted.update', $wanted->id], 'id' => 'wantedForm'] ) !!}

              @include('wanted._form', ['submitButtonText' => 'Save wanted'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

