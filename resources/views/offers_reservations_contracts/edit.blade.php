@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit standard document</h1>

            {!! Form::model($document, ['method' => 'PATCH', 'route' => ['offers-reservations-contracts.update', $document->id], 'files' => 'true' ] ) !!}

            @include('offers_reservations_contracts._form', ['submitButtonText' => 'Edit document'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
