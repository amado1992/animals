@extends('layouts.admin')

@section('header-content')

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Basic details') }}</h1>
  <p class="text-white">Here you can see basic info of the company</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="basicDetailsTabs">
            <li class="nav-item">
                <a class="nav-link active" id="izs-info-tab" data-toggle="tab" href="#izsInfoTab" role="tab" aria-controls="izsInfoTab" aria-selected="true">IZS details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sites-credentials-tab" data-toggle="tab" href="#sitesCredentialsTab" role="tab" aria-controls="sitesCredentialsTab" aria-selected="false">Sites Credentials</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="letterhead-tab" data-toggle="tab" href="#letterheadTab" role="tab" aria-controls="letterheadTab" aria-selected="false">Letterhead</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="signature-stamp-tab" data-toggle="tab" href="#signatureStampTab" role="tab" aria-controls="signatureStampTab" aria-selected="false">Signature & Stamp</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="invoice-paper-tab" data-toggle="tab" href="#invoicePaperTab" role="tab" aria-controls="invoicePaperTab" aria-selected="false">Invoice paper</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="emails-gmail-tab" data-toggle="tab" href="#emailsGmailTab" role="tab" aria-controls="emailsGmailTab" aria-selected="false">Emails google</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="izsInfoTab" role="tabpanel" aria-labelledby="izs-info-tab">
                <div class="row">
                    <div class="col-md-3">
                        <span style="font-weight: bold;">Name: </span>International Zoo Services<br>
                        <span style="font-weight: bold;">Addres: </span>LOUIS COUPERUSPLEIN 2, 2514HP. The Hague, The Netherlands<br>
                        <span style="font-weight: bold;">Email: </span>info@zoo-services.com<br>
                        <span style="font-weight: bold;">Website: </span><a href="//www.zoo-services.com" target="_blank">www.zoo-services.com</a><br>
                        <span style="font-weight: bold;">Phone/Whatsapp: </span>+31854011610
                    </div>
                    <div class="col-md-3">
                        <span style="font-weight: bold;">VAT/BTW No.: </span>NL800799227B02
                    </div>
                    <div class="col-md-3">
                        <span style="font-weight: bold;">EORI No.: </span>NL076153915
                    </div>
                    <div class="col-md-3">
                        <span style="font-weight: bold;">Chamber of Commerce: </span>27115375
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="sitesCredentialsTab" role="tabpanel" aria-labelledby="sites-credentials-tab">
                @unless($sitesCodes->isEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Site name</th>
                                <th style="width: 20%;">Url</th>
                                <th style="width: 20%;">Remarks</th>
                                <th style="width: 20%;">Login username</th>
                                <th style="width: 20%;">Login password</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $sitesCodes as $code )
                            <tr>
                                <td>{{ $code->siteName }}</td>
                                <td><a href="//{{ $code->siteUrl }}" target="_blank"><u>{{ $code->siteUrl }}</u></a></td>
                                <td>{{ $code->siteRemarks }}</td>
                                <td>{{ $code->loginUsername }}</td>
                                <td>{{ ($code->loginPassword != null) ? Crypt::decryptString($code->loginPassword) : '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                @endunless
            </div>
            <div class="tab-pane fade show" id="letterheadTab" role="tabpanel" aria-labelledby="letterhead-tab">
                @unless(count($letterheadFiles) == 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Name</th>
                                    <th style="width: 20%;">Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($letterheadFiles as $letterheadFile)
                                @php
                                    $letterheadFile = pathinfo($letterheadFile);
                                @endphp
                                <tr>
                                    <td><a href="{{Storage::url('basic_details/letterhead/'.$letterheadFile['basename'])}}" target="_blank">{{$letterheadFile['basename']}}</a></td>
                                    <td>{{ FileSizeHelper::bytesToHuman(Storage::size('public/basic_details/letterhead/'.$letterheadFile['basename'])) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endunless
            </div>
            <div class="tab-pane fade show" id="signatureStampTab" role="tabpanel" aria-labelledby="signature-stamp-tab">
                @unless(count($signatureStampFiles) == 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Name</th>
                                    <th style="width: 20%;">Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($signatureStampFiles as $signatureStampFile)
                                @php
                                    $signatureStampFile = pathinfo($signatureStampFile);
                                @endphp
                                <tr>
                                    <td><a href="{{Storage::url('basic_details/signature_stamp/'.$signatureStampFile['basename'])}}" target="_blank">{{$signatureStampFile['basename']}}</a></td>
                                    <td>{{ FileSizeHelper::bytesToHuman(Storage::size('public/basic_details/signature_stamp/'.$signatureStampFile['basename'])) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endunless
            </div>
            <div class="tab-pane fade show" id="invoicePaperTab" role="tabpanel" aria-labelledby="invoice-paper-tab">
                @unless(count($invoicePaperFiles) == 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Name</th>
                                    <th style="width: 20%;">Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoicePaperFiles as $invoicePaperFile)
                                @php
                                    $invoicePaperFile = pathinfo($invoicePaperFile);
                                @endphp
                                <tr>
                                    <td><a href="{{Storage::url('basic_details/invoice_paper/'.$invoicePaperFile['basename'])}}" target="_blank">{{$invoicePaperFile['basename']}}</a></td>
                                    <td>{{ FileSizeHelper::bytesToHuman(Storage::size('public/basic_details/invoice_paper/'.$invoicePaperFile['basename'])) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endunless
            </div>
            <div class="tab-pane fade show" id="emailsGmailTab" role="tabpanel" aria-labelledby="emails-gmail-tab">
                @unless($gmailCodes->isEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Site name</th>
                                <th style="width: 20%;">Url</th>
                                <th style="width: 20%;">Remarks</th>
                                <th style="width: 20%;">Login username</th>
                                <th style="width: 20%;">Login password</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $gmailCodes as $gmailCode )
                            <tr>
                                <td>{{ $gmailCode->siteName }}</td>
                                <td><a href="//{{ $gmailCode->siteUrl }}" target="_blank"><u>{{ $gmailCode->siteUrl }}</u></a></td>
                                <td>{{ $gmailCode->siteRemarks }}</td>
                                <td>{{ $gmailCode->loginUsername }}</td>
                                <td>{{ ($gmailCode->loginPassword != null) ? Crypt::decryptString($gmailCode->loginPassword) : '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                @endunless
            </div>
        </div>
    </div>
</div>

@endsection
