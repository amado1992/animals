@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        @if (Auth::user()->hasPermission('general-documents.create'))
            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#uploadVeterinaryDoc">
                <i class="fas fa-fw fa-upload"></i> Upload
            </button>
        @endif
        @if (Auth::user()->hasPermission('general-documents.delete'))
            <button type="button" id="deleteSelectedItems" class="btn btn-light">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </button>
        @endif
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-box-open mr-2"></i> {{ __('Veterinary documents') }}</h1>
    <p class="text-white">General veterinary documents.</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header">
        <h4>Other veterinary documents</h4>
    </div>
    <div class="card-body">
        @unless(count($files) == 0)
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 20px;"><input type="checkbox" id="selectAll" name="selectAll" /></th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Date uploaded</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                    @php
                        $file = pathinfo($file);
                    @endphp
                    <tr>
                        <td><input type="checkbox" class="selector mr-2" value="{{ $file['basename'] }}" /></td>
                        <td><a href="{{Storage::url('veterinary_docs/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a></td>
                        <td>{{ FileSizeHelper::bytesToHuman(Storage::size('public/veterinary_docs/'.$file['basename'])) }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified('public/veterinary_docs/'.$file['basename']))->toDateTimeString() }}</td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        @else
            <p> No documents found. </p>
        @endunless
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
        <h4>Offers/Orders veterinary documents</h4>
    </div>
    <div class="card-body">
        @unless(count($offers) == 0)
            <div class="table-responsive">
                <table class="table table-bordered datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Offer No.</th>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Date uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offers as $offer)
                            @foreach ($offer->veterinary_docs as $document)
                                @php
                                    $file = pathinfo($document);
                                @endphp
                                <tr>
                                    <td>
                                        @if ($loop->index == 0)
                                            {{$offer->full_number}}
                                        @endif
                                    </td>
                                    <td><a href="{{Storage::url('offers_docs/'.$offer->full_number.'/veterinary_docs/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a></td>
                                    <td>{{ FileSizeHelper::bytesToHuman(Storage::size('public/offers_docs/'.$offer->full_number.'/veterinary_docs/'.$file['basename'])) }}</td>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified('public/offers_docs/'.$offer->full_number.'/veterinary_docs/'.$file['basename']))->toDateTimeString() }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p> No documents found. </p>
        @endunless
    </div>
</div>

@include('uploads.upload_modal', ['modalId' => 'uploadVeterinaryDoc', 'route' => 'veterinary_documents.upload_file'])

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

    $('#deleteSelectedItems').on('click', function () {
        var file_names = [];
        $(":checked.selector").each(function(){
            file_names.push($(this).val());
        });

        if(file_names.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('veterinary_documents.delete_files') }}",
                data:{items: file_names},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection

