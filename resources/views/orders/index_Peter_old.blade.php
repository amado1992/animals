@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="#" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Add new order
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-suitcase mr-2"></i> {{ __('Orders') }}</h1>
  <p class="text-white">All orders where an animal is sold</p>

@endsection


@section('main-content')


@foreach($orders as $order)
<div class="card shadow mb-2">
  <div class="card-body">

    <div class="row">

      <div class="col-md-3">

        <div class="float-right">

            <img src="{{ asset('img/animals/emperor-tamarin.jpg') }}" style="max-width: 60px;" class="rounded mr-4" />

        </div>

        <h4 class="card-title mb-0"><a href="{{ route('orders.show', [$order->id]) }}">{{ $order->order_number }}</a></h4>
        <small class="text-muted"><em>Emperor Tamarin</em></small>

      </div>
      <div class="col-md-2">


        <table class="table table-striped table-sm mb-0 text-center" style="font-size:0.8rem;">
          <tbody>
            <tr>
              <td><b>Male</b></td>
              <td><b>Female</b></td>
              <td><b>Unknown</b></td>
              <td><b>Pair</b></td>
            </tr>
            <tr>
              <td>1</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
            </tr>
          </tbody>

        </table>



      </div>

      <div class="col-md-4">

        <dl class="dl-horizontal">
            <dt>Provider</dt>
            <dd>African breeding company</dd>

            <dt>Client</dt>
            <dd>Copenhagen Zoo</dd>

            <dt>Projectmanager</dt>
            <dd>Annelies</dd>
        </dl>

      </div>

      <div class="col-md-2">
        <p>
          <b>Next action</b><br />
          Arrange shipping documentation
        </p>
      </div>
    </div>

  </div>
</div>

@endforeach





@endsection

