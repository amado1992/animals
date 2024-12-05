@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit website text</h1>

            {!! Form::model($websiteText, ['method' => 'PATCH', 'route' => ['website-texts.update', $websiteText->id] ] ) !!}

            @include('website_texts_pictures._form', ['submitButtonText' => 'Edit website text'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
