@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Add standard text</h1>

          {!! Form::open(['route' => 'std-texts.store']) !!}

              @include('std_texts._form', ['submitButtonText' => 'Add standard text'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

