@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
    <li class="breadcrumb-item active">Find duplicated contacts</li>
</ol>
@endsection

@section('header-content')

<div class="col-md-12">
    <div class="float-right">
        @if (Auth::user()->hasPermission('contacts.update'))
            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                <i class="fas fa-fw fa-edit"></i> Edit selection
            </button>
        @endif
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Find duplicated contacts') }}</h1>
    <p class="text-white">Find contacts that are doubles.</p>
</div>

@endsection

@section('main-content')

<div class="card shadow mb-2">
    <div class="card-body">
        {!! Form::open(['route' => 'contacts.filterDoubles', 'method' => 'GET', 'class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::label('filter_country', 'Country:') !!}
                {!! Form::select('filter_country', $countries, null, ['class' => 'form-control ml-1', 'style' => 'width: 200px', 'placeholder' => '- select -']) !!}
            </div>
            <div class="form-group ml-3">
                {!! Form::label('filter_city', 'City:') !!}
                {!! Form::select('filter_city', array(), null, ['class' => 'form-control ml-1', 'style' => 'width: 200px', 'placeholder' => '- select -']) !!}
            </div>
            <div class="form-group ml-3">
                {!! Form::label('filter_doubles_by', 'Doubles by:', ['class' => 'mr-2']) !!}
                {!! Form::radio('filter_doubles_by', 'full_name', true) !!}
                {!! Form::label('by_name', 'Name', ['class' => 'ml-1 mr-2']) !!}
                {!! Form::radio('filter_doubles_by', 'email') !!}
                {!! Form::label('by_email', 'Email', ['class' => 'ml-1 mr-2']) !!}
                {!! Form::radio('filter_doubles_by', 'domain_name') !!}
                {!! Form::label('by_domain_name', 'Domain name', ['class' => 'ml-1']) !!}
            </div>
            <div class="form-group ml-3">
                <button type="submit" class="btn btn-primary mr-2">Search</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        {!! Form::close() !!}
    </div>
</div>

@if (isset($contacts))
    <div class="card shadow mb-4">
        <div class="card-body">

          @unless($contacts->isEmpty())
          <div class="table-responsive mb-2" style="overflow-x: auto; overflow-y: hidden;">
            <table class="table table-bordered" width="100%" cellspacing="0">
              <thead>
                <tr>
                    <th></th>
                    <th @if ($criteria == 'full_name')
                        style="text-decoration: underline;"
                    @endif>Name</th>
                    <th @if ($criteria == 'email')
                        style="text-decoration: underline;"
                    @endif>Email address</th>
                    <th>City</th>
                    <th @if ($criteria == 'domain_name')
                        style="text-decoration: underline;"
                    @endif>Domain name</th>
                </tr>
              </thead>
              <tbody>
                @foreach($contacts as $contact)
                    <tr @if ($contact->source == 'website')
                        style="color: red;"
                    @else
                        style="color: black;"
                    @endif>
                        <td>
                            <input type="checkbox" class="selector" value="{{ $contact->id }}" />
                        </td>
                        <td>{{ $contact->full_name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->city }}</td>
                        <td>{{ $contact->domain_name }}</td>
                    </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{$contacts->links()}}
          @else
            <p> No contacts doubles were found. </p>
          @endunless
        </div>
    </div>
@endif

@include('contacts.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('[name=filter_country]').on('change', function () {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('countries.getAirportsByCountryId') }}",
            data:{
                value: value,
            },
            success:function(data) {
                $('[name=filter_city]').empty();
                $('[name=filter_city]').append('<option value="">- select -</option>');
                $.each(data.airports, function(key, value) {
                    $('[name=filter_city]').append('<option value="'+ key +'">' + value +'</option>');
                });
            }
        });
    });

    $('#mergeOption').on('click', function() {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length < 2 || ids.length > 2)
            alert("You must select 2 contacts to merge.");
        else {
            var contact_id1 = ids[0];
            var contact_id2 = ids[1];

            var url = '{{ route("contacts.compare", ["id1", "id2", "contact_doubles", 0]) }}';
            url = url.replace('id1', contact_id1);
            url = url.replace('id2', contact_id2);
            window.location = url;
        }
    });

    $('#editSelectedRecords').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#sendEditSelectionForm').on('click', function(event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to edit.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('contacts.editSelectedRecords') }}",
                data:{
                    items: ids,
                    title: $('#editSelectedRecords [name=title]').val(),
                    first_name: $('#editSelectedRecords [name=first_name]').val(),
                    last_name: $('#editSelectedRecords [name=last_name]').val(),
                    institution_id: $('#editSelectedRecords [name=institution_id]').val(),
                    institution_type: $('#editSelectedRecords [name=organisation_type]').val(),
                    level: $('#editSelectedRecords [name=level]').val(),
                    mailing_category: $('#editSelectedRecords [name=mailing_category]').val()
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });
</script>

@endsection
