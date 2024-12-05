@extends('layouts.admin')

@section('header-content')

<div class="row">
  <div class="col-md-3">
    <h1 class="h1 text-white">Offer {{ $offer->offer_number }}</h1>
  </div>
  <div class="col-md-9">
    <div class="text-white">
      Destination: Havana, Cuba <br />
      <span style="font-size: 0.6rem">Requested at: {{ \Carbon\Carbon::now() }}</span>
    </div>
  </div>
</div>


@endsection


@section('main-content')

<div class="row">
  <div class="col-md-9">

    <div class="card shadow mb-4">

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true">Dashboard</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#calculation" role="tab" aria-controls="calculation" aria-selected="false">Calculation</a>
              </li>
            </ul>
        </div>

        <div class="card-body">

          <div class="tab-content" id="myTabContent">

              <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

                    <table class="table table-striped" width="100%" cellspacing="0">
                      <thead>
                        <tr class="table-active">
                          <th>Quanity</th>
                          <th>Sex</th>
                          <th>SPECIES</th>
                          <th></th>
                          <th class="text-center">Cost price</th>
                          <th class="text-center">In USD</th>
                          <th class="text-right">Profit USD</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="align-middle">1</td>
                          <td class="align-middle">M</td>
                          <td class="align-middle">Saddle billed stork <i>(Ephippiorhynchus senegalensis)</i></td>
                          <td class="align-middle">$</td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 40.000 <br />
                            Sell. 44.200
                          </td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 85.000 <br />
                            Sell. 86.500
                          </td>
                          <td class="align-middle text-right">7.000</td>

                        </tr>

                        <tr>
                          <td class="align-middle">1</td>
                          <td class="align-middle">M</td>
                          <td class="align-middle">Inca tern</td>
                          <td class="align-middle">$</td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 40.000 <br />
                            Sell. 44.200
                          </td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 85.000 <br />
                            Sell. 86.500
                          </td>
                          <td class="align-middle text-right">7.000</td>
                        </tr>

                        <tr>
                          <td class="align-middle">1</td>
                          <td class="align-middle">M</td>
                          <td class="align-middle">Inca tern</td>
                          <td class="align-middle">$</td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 40.000 <br />
                            Sell. 44.200
                          </td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 85.000 <br />
                            Sell. 86.500
                          </td>
                          <td class="align-middle text-right">7.000</td>
                        </tr>

                      </tbody>
                    </table>

                    <table class="table table-striped" width="100%" cellspacing="0">
                      <thead>
                        <tr class="table-active">
                          <th>Quanity</th>
                          <th>CRATES</th>
                          <th></th>
                          <th class="text-center">Cost price</th>
                          <th class="text-center">In USD</th>
                          <th class="text-right">Profit USD</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="align-middle">1</td>
                          <td class="align-middle">42cm x 43cm x 80cm <i>(Crate 31)</i></td>
                          <td class="align-middle">$</td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 400 <br />
                            Sell. 450
                          </td>
                          <td class="text-center" style="line-height: 1.8rem">
                            Cost. 400 <br />
                            Sell. 450
                          </td>
                          <td class="align-middle text-right">500</td>

                        </tr>


                      </tbody>
                    </table>

              </div>

              <div class="tab-pane fade show" id="calculation" role="tabpanel" aria-labelledby="calculation-tab">

                  Show calculation

              </div>

          </div>


        </div>
    </div>

  </div>
  <div class="col-md-3">

    <div class="card shadow mb-4">

        <div class="card-header">
          <h5>Internal remarks</h5>
        </div>

        <div class="card-body">

            <p>This client has urgency to open up his zoo.</p>


        </div>
    </div>

    <div class="card shadow mb-4">

        <div class="card-header">
          <h5>Client details</h5>
        </div>

        <div class="card-body">


          <table class="table">
            <tr>
              <td class="font-weight-bold">Institution</td>
              <td>Blijdorp Rotterdam</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Contact</td>
              <td>Hector Lam</td>
            </tr>
            <tr>
              <td class="font-weight-bold">E-mail address</td>
              <td>hector.lam@blijdorp.nl</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Phone number</td>
              <td>0031 615034687</td>
            </tr>
            <tr>
              <td class="font-weight-bold">Country</td>
              <td>Netherlands</td>
            </tr>
          </table>

        </div>
    </div>

    <div class="card shadow mb-4">

        <div class="card-header">
          <h5>Contact history</h5>
        </div>

        <div class="card-body">

            <p>Client request a price</p>
            <p>Email #2934234 received</p>


        </div>
    </div>


  </div>

</div>


@endsection

