@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit area {{ $area->name }}</h1>

          {!! Form::model($area, ['method' => 'PATCH', 'route' => ['areas.update', $area->id] ] ) !!}

              @include('areas._form', ['submitButtonText' => 'Edit area'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

