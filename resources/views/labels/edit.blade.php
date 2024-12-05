@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit label "{{ $label->title }}"</h1>

          {!! Form::model($label, ['method' => 'PATCH', 'route' => ['labels.update', $label->id] ] ) !!}

              @include('labels._form', ['submitButtonText' => 'Edit label'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

