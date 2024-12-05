@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit zoo association</h1>

            {!! Form::model($zooAssociation, ['method' => 'PATCH', 'route' => ['zoo-associations.update', $zooAssociation->id] ] ) !!}

            @include('zoo_associations._form', ['submitButtonText' => 'Edit zoo association'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
