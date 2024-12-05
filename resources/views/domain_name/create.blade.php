@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add Domain Name</h1>

          {!! Form::open(['route' => 'domain-name-link.store', 'files' => 'true']) !!}

              @include('domain_name._form', ['submitButtonText' => 'Add Domain Name'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

