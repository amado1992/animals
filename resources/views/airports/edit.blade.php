@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">
          
          <h1 class="mb-4">Edit airport {{ $airport->name }}</h1>

          {!! Form::model($airport, ['method' => 'PATCH', 'route' => ['airports.update', $airport->id] ] ) !!}
              
              @include('airports._form', ['submitButtonText' => 'Edit airport'])

          {!! Form::close() !!}

        </div>
      </div>
      
    </div>
  </div>

    
@endsection

