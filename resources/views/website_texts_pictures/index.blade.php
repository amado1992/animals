@extends('layouts.admin')

@section('header-content')
  <div class="float-right">
      @if (Auth::user()->hasPermission('standard-texts.create'))
        <a href="{{ route('website-texts.create') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
      @endif
      <button type="button" id="filterWebsiteTextButton" class="btn btn-light" data-toggle="modal" data-target="#filterWebsiteText">
        <i class="fas fa-fw fa-search"></i> Filter
      </button>
      <a href="{{ route('website-texts.showAll') }}" class="btn btn-light">
        <i class="fas fa-fw fa-window-restore"></i> Show all
      </a>
      @if (Auth::user()->hasPermission('standard-texts.delete'))
        <button type="button" id="deleteSelectedItems" class="btn btn-light">
            <i class="fas fa-fw fa-window-close"></i> Delete
        </button>
      @endif
  </div>

  <h1 class="h1 text-white"><i class="fas fa-fw fa-info mr-2"></i> {{ __('Website') }}</h1>
  <p class="text-white">List of all website texts and pictures</p>
  {!! Form::hidden('selectedWebsiteTab', $selectedWebsiteTab) !!}
@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="websiteTabs">
            <li class="nav-item">
                <a class="nav-link active" id="website-texts-tab" data-toggle="tab" href="#websiteTextsTab" role="tab" aria-controls="websiteTextsTab" aria-selected="true">Website texts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="website-images-tab" data-toggle="tab" href="#websiteImagesTab" role="tab" aria-controls="websiteImagesTab" aria-selected="false">Website images</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="websiteTextsTab" role="tabpanel" aria-labelledby="website-texts-tab">
                <div class="d-flex flex-row align-items-center mb-3">
                    <span class="mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('website-texts.removeFromWebsiteTextSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>

                @unless($websiteTexts->isEmpty())
                <div class="table-responsive">
                    <table class="table clickable table-hover table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 2%;"><input type="checkbox" id="selectAllTexts" name="selectAllTexts" /></th>
                            <th style="width: 10%;">Date modified</th>
                            <th style="width: 20%">Remarks</th>
                            <th style="width: 34%;">English text</th>
                            <th style="width: 34%;">Spanish text</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $websiteTexts as $websiteText )
                            <tr @if (Auth::user()->hasPermission('standard-texts.update')) data-url="{{ route('website-texts.edit', [$websiteText->id]) }}" @endif>
                                <td class="no-click">
                                    <input type="checkbox" class="selectorText" value="{{ $websiteText->id }}" />
                                </td>
                                <td>{{ ($websiteText->updated_at != null) ? date('Y-m-d', strtotime($websiteText->updated_at)) : '' }}</td>
                                <td>{{ $websiteText->remarks }}</td>
                                <td>{!! $websiteText->english_text !!}</td>
                                <td>{!! $websiteText->spanish_text !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                {{ $websiteTexts->links() }}
                @else
                    <p> No website texts.</p>
                @endunless
            </div>
            <div class="tab-pane fade show" id="websiteImagesTab" role="tabpanel" aria-labelledby="website-images-tab">
                <div class="d-flex mb-3">
                    <button type="button" class="btn btn-light mr-3" data-toggle="modal" data-target="#uploadWebsiteImage">
                        <i class="fas fa-fw fa-upload"></i> Upload
                    </button>
                    <button type="button" id="deleteSelectedImages" class="btn btn-light">
                        <i class="fas fa-fw fa-window-close"></i> Delete
                    </button>
                </div>
                @unless(count($files) == 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 20px;"><input type="checkbox" id="selectAllImages" name="selectAllImages" /></th>
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
                                    <td><input type="checkbox" class="selectorImage mr-2" value="{{ $file['basename'] }}" /></td>
                                    <td><a href="{{Storage::url('website_images/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a></td>
                                    <td>{{ FileSizeHelper::bytesToHuman(Storage::size('public/website_images/'.$file['basename'])) }}</td>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified('public/website_images/'.$file['basename']))->toDateTimeString() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p> No images found. </p>
                @endunless
            </div>
        </div>
    </div>
  </div>

  @include('website_texts_pictures._modal', ['modalId' => 'filterWebsiteText'])

  @include('uploads.upload_modal', ['modalId' => 'uploadWebsiteImage', 'route' => 'website-texts.upload_file'])

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

    $('#websiteTabs a[href="'+$('[name=selectedWebsiteTab]').val()+'"]').tab('show');

    $('#websiteTabs a').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            type:'POST',
            url:"{{ route('website-texts.selectedWebsiteTab') }}",
            data:{
                websiteTab: $(this).attr('href')
            },
            success:function(data){
                $(this).tab('show');
            }
        });
    })

    $('#selectAllTexts').on('change', function () {
        $(":checkbox.selectorText").prop('checked', this.checked);
    });

    $('#deleteSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selectorText").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('website-texts.deleteWebsiteTexts') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#selectAllImages').on('change', function () {
        $(":checkbox.selectorImage").prop('checked', this.checked);
    });

    $('#deleteSelectedImages').on('click', function () {
        var ids = [];
        $(":checked.selectorImage").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected items?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('website-texts.deleteWebsiteImages') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

</script>

@endsection

