@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create new surplus collection</h1>

          {!! Form::open(['route' => 'surplus-collection.store', 'id' => 'surplusCollectionForm']) !!}

              @include('surplus_collections._form', ['submitButtonText' => 'Create surplus'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

