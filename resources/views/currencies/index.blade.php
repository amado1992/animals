@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <a href="{{ route('currencies.rates') }}" class="btn btn-light">
        <i class="fas fa-fw fa-sync"></i> Reload rates
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-comments-dollar mr-2"></i> {{ __('Currencies') }}</h1>
  <p class="text-white">These are the currencies in the systems. <a href="http://exchangeratesapi.io/" target="_blank" class="text-dark">http://exchangeratesapi.io/</a></p>

@endsection


@section('main-content')

 <div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col-auto">
                        <i class="fas fa-euro-sign fa-2x text-gray-700"></i>
                    </div>
                    <div class="col ml-3">
                        <div class="h4 mb-0 font-weight-bold text-gray-800">EUR</div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ $rates->first() ? $rates->first()->EUR_USD . ' USD' : 'No rate' }}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-700"></i>
                    </div>
                    <div class="col ml-3">
                        <div class="h4 mb-0 font-weight-bold text-gray-800">USD</div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ $rates->first() ? $rates->first()->USD_EUR . ' EUR' : 'No rate' }}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col-auto">
                        <i class="fas fa-pound-sign fa-2x text-gray-700"></i>
                    </div>
                    <div class="col ml-3">
                        <div class="h4 mb-0 font-weight-bold text-gray-800">GBP</div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ $rates->first() ? $rates->first()->EUR_GBP . ' EUR' : 'No rate' }}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col-auto">
                        <i class="fab fa-canadian-maple-leaf fa-2x text-gray-700"></i>
                    </div>
                    <div class="col ml-3">
                        <div class="h4 mb-0 font-weight-bold text-gray-800">CAD</div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ $rates->first() ? $rates->first()->EUR_CAD . ' EUR' : 'No rate' }}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($rates->isEmpty())
        <table class="table table-striped" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Date</th>
              <th>EUR_USD</th>
              <th>EUR_GBP</th>
              <th>EUR_CAD</th>
              <th>USD_EUR</th>
              <th>USD_GBP</th>
              <th>USD_CAD</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $rates as $rate )
            <tr>
              <td>{{ $rate->date->format('Y-m-d') }}</td>
              <td>{{ $rate->EUR_USD }}</td>
              <td>{{ $rate->EUR_GBP }}</td>
              <td>{{ $rate->EUR_CAD }}</td>
              <td>{{ $rate->USD_EUR }}</td>
              <td>{{ $rate->USD_GBP }}</td>
              <td>{{ $rate->USD_CAD }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else

        <p> No rates are available </p>

      @endunless
    </div>
</div>


@endsection
