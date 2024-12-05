@extends('layouts.admin')

@section('header-content')

<div class="row">
  <div class="col-md-4">

      <h1 class="h1 text-white">2020-33</h1>
      <p class="text-white">White bennet wallaby</p>

  </div>
  <div class="col-md-2">

  </div>
  <div class="col-md-3 offset-md-3">


  </div>
</div>

@endsection

@section('main-content')

<div class="row">


        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Outstanding amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">70%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">50%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Users') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="row">
  <div class="col-md-9">

    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item">
                <a class="nav-link active" id="dashboard-tab" data-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true">Dashboard</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="animals-tab" data-toggle="tab" href="#animals" role="tab" aria-controls="animals" aria-selected="false">Animals</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices" role="tab" aria-controls="invoices" aria-selected="false">Invoices</a>
              </li>
            </ul>
        </div>
        <div class="card-body">


          <div class="tab-content" id="myTabContent">

              <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

                <table class="table table-striped table-sm">
                  <thead>
                    <tr class="table-active">
                      <th></th>
                      <th>Action</th>
                      <th>Category</th>
                      <th>Responsbile</th>
                      <th>Reminder at</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="table-success">
                      <td><i class="fas fa-fw fa-check"></i></td>
                      <td>Transaction/Order form</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr class="table-success">
                      <td><i class="fas fa-fw fa-check"></i></td>
                      <td>Veterinary import conditions</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr class="table-success">
                      <td><i class="fas fa-fw fa-check"></i></td>
                      <td>Check list of client</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr class="table-success">
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Reservation to client</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Deposit invoice to client</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>2020-08-07 12:15:00</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Reservation to supplier</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Quotation crate construction</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Quotation airfreight <span class="badge badge-primary ml-2">Vie</span></td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Quotation pick-up and other costs</td>
                      <td>Order preperation</td>
                      <td>Project manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply Cites export permit</td>
                      <td>Permits > Cites</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply Cites import permit</td>
                      <td>Permits > Cites</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply FWA permit</td>
                      <td>Permits > FWA (only for USA)</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply supplier to check preparations for Health-certificate (H.C.)</td>
                      <td>Veterinary aspects</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>See Veterinary import conditions (H.R.)</td>
                      <td>Veterinary aspects</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply supplier for quantity and/or dimensions of transport crate(s)</td>
                      <td>Crates</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply for quotation of construction crates</td>
                      <td>Crates</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply for quotation transport of empty crates</td>
                      <td>Crates</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply for booking and Airwaybill (AWB) freight forwarder</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply or Health certificate of supplier</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply for Traces certificate of supplier</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply for pick-up</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Make packing-list</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Apply for Certificate of origin</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr class="table-active">
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Inform date and time of pick-up to Supplier</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td>{!! Form::checkbox('country_id', 1, false) !!}</td>
                      <td>Inform date and arrival to Receiver.</td>
                      <td>Transport</td>
                      <td>Transport manager</td>
                      <td>-</td>
                    </tr>
                  </tbody>

                </table>

              </div>

              <div class="tab-pane fade show" id="animals" role="tabpanel" aria-labelledby="animals-tab">

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

              <div class="tab-pane fade show" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                Hier komt het financiele gedeelte
              </div>

          </div>


        </div>
    </div>

  </div>
  <div class="col-md-3">

    <div class="card shadow mb-4">

      <div class="card-header">
        <h5>Supplier</h5>
      </div>

      <div class="card-body">
        <b>Amsterdam Zoo</b>
        <p>Stationsweg 41 b<br />
        3331 LR  Zwijndrecht</p>
      </div>
    </div>

    <div class="card shadow mb-4">

      <div class="card-header">
        <h5>Client</h5>
      </div>

      <div class="card-body">
        <b>Amsterdam Zoo</b>
        <p>Stationsweg 41 b<br />
        3331 LR  Zwijndrecht</p>
      </div>

    </div>

  </div>

</div>


@endsection

