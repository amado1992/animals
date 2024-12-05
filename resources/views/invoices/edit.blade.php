@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit invoice</h1>

          {!! Form::model($invoice, ['method' => 'PATCH', 'route' => ['invoices.update', $invoice->id] ] ) !!}

              @include('invoices._form', ['submitButtonText' => 'Edit invoice'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

