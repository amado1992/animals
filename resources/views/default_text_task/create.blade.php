@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add Default Text</h1>

          {!! Form::open(['route' => 'default-text-task.store', 'files' => 'true']) !!}

              @include('default_text_task._form', ['submitButtonText' => 'Add Default Text'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

