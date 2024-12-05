@extends('layouts.admin')

@section('main-content')

<div class="row justify-content-center">

  <div class="col-md-10">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h3 class="mb-4">
               <b>Convert</b> {!! $type === "contact" ? "contact <b>to</b> organisation" : "organisation <b>to</b> contact"  !!} <b>including relations</b>
            </h3>
            <h5>
                Note that the original {{ $type === "contact" ? "contact" : "organisation" }} will be deleted.
            </h5>

            @if ($errors->any())
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-2"></div>
                <div class="col-md-4 text-center">Original {{ $type === "contact" ? "Contact" : "Organisation" }}</div>
                <i class="fas fa-fw fa-arrow-right"></i>
                <div class="col-md-4 text-center">New {{ $type !== "contact" ? "Contact" : "Organisation" }}</div>
            </div>

            @if($type === "contact")
                {!! Form::model($model, ['method' => 'POST', 'action' => 'OrganisationController@convert']) !!}
                {!! Form::hidden('id', $model->id, ['class' => 'form-control']) !!}
                {!! Form::hidden('type', $type, ['class' => 'form-control']) !!}

                <div class="form-row">
                    {!! Form::label('name', 'Name', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('name_original', $model->first_name, ['class' => 'form-control', 'readonly' => true]) !!}
                            {!! Form::text('name_original', $model->last_name, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('name', $model->name, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('relation_type', 'Relation type', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('relation_type_original', ['supplier' => 'supplier', 'client' => 'client', 'both' => 'both'], $model->relation_type, ['class' => 'form-control', 'disabled' => true, 'placeholder' => '-']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('relation_type', ['supplier' => 'supplier', 'client' => 'client', 'both' => 'both'], $model->relation_type, ['class' => 'form-control', 'placeholder' => 'Select relation type']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('specialty', 'Specialty', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('specialty_original', \App\Enums\Specialty::get(), $model->specialty, ['class' => 'form-control', 'disabled' => true, 'placeholder' => '-']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('specialty', \App\Enums\Specialty::get(), $model->specialty, ['class' => 'form-control', 'placeholder' => 'Select specialty']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('email', 'Domain name', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('domain_name_original', $model->domain_name, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('domain_name', $model->domain_name, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('organisation_type', 'Organisation type', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('organisation_type', $organisationTypes, null, ['class' => 'form-control', 'placeholder' => 'Select organisation type']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('email', 'Email', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('email', $model->email, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('email', $model->email, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('phone', 'Phone', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('phone_original', $model->mobile_phone, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('phone', $model->mobile_phone, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('fax', 'Fax', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('fax', $model->mobile_phone, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('website', 'Website', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('website', "www." . $model->domain_name, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('facebook_page', 'Facebook page', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('facebook_page', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('address', 'Address', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('address', $model->mobile_phone, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('zipcode', 'Zipcode', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('zipcode', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('city', 'City', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        {!! Form::text('city', $model->city, ['class' => 'form-control', 'readonly' => true]) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('city', $model->city, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('country', 'Country', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        {!! Form::select('country', $countries, $model->country_id, ['class' => 'form-control', 'disabled' => true]) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('country', $countries, $model->country_id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('vat_number', 'Vat number', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('vat_number', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('level', 'Level', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('level', ['A' => 'A', 'B' => 'B', 'C' => 'C'], null, ['class' => 'form-control', 'placeholder' => 'Select level']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('info_status', 'Info status', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('info_status', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('remarks', 'Remarks', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('open_remarks', 'Open remarks', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('open_remarks', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('internal_remarks', 'Internal remarks', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('internal_remarks', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('short_description', 'Short description', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('short_description', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('email', 'Mailing category', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        {!! Form::select('mailing_category', \App\Enums\ContactMailingCategory::get(), $model->mailing_category, ['class' => 'form-control', 'placeholder' => '-', 'disabled' => true]) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('mailing_category', \App\Enums\ContactMailingCategory::get(), $model->mailing_category, ['class' => 'form-control', 'placeholder' => 'Select mailing category']) !!}
                        </div>
                    </div>
                </div>

                <hr class="mb-4">

                <button class="btn btn-primary btn-lg" type="submit">Convert</button>
                <a href="{{ route('organisations.index') }}" class="btn btn-link" type="button">Cancel</a>
                {!! Form::close() !!}
            @else
                {!! Form::model($model, ['method' => 'POST', 'action' => 'OrganisationController@convert']) !!}
                {!! Form::hidden('id', $model->id, ['class' => 'form-control']) !!}
                {!! Form::hidden('type', $type, ['class' => 'form-control']) !!}

                <div class="form-row">
                    {!! Form::label('name', 'Name', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('name_original', $model->name, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group">
                                {!! Form::text('first_name', $model->name, ['class' => 'form-control', 'placeholder' => 'First name...']) !!}
                                {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'Last name...']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('relation_type', 'Relation type', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('relation_type_original', ['supplier' => 'supplier', 'client' => 'client', 'both' => 'both'], $model->relation_type, ['class' => 'form-control', 'disabled' => true, 'placeholder' => '-']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('relation_type', ['supplier' => 'supplier', 'client' => 'client', 'both' => 'both'], $model->relation_type, ['class' => 'form-control', 'placeholder' => 'Select relation type']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('specialty', 'Specialty', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('specialty_original', \App\Enums\Specialty::get(), $model->specialty, ['class' => 'form-control', 'disabled' => true, 'placeholder' => '-']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('specialty', \App\Enums\Specialty::get(), $model->specialty, ['class' => 'form-control', 'placeholder' => 'Select specialty']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('title', 'Title', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('title', ['Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.', 'Dr.' => 'Dr.', 'Ing.' => 'Ing.'], null, ['class' => 'form-control', 'placeholder' => 'Select title']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('position', 'Position', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('position', null, ['class' => 'form-control', 'placeholder' => 'Position...']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('email', 'Email', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('email', $model->email, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('email', $model->email, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('domain_name', 'Domain name', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('domain_name_original', $model->domain_name, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('domain_name', $model->domain_name, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('country', 'Country', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        {!! Form::select('country', $countries, $model->country_id, ['class' => 'form-control', 'disabled' => true]) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('country', $countries, $model->country_id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('city', 'City', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        {!! Form::text('city', $model->city, ['class' => 'form-control', 'readonly' => true]) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('city', $model->city, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('mobile_phone', 'Mobile phone', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('phone_original', $model->phone, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('mobile_phone', $model->phone, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('source', 'Source', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('source', ['website' => 'website', 'crm' => 'crm'], null, ['class' => 'form-control', 'placeholder' => 'Select source']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('member_approved_status', 'Approved status', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('member_approved_status', \App\Enums\ContactApprovedStatus::get(), null, ['class' => 'form-control', 'placeholder' => 'Select approved status']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('email', 'Mailing category', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        {!! Form::select('mailing_category', \App\Enums\ContactMailingCategory::get(), $model->mailing_category, ['class' => 'form-control', 'placeholder' => '-', 'disabled' => true]) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::select('mailing_category', \App\Enums\ContactMailingCategory::get(), $model->mailing_category, ['class' => 'form-control', 'placeholder' => 'Select mailing category']) !!}
                        </div>
                    </div>
                </div>

                <hr class="mb-4">

                <button class="btn btn-primary btn-lg" type="submit">Convert</button>
                <a href="{{ route('organisations.index') }}" class="btn btn-link" type="button">Cancel</a>
                {!! Form::close() !!}
            @endif
        </div>
      </div>

    </div>
  </div>

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
    });

</script>

@endsection
