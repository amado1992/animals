@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add an airport</h1>

          {!! Form::open(['route' => 'airports.store']) !!}
              
              @include('airports._form', ['submitButtonText' => 'Add airport'])

          {!! Form::close() !!}

        </div>
      </div>
      
    </div>
  </div>

    
@endsection

