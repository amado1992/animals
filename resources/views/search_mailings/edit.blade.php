@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit search mailing</h1>

            {!! Form::model($mailing, ['method' => 'PATCH', 'route' => ['search-mailings.update', $mailing->id], 'files' => 'true' ] ) !!}

            @include('search_mailings._form', ['submitButtonText' => 'Edit search mailing'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
