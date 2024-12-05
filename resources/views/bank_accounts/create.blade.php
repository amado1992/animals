@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create a new bank account</h1>

          {!! Form::open(['route' => 'bank_accounts.store']) !!}

              @include('bank_accounts._form', ['submitButtonText' => 'Create bank account'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

