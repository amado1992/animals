@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-8 offset-md-2">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Create new order</h1>

          {!! Form::open(['route' => 'orders.store', 'id' => 'orderForm']) !!}

              @include('orders._form', ['id' => 'newOrderForm', 'submitButtonText' => 'Create order'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

