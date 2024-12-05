@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create a new contact</h1>

          {!! Form::open(['route' => 'contacts.store', 'id' => 'contactForm']) !!}

              @include('contacts._form', ['submitButtonText' => 'Create', 'submitContactButtonText' => 'Create contact', 'preset' => compact('first_name', 'last_name','domain','city','country_id')])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

