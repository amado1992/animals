@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">
          
          <h1 class="mb-4">Edit country {{ $country->name }}</h1>

          {!! Form::model($country, ['method' => 'PATCH', 'route' => ['countries.update', $country->id] ] ) !!}
              
              @include('countries._form', ['submitButtonText' => 'Edit country'])

          {!! Form::close() !!}

        </div>
      </div>
      
    </div>
  </div>

    
@endsection

