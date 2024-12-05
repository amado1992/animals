@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create a new contact</h1>

          {!! Form::open(['route' => 'organisations.storeContact', 'id' => 'addContactForm']) !!}

              @include('institutions.add_contact_form', ['submitButtonText' => 'Save contact'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
</div>

@endsection
