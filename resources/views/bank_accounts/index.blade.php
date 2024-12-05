@extends('layouts.admin')


@section('header-content')

  <div class="float-right">
      <a href="{{ route('bank_accounts.create') }}" class="btn btn-light">
        <i class="fas fa-fw fa-plus"></i> Create bank account
      </a>
      <a id="exportBankAccountInfo" href="#" class="btn btn-light">
        <i class="fas fa-fw fa-print"></i> Print info
      </a>
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-piggy-bank mr-2"></i> {{ __('Bank accounts') }}</h1>
  <p class="text-white">Here you can manage all bank accounts</p>

@endsection


@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

      @unless($bank_accounts->isEmpty())
      <div class="table-responsive">
        <table class="table clickable table-hover table-bordered datatable" width="100%" cellspacing="0">
          <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                <th>Name</th>
                <th>Currency</th>
                <th>IBAN</th>
                <th>Swift code</th>
                <th>Swift code</th>
                <th>Company</th>
                <th>Correspondent</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $bank_accounts as $bank_account )
            <tr data-url="{{ route('bank_accounts.show', [$bank_account->id]) }}">
                <td class="no-click">
                    <input type="checkbox" class="selector" value="{{ $bank_account->id }}" />
                </td>
                <td>{{ $bank_account->name }}</td>
                <td>{{ $bank_account->currency }}</td>
                <td>{{ $bank_account->iban }}</td>
                <td>{{ $bank_account->beneficiary_name }}</td>
                <td>{{ $bank_account->beneficiary_swift }}</td>
                <td>{{ $bank_account->company_name }}</td>
                <td>{{ $bank_account->correspondent_name }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p> No bank accounts are added yet </p>

      @endunless
    </div>
</div>

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

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    $('#exportBankAccountInfo').on('click', function (event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("There are not selected records to print.");
        else {
            $.ajax({
                type:'GET',
                url:"{{ route('bank_accounts.exportBankAccountInfo') }}",
                data:{
                    items: ids
                },
                success: function(response){
                    var link = document.createElement('a');

                    link.href = window.URL = response.url;

                    link.download = response.fileName;

                    link.click();
                }
            });
        }
    });

</script>

@endsection

