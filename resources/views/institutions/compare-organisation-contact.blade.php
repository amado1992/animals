@extends('layouts.admin')

@section('main-content')

    <div class="row justify-content-center">

        <div class="col-md-10">

            <div class="card shadow mb-4">

                <div class="card-body">

                    <h1 class="mb-4">Compare contact and institution</h1>
                    <div class="row mb-3">
                        <div class="col-md-8 text-center">
                            <b>Merge</b> {{ $toMerge->name }} <b>into</b> {{ $merging->name }} <b>including
                                relations</b>
                        </div>
                        <button type="button" class="col-md-2 btn btn-dark" onclick="switchOrganisations()">
                            <i class="fas fa-repeat"></i> Switch merger
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2"></div>
                        <div class="col-md-4 text-center">
                            <b>
                                @if($contact === 1)
                                    Contact
                                @else
                                    Institution
                                @endif
                            </b>
                            {{ $toMerge->name }}
                        </div>
                        <div>
                            <i class="fas fa-fw fa-arrow-right"></i>
                        </div>
                        <div class="col-md-4 text-center">
                            <b>
                                @if($contact === 0)
                                    Contact
                                @else
                                    Institution
                                @endif
                            </b>
                            {{ $merging->name }}
                        </div>
                    </div>
                    {!! Form::model($toMerge, ['method' => 'POST', 'action' => 'OrganisationController@mergeContact']) !!}

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
                        <input type="hidden" value="{{ $toMerge->id }}" name="toMergeId">
                        <input type="hidden" value="{{ $merging->id }}" name="mergerId">
                        <input type="hidden" value="{{ $contact }}" name="contact">

                        {!! Form::label('name', 'Type *', ['class' => 'col-md-2']) !!}
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span
                                        class="input-group-text">{!! Form::checkbox('check_relation_type',null) !!}</span>
                                </div>
                                {!! Form::text('name', $toMerge->relation_type, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                                {!! Form::hidden('organizationToMerge_id', $toMerge->id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('name', $merging->relation_type, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                                {!! Form::hidden('organization_id', $merging->id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        {!! Form::label('name', 'Specialty', ['class' => 'col-md-2']) !!}
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{!! Form::checkbox('check_specialty',null) !!}</span>
                                </div>
                                {!! Form::text('name', $toMerge->specialty ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                                {!! Form::hidden('organizationToMerge_id', $toMerge->id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('name', $merging->specialty ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                                {!! Form::hidden('organization_id', $merging->id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        {!! Form::label('name', 'Name *', ['class' => 'col-md-2']) !!}
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{!! Form::checkbox('check_name',null) !!}</span>
                                </div>
                                {!! Form::text('name', $toMerge->name ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                                {!! Form::hidden('organizationToMerge_id', $toMerge->id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('name', $merging->name  ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                                {!! Form::hidden('organization_id', $merging->id, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        {!! Form::label('email', 'Email *', ['class' => 'col-md-2']) !!}
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{!! Form::checkbox('check_email',null) !!}</span>
                                </div>
                                {!! Form::text('email', $toMerge->email ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('email', $merging->email ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
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
                                {!! Form::text('country', ($toMerge->country) ? $toMerge->country->name : null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('country', ($merging->country) ? $merging->country->name : null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
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
                                {!! Form::text('city', $toMerge->city, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('city', $merging->city, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
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
                                {!! Form::text('phone', $toMerge->phone, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('phone2', $merging->phone, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        {!! Form::label('website', 'Domain name *', ['class' => 'col-md-2']) !!}
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{!! Form::checkbox('check_website',null) !!}</span>
                                </div>
                                {!! Form::text('website', $toMerge->domain_name ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('website', $merging->domain_name ?? '', ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <button class="btn btn-primary btn-lg" type="submit">Merge</button>
                    @switch($from)
                        @case('contacts')
                            <a href="{{ route('contacts.show', $source_id) }}" class="btn btn-link"
                               type="button">Cancel</a>
                            @break
                        @case('contacts_to_approve')
                            <a href="{{ route('contacts-approve.show', $source_id) }}" class="btn btn-link"
                               type="button">Cancel</a>
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
            let currentURL = window.location.href;
            let regex = /contact=([01])/;
            let match = currentURL.match(regex);

            if (match) {
                let currentValue = match[1];
                let newValue = currentValue === "0" ? "1" : "0";
                window.location.href = currentURL.replace(`contact=${currentValue}`, `contact=${newValue}`);
            }
        };

        $(document).ready(function () {
            $(":checkbox:checked").prop("checked", false);
        });

    </script>

@endsection
