@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit dashboard</h1>

          {!! Form::model($dashboard, ['method' => 'PATCH', 'route' => ['dashboards.update', $dashboard->id] ] ) !!}

              @include('dashboards._form', ['submitButtonText' => 'Edit dashboard'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

