@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit directory "{{ $directory->title }}"</h1>

          {!! Form::model($directory, ['method' => 'PATCH', 'route' => ['directories.update', $directory->id] ] ) !!}

              @include('directories._form', ['submitButtonText' => 'Edit directory'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

