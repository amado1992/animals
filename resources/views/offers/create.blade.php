@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create new offer</h1>

          {!! Form::open(['route' => 'offers.store', 'id' => 'offerForm']) !!}

              @include('offers._form', ['id' => 'newOfferForm', 'submitButtonText' => 'Create offer'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

