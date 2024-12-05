@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">
          
          <h1 class="mb-4">Edit region {{ $region->name }}</h1>

          {!! Form::model($region, ['method' => 'PATCH', 'route' => ['regions.update', $region->id] ] ) !!}
              
              @include('regions._form', ['submitButtonText' => 'Edit region'])

          {!! Form::close() !!}

        </div>
      </div>
      
    </div>
  </div>

    
@endsection

