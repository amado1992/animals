@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-10 offset-md-1">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create new standard surplus</h1>

          {!! Form::open(['route' => 'our-surplus.store', 'id' => 'ourSurplusForm']) !!}

              @include('our_surplus._form', ['submitButtonText' => 'Create'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

