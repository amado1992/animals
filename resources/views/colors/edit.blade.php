@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit color "{{ $color->title }}"</h1>

          {!! Form::model($color, ['method' => 'PATCH', 'route' => ['colors.update', $color->id] ] ) !!}

              @include('colors._form', ['submitButtonText' => 'Edit color'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

