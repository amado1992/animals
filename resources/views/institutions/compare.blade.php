@extends('layouts.admin')

@section('main-content')

<div class="row justify-content-center">

  <div class="col-md-10">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Compare two institutions</h1>
            <div class="row mb-3">
                <div class="col-md-8 text-center">
                    <b>Merge</b> {{ $organisation->name }} <b>into</b> {{ $organisationToMerge->name }} <b>including relations</b>
                </div>
                <button type="button" class="col-md-2 btn btn-dark" onclick="switchOrganisations()">
                    <i class="fas fa-repeat"></i> Switch merger
                </button>
            </div>
            <div class="row mb-3">
                <div class="col-md-2"></div>
                <div class="col-md-4 text-center"><b>Institution</b> - {{ $organisation->name }}</div>
                <div>
                    <i class="fas fa-fw fa-arrow-right"></i>
                </div>
                <div class="col-md-4 text-center"><b>Institution</b> - {{ $organisationToMerge->name }}</div>
            </div>
            {!! Form::model($organisation, ['method' => 'PATCH', 'action' => 'OrganisationController@merge']) !!}

                <div class="form-row">
                    <p class="col-md-2"></p>
                    <div class="col-md-4">
                        <button type="button" onclick="checkAll()" class="btn btn-light" data-toggle="modal"
                                data-target="#filterStandardDocument">
                            <i class="fas fa-fw fa-check"></i> Alle velden selecteren
                        </button>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('name', 'Institution name *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_name',null) !!}</span>
                            </div>
                            {!! Form::text('name', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            {!! Form::hidden('organization_id', $organisation->id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('name', $organisationToMerge->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            {!! Form::hidden('organizationToMerge_id', $organisationToMerge->id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('organisation_type', 'Institution type *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_type',null) !!}</span>
                            </div>
                            {!! Form::text('organisation_type', $organisation->type->label, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('organisation_type', $organisationToMerge->type->label, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('email', 'Institution email *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_email',null) !!}</span>
                            </div>
                            {!! Form::text('email', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('email', $organisationToMerge->email, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('address', 'Address *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_address',null) !!}</span>
                            </div>
                            {!! Form::text('address', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('address', $organisationToMerge->address, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('zipcode', 'Zipcode *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_zipcode',null) !!}</span>
                            </div>
                            {!! Form::text('zipcode', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('zipcode', $organisationToMerge->zipcode, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('country_id', 'Country *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_country',null) !!}</span>
                            </div>
                            {!! Form::text('country', ($organisation->country) ? $organisation->country->name : null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('country', ($organisationToMerge->country) ? $organisationToMerge->country->name : null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('city', 'City *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_city',null) !!}</span>
                            </div>
                            {!! Form::text('city', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('city', $organisationToMerge->city, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('phone', 'Phone *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_phone',null) !!}</span>
                            </div>
                            {!! Form::text('phone', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('phone2', $organisationToMerge->phone, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('fax', 'Fax *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_fax',null) !!}</span>
                            </div>
                            {!! Form::text('fax', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('fax', $organisationToMerge->fax, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('website', 'Website *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_website',null) !!}</span>
                            </div>
                            {!! Form::text('website', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('website', $organisationToMerge->website, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('facebook_page', 'Facebook page *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_facebook_page',null) !!}</span>
                            </div>
                            {!! Form::text('facebook_page', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('facebook_page', $organisationToMerge->facebook_page, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('vat_number', 'Vat number *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_vat_number',null) !!}</span>
                            </div>
                            {!! Form::text('vat_number', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('vat_number', $organisationToMerge->vat_number, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('level', 'Level *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_level',null) !!}</span>
                            </div>
                            {!! Form::text('level', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('level', $organisationToMerge->level, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('info_status', 'Info status *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_info_status',null) !!}</span>
                            </div>
                            {!! Form::text('info_status', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('info_status', $organisationToMerge->info, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('remarks', 'Remarks *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_remarks',null) !!}</span>
                            </div>
                            {!! Form::text('remarks', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('remarks', $organisationToMerge->remarks, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('short_description', 'Short description *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_short_description',null) !!}</span>
                            </div>
                            {!! Form::text('short_description', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('short_description', $organisationToMerge->short_description, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('public_zoos_relation', 'Public zoos relation *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_public_zoos_relation',null) !!}</span>
                            </div>
                            {!! Form::text('public_zoos_relation', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('public_zoos_relation', $organisationToMerge->public_zoos_relation, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('animal_related_association', 'Animal related association *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_animal_related_association',null) !!}</span>
                            </div>
                            {!! Form::text('animal_related_association', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('animal_related_association', $organisationToMerge->animal_related_association, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

            <hr class="mb-4">

            <button class="btn btn-primary btn-lg" type="submit">Merge</button>
            @switch($from)
                @case('contacts')
                    <a href="{{ route('contacts.show', $source_id) }}" class="btn btn-link" type="button">Cancel</a>
                    @break
                @case('contacts_to_approve')
                    <a href="{{ route('contacts-approve.show', $source_id) }}" class="btn btn-link" type="button">Cancel</a>
                    @break
                @case('organization')
                    <a href="{{ route('organisations.show', $source_id) }}" class="btn btn-link" type="button">Cancel</a>
                    @break
                @case('organization_doubles')
                    <a href="{{ route('organisations.searchDoubles') }}" class="btn btn-link" type="button">Cancel</a>
                    @break
                @default
                    <a href="{{ route('organisations.index') }}" class="btn btn-link" type="button">Cancel</a>
            @endswitch

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

@section('page-scripts')

<script type="text/javascript">
    const checkAll = () => {
        document.querySelectorAll("input[type=checkbox]").forEach(element => {
            element.checked = !element.checked;
        });
    };

    const switchOrganisations = () => {
        window.location = "{{ route("organisations.compare", [$organisationToMerge->id, $organisation->id, "organizations", 0]) }}"
    }

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
    });

</script>

@endsection
