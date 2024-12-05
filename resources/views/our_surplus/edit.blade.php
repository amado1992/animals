@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-10 offset-md-1">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit standard surplus</h1>

          {!! Form::model($ourSurplus, ['method' => 'PATCH', 'route' => ['our-surplus.update', $ourSurplus], 'id' => 'ourSurplusForm'] ) !!}

              @include('our_surplus._form', ['submitButtonText' => 'Save'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

