@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit standard text</h1>

            {!! Form::model($stdText, ['method' => 'PATCH', 'route' => ['std-texts.update', $stdText->id] ] ) !!}

            @include('std_texts._form', ['submitButtonText' => 'Edit standard text'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
