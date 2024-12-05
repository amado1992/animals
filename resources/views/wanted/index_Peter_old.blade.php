@extends('layouts.admin')

@section('header-content')

  <div class="float-right">
      <button type="button" class="btn btn-light" data-toggle="modal" data-target="#addWanted" >
        <i class="fas fa-fw fa-plus"></i> Add new wanted
      </button>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-search-dollar mr-2"></i> {{ __('Wanted') }}</h1>
  <p class="text-white">All wanted animals from the market are listed as demand</p>

@endsection


@section('main-content')

<div class="card shadow mb-2">
  <div class="card-body">

    <div class="row">

      <div class="col-md-4">

        <img src="{{ asset('img/animals/Pterocles_quadricinctus.jpg') }}" style="max-width: 111px;" class="rounded float-left mr-4" />


        <h4 class="card-title mb-0">Four banded sandgrouse</h4>
        <small class="text-muted border-bottom pb-2"><em>Pterocles quadricinctus</em></small>

        <br />

        <span class="bg-primary px-3 py-1 text-white rounded d-inline-block mt-3">Offered by Ernest Faber Zoo's</span>

        <div class="text-black-50 mt-3" style="font-size: 0.6rem">Created: {{ \Carbon\Carbon::now() }}</div>


      </div>
      <div class="col-md-2">
        <table class="table table-striped table-sm mb-0 text-center" style="font-size:0.8rem;">
          <tbody>
            <tr>
              <td style="width: 85px;"><b>Male</b></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Female</b></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Unknown</b></td>
              <td>1</td>
              <td>-</td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Pair</b></td>
              <td>-</td>
              <td></td>
            </tr>
          </tbody>

        </table>

      </div>

      <div class="col-md-3 offset-md-1">

        <dl class="dl-horizontal">
            <dt>Age</dt>
            <dd>Young Adult</dd>

            <dt>Origin</dt>
            <dd>Wild Caught</dd>

            <dt>Continent</dt>
            <dd>Africa</dd>
        </dl>

      </div>

      <div class="col-md-1">
        <p><b>Remarks</b><br />
        -</p>
      </div>
    </div>

  </div>
</div>

<div class="card shadow mb-2">
  <div class="card-body">

    <div class="row">
      <div class="col-4">

        <img src="{{ asset('img/animals/Lamprotornis_iris.jpg') }}" style="max-width: 111px;" class="rounded float-left mr-4" />

        <h4 class="card-title mb-0">Egyptian plover</h4>
        <small class="text-muted border-bottom pb-2"><em>Pluvianus aegyptius</em></small> <br />

        <div class="text-black-50 mt-3" style="font-size: 0.6rem">Created: {{ \Carbon\Carbon::now() }}</div>
      </div>

      <div class="col-md-2">
        <table class="table table-striped table-sm mb-0 text-center" style="font-size:0.8rem;">
          <tbody>
            <tr>
              <td style="width: 85px;"><b>Male</b></td>
              <td>3</td>
              <td>$ 80.0</td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Female</b></td>
              <td>3</td>
              <td>$ 75.0</td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Unknown</b></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Pair</b></td>
              <td></td>
              <td></td>
            </tr>
          </tbody>

        </table>

      </div>

      <div class="col-md-3 offset-md-1">

        <dl class="dl-horizontal">
            <dt>Age</dt>
            <dd>Young Adult</dd>

            <dt>Origin</dt>
            <dd>Captive Bred</dd>

            <dt>Continent</dt>
            <dd>Central America</dd>
        </dl>

      </div>

      <div class="col-md-1">
        <p><b>Remarks</b><br />
        -</p>
      </div>

    </div>

  </div>
</div>


<div class="card shadow mb-2">
  <div class="card-body">

    <div class="row">
      <div class="col-md-4">

        <img src="{{ asset('img/animals/emperor-tamarin.jpg') }}" style="max-width: 111px;" class="rounded float-left mr-4" />

        <h4 class="card-title mb-0">Emperor Tamarin</h4>
        <small class="text-muted border-bottom pb-2"><em>Saguinus imperator</em></small>
        <br />

        <div class="text-black-50 mt-3" style="font-size: 0.6rem">Last modified at {{ \Carbon\Carbon::now() }}</div>
      </div>

      <div class="col-md-2">
        <table class="table table-striped table-sm mb-0 text-center" style="font-size:0.8rem;">
          <tbody>
            <tr>
              <td style="width: 85px;"><b>Male</b></td>
              <td>3</td>
              <td>$ 80.0</td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Female</b></td>
              <td>3</td>
              <td>$ 75.0</td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Unknown</b></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td style="width: 85px;"><b>Pair</b></td>
              <td></td>
              <td></td>
            </tr>
          </tbody>

        </table>

      </div>

      <div class="col-md-3 offset-md-1">

        <dl class="dl-horizontal">
            <dt>Age</dt>
            <dd>Young Adult</dd>

            <dt>Origin</dt>
            <dd>Wild Caught</dd>

            <dt>Continent</dt>
            <dd>Europe</dd>
        </dl>

      </div>
    </div>

  </div>
</div>

@include('wanted._modal', ['modalId' => 'addWanted'])

@endsection


@section('page-scripts')


<script type="text/javascript">

$(document).ready(function() {

  var url = "{{ route('api.animals') }}";

  $.get(url , function(data, status){

    $(".autocomplete").autocomplete({
        source: data,
        treshold: 3,
        highlightClass: 'text-danger'
    });

  });

});

</script>

@endsection
