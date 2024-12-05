@extends('layouts.admin')

@section('header-content')

<div class="row">
  <div class="col-md-9">

      <div>
        <img src="{{ asset('img/animals/emperor-tamarin.jpg') }}" class="float-left mr-4 rounded" style="max-width:180px;" alt="">
        <h1 class="h1 text-white">{{$animal->common_name}}</h1>
        <p class="text-white">{{$animal->scientific_name}}</p>

        <p class="text-white">
          > {{$animal->classification->common_name}} > {{$animal->classification->family->common_name}} > {{$animal->classification->order->common_name}} > {{$animal->classification->class->common_name}}
        </p>
      </div>
    
  </div>
  
  <div class="col-md-3">
    <table class="table text-white">
      <tr>
        <td>CITES </td>
        <td>
          @if($animal->cites_global)Global: {{$animal->cites_global->key}} @endif
          @if($animal->cites_europe) Europe: {{$animal->cites_europe->key}} @endif
        </td>
      </tr>
      <tr>
        <td>Crates</td>
        <td>1, 31</td>
      </tr>

      <tr>
        <td>Standard weight</td>
        <td>500 KG</td>
      </tr>
    </table>

  </div>
</div>

@endsection

@section('main-content')

<div class="row">
  <div class="col-md-8">

    <div class="card shadow mb-4">
        <div class="card-header">
            <a href="#" class="float-right btn btn-primary"><i class="fas fa-fw fa-plus"></i> Add new variety</a>
            <h3>Our surplus variaties</h3>
        </div>
        <div class="card-body">

            <table class="table table-striped" width="100%" cellspacing="0">
              <thead>
                <tr class="table-active">
                  <th>Origin</th>
                  <th>Continent</th>
                  <th class="text-center">M</th>
                  <th class="text-center">F</th>
                  <th class="text-center">U</th>
                  <th class="text-center">Pr</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Any</td>
                  <td>USA</td>
                  <td class="text-center">$ 75.000</td>
                  <td class="text-center">$ 75.000</td>
                  <td class="text-center">$ 75.000</td>
                  <td class="text-center">$ 75.000</td>
                </tr>
                <tr>
                  <td>Any</td>
                  <td>Africa</td>
                  <td class="text-center">$ 75.000</td>
                  <td class="text-center">$ 75.000</td>
                  <td class="text-center">$ 75.000</td>
                  <td class="text-center">$ 75.000</td>
                </tr>
                <tr>
                  <td class="align-middle">Captive Bred</td>
                  <td class="align-middle">Europe</td>
                  <td class="text-center" style="line-height: 1.8rem">
                    Cost. $ 40.000 <br />  
                    Sell. $ 44.200 <br />
                    <span class="badge badge-primary">4%</span>
                  </td>
                  <td class="text-center" style="line-height: 1.8rem">
                    Cost. $ 85.000 <br /> 
                    Sell. $ 86.500 <br /> 
                    <span class="badge badge-primary">3.2%</span>
                  </td>
                  <td class="text-center">-</td>
                  <td class="text-center" style="line-height: 1.8rem">
                    Cost. $ 95.000 <br /> 
                    Sell. $ 102.000 <br /> 
                    <span class="badge badge-primary">3%</span>
                  </td>
                </tr>
                <tr>
                  <td>Wild Caught</td>
                  <td>Europe</td>
                  <td class="text-center">$ 35.000</td>
                  <td class="text-center">$ 75.000</td>
                  <td class="text-center">-</td>
                  <td class="text-center">$ 90.000</td>
                </tr>
                
              </tbody>
            </table>

          
         
        </div>
    </div>

  </div>
  <div class="col-md-4">

    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="card-title">Recent market items</h4>
        </div>
        <div class="card-body">


            <div class="table-responsive">
              <table class="table" width="100%" cellspacing="0" style="border-top:none">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>F</th>
                    <th>M</th>
                    <th>U</th>
                    <th>Pr</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Surplus</td>
                    <td>Blijdorp Rotterdam</td>
                    <td>1</td>
                    <td>1</td>
                    <td>0</td>
                    <td>0</td>
                    <td>21-07-2020</td>
                  </tr>
                  <tr>
                    <td>Surplus</td>
                    <td>Artis Amsterdam Zoo</td>
                    <td>0</td>
                    <td>1</td>
                    <td>0</td>
                    <td>0</td>
                    <td>20-07-2020</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="text-right">
              <a href="{{ route('surplus.index') }}" >View all surplus</a>
            </div>

        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="card-title">Recent orders</h4>
        </div>
        <div class="card-body">


            <div class="table-responsive">
              <table class="table" width="100%" cellspacing="0" style="border-top:none">
                <thead>
                  <tr>
                    <th>Order number</th>
                    <th>F</th>
                    <th>M</th>
                    <th>U</th>
                    <th>Pr</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>2020-11</td>
                    <td>1</td>
                    <td>1</td>
                    <td>0</td>
                    <td>0</td>
                    <td>21-07-2020</td>
                  </tr>
                  <tr>
                    <td>2020-10</td>
                    <td>0</td>
                    <td>1</td>
                    <td>0</td>
                    <td>0</td>
                    <td>20-07-2020</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="text-right">
              <a href="{{ route('orders.index') }}" >View all orders</a>
            </div>

        </div>
    </div>

  </div>

</div>

    
@endsection

