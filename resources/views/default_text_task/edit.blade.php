@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit Default Text</h1>

            {!! Form::model($default_text_task, ['method' => 'PATCH', 'route' => ['default-text-task.update', $default_text_task->id], 'files' => 'true' ] ) !!}

            @include('default_text_task._form', ['submitButtonText' => 'Edit Default Text'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
