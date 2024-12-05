@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add an area/zone</h1>

          {!! Form::open(['route' => 'areas.store']) !!}

              @include('areas._form', ['submitButtonText' => 'Add area'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

