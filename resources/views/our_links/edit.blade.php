@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit link</h1>

            {!! Form::model($ourLink, ['method' => 'PATCH', 'route' => ['our-links.update', $ourLink->id] ] ) !!}

            @include('our_links._form', ['submitButtonText' => 'Edit link'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
