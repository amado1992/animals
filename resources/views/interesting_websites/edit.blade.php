@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit site</h1>

            {!! Form::model($interestingWebsite, ['method' => 'PATCH', 'route' => ['interesting-websites.update', $interestingWebsite->id] ] ) !!}

            @include('interesting_websites._form', ['submitButtonText' => 'Edit site'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
