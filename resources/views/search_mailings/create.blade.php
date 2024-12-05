@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add search mailing</h1>

          {!! Form::open(['route' => 'search-mailings.store', 'files' => 'true']) !!}

              @include('search_mailings._form', ['submitButtonText' => 'Add search mailing'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

