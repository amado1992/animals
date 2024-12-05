@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit standard wanted</h1>

          {!! Form::model($ourWanted, ['method' => 'PATCH', 'route' => ['our-wanted.update', $ourWanted->id], 'id' => 'ourWantedForm'] ) !!}

              @include('our_wanted._form', ['submitButtonText' => 'Save'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

