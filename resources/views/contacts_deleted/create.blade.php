@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create a new contact</h1>

          {!! Form::open(['route' => 'contacts-deleted.store']) !!}

              @include('contacts_deleted._form', ['submitButtonText' => 'Create', 'submitContactButtonText' => 'Create contact'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

