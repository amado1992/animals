@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create new standard wanted</h1>

          {!! Form::open(['route' => 'our-wanted.store', 'id' => 'c']) !!}

              @include('our_wanted._form', ['submitButtonText' => 'Create'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

