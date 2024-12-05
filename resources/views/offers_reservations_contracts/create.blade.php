@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add standard document</h1>

          {!! Form::open(['route' => 'offers-reservations-contracts.store', 'files' => 'true']) !!}

              @include('offers_reservations_contracts._form', ['submitButtonText' => 'Add document'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

