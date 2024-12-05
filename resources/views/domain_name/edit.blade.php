@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit Domain Name</h1>

            {!! Form::model($domain_name, ['method' => 'PATCH', 'route' => ['domain-name-link.update', $domain_name->id], 'files' => 'true' ] ) !!}

            @include('domain_name._form', ['submitButtonText' => 'Edit Domain Name'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
