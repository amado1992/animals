@extends('layouts.admin')
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/activity-timeline/base/index.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/activity-timeline/base/salesforce-lightning-design-system.css') }}">
@endsection

@section('header-content')

    <div class="row mb-2">
        <div class="col-md-3">
            <h1 class="h1 text-white"><i class="fas fa-fw fa-tasks mr-2"></i> {{ __('Tasks') }}</h1>
            <p class="text-white">Mark tasks as finished/unfinished</p>
        </div>
        <div class="col-md-9 text-right">
            <a href="{{ route('tasks.create') }}" class="btn btn-light">
                <i class="fas fa-fw fa-plus"></i> Add
            </a>
            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterTasks">
                <i class="fas fa-fw fa-search"></i> Filter
            </button>
            <a href="{{ route('tasks.showAllTodayTasks') }}" class="btn btn-light">
                <i class="fas fa-fw fa-window-restore"></i> Show all
            </a>
            <button type="button" id="deleteSelectedItems" class="btn btn-light">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </button>
        </div>
    </div>
    <div class="d-flex flex-row justify-content-between items-center text-white mb-2">
        <div class="d-flex align-items-center">
            <h4>My tasks for today and pending tasks</h4>
        </div>
    </div>

    @if (Auth::user()->hasPermission('orders.see-all-orders'))
        <div class="float-right ml-2">
            {{$todayTasks->links()}}
        </div>
    @endif
    {!! Form::hidden('selectedTasksTab', $selectedTasksTab) !!}
@endsection

@section('main-content')
<div class="card shadow mb-2">
    <div class="card-body">
        <div class="d-flex flex-row items-center">
            <div class="d-flex align-items-center">
                <input type="checkbox" id="selectAll" name="selectAll" class="{{ str_replace("#", "", $selectedTasksTab) }}" />&nbsp;Select all
                <input type="hidden" id="countContactsVisible" value="{{ ($todayTasks->count() > 0) ? $todayTasks->count() : 0 }}" />
            </div>

            <div class="d-flex align-items-center">
                <span class="ml-3 mr-1">Filtered on:</span>
                @foreach ($filterData as $key => $value)
                    <a href="{{ route('tasks.removeFromTaskSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                @endforeach
            </div>
        </div>
    </div>
</div>


<div class="card shadow mb-4">
    <div class="card-body">
        <ul class="nav nav-tabs card-header-tabs mb-2" id="myTabTasks">
            <li class="nav-item active">
                <a class="nav-link" id="today-tab" data-toggle="tab" href="#todayTab" role="tab" aria-controls="todayTab" aria-selected="true"> Need to be ready today @if(!empty($todayTasks) && $todayTasks->total() > 0) <span class="badge badge-soft-danger float-end ms-2 total-inbox" style="color: #fff;background: #f1556c;">{{ $todayTasks->total() }}</span>@endif</a>
            </li>
            @if (Auth::user()->hasPermission('tasks.complete') || Auth::user()->hasRole(['admin']))
                <li class="nav-item">
                    <a class="nav-link" id="forapproval-tab" data-toggle="tab" href="#forapprovalTab" role="tab" aria-controls="forapprovalTab" aria-selected="false"> Completed by the receiver @if(!empty($forApprovalTasks) && $forApprovalTasks->total() > 0) <span class="badge badge-soft-danger float-end ms-2 total-inbox" style="color: #fff;background: #f1556c;">{{ $forApprovalTasks->total() }}</span>@endif</a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" id="complete-tab" data-toggle="tab" href="#completeTab" role="tab" aria-controls="completeTab" aria-selected="false"> Completed that are checked by the sender and put in: done</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="incomplete-tab" data-toggle="tab" href="#incompleteTab" role="tab" aria-controls="incompleteTab" aria-selected="false"> Not completed or partially completed: that are expired. @if(!empty($noCompleteTasks) && $noCompleteTasks->total() > 0) <span class="badge badge-soft-danger float-end ms-2 total-inbox" style="color: #fff;background: #f1556c;">{{ $noCompleteTasks->total() }}</span>@endif</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="future-tab" data-toggle="tab" href="#futureTab" role="tab" aria-controls="futureTab" aria-selected="false"> Coming days and weeks. @if(!empty($futureTasks) && $futureTasks->total() > 0) <span class="badge badge-soft-danger float-end ms-2 total-inbox" style="color: #fff;background: #f1556c;">{{ $futureTasks->total() }}</span>@endif</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="todayTab" role="tabpanel" aria-labelledby="today-tab">
                <div class="row mb-4 pages-right">
                    <div class="col-md-12">
                        <div class="float-right">
                            <div class="d-flex align-items-center" style="margin: 11px 0 0 -36px !important;">
                                Page: {{$todayTasks->currentPage()}} | Records:&nbsp;
                                @if (Auth::user()->hasPermission('orders.see-all-orders'))
                                    {!! Form::open(['id' => 'recordsPerPageFormOther', 'route' => 'tasks.recordsPerPage', 'method' => 'GET']) !!}
                                        {!! Form::text('recordsPerPageToday', $todayTasks->count(), ['id' => 'recordsPerPageToday', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                                    {!! Form::close() !!}
                                @else
                                    {{$todayTasks->count()}}
                                @endif
                                &nbsp;| Total: {{$todayTasks->total()}}
                                <a href="#" class="btn btn-success ml-3 updateStatusTask" data-status="forapproval">
                                    <i class="fas fa-fw fa-check"></i> For approval
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @unless($todayTasks->isEmpty())
                    <ul class="slds-timeline pt-2">
                        @foreach($todayTasks as $task)
                            @include('tasks.item_tasks')
                        @endforeach
                    </ul>

                    {{$todayTasks->links()}}
                @else
                    <p>No tasks for today.</p>
                @endunless
            </div>
            <div class="tab-pane fade show" id="forapprovalTab" role="tabpanel" aria-labelledby="forapproval-tab">
                <div class="row mb-4 pages-right">
                    <div class="col-md-12">
                        <div class="float-right">
                            <div class="d-flex align-items-center" style="margin: 11px 0 0 -36px !important;">
                                Page: {{$forApprovalTasks->currentPage()}} | Records:&nbsp;
                                @if (Auth::user()->hasPermission('orders.see-all-orders'))
                                    {!! Form::open(['id' => 'recordsPerPageFormOther', 'route' => 'tasks.recordsPerPage', 'method' => 'GET']) !!}
                                        {!! Form::text('recordsPerPageForApproval', $forApprovalTasks->count(), ['id' => 'recordsPerPageForApproval', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                                    {!! Form::close() !!}
                                @else
                                    {{$forApprovalTasks->count()}}
                                @endif
                                &nbsp;| Total: {{$forApprovalTasks->total()}}
                                <a href="#" class="btn btn-success ml-3 notCompleteModal">
                                    <i class="fas fa-window-close"></i> Not Complete
                                </a>
                                <a href="#" class="btn btn-success ml-3 updateStatusTask" data-status="complete">
                                    <i class="fas fa-fw fa-check"></i> Complete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @unless($forApprovalTasks->isEmpty())
                    <ul class="slds-timeline pt-2">
                        @foreach($forApprovalTasks as $task)
                            @include('tasks.item_tasks')
                        @endforeach
                    </ul>

                    {{$forApprovalTasks->links()}}
                @else
                    <p>No tasks for approval.</p>
                @endunless
            </div>
            <div class="tab-pane fade show" id="completeTab" role="tabpanel" aria-labelledby="complete-tab">
                <div class="row mb-4 pages-right">
                    <div class="col-md-12">
                        <div class="float-right">
                            <div class="d-flex align-items-center" style="margin: 11px 0 0 -36px !important;">
                                Page: {{$completeTasks->currentPage()}} | Records:&nbsp;
                                @if (Auth::user()->hasPermission('orders.see-all-orders'))
                                    {!! Form::open(['id' => 'recordsPerPageFormOther', 'route' => 'tasks.recordsPerPage', 'method' => 'GET']) !!}
                                        {!! Form::text('recordsPerPageComplete', $completeTasks->count(), ['id' => 'recordsPerPageComplete', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                                    {!! Form::close() !!}
                                @else
                                    {{$completeTasks->count()}}
                                @endif
                                &nbsp;| Total: {{$completeTasks->total()}}
                            </div>
                        </div>
                    </div>
                </div>
                @unless($completeTasks->isEmpty())
                    <ul class="slds-timeline pt-2">
                        @foreach($completeTasks as $task)
                            @include('tasks.item_tasks')
                        @endforeach
                    </ul>

                    {{$completeTasks->links()}}
                @else
                    <p>No tasks complete.</p>
                @endunless
            </div>
            <div class="tab-pane fade show" id="incompleteTab" role="tabpanel" aria-labelledby="incomplete-tab">
                <div class="row mb-4 pages-right">
                    <div class="col-md-12">
                        <div class="float-right">
                            <div class="d-flex align-items-center" style="margin: 11px 0 0 -36px !important;">
                                Page: {{$noCompleteTasks->currentPage()}} | Records:&nbsp;
                                @if (Auth::user()->hasPermission('orders.see-all-orders'))
                                    {!! Form::open(['id' => 'recordsPerPageFormOther', 'route' => 'tasks.recordsPerPage', 'method' => 'GET']) !!}
                                        {!! Form::text('recordsPerPageNoComplete', $noCompleteTasks->count(), ['id' => 'recordsPerPageNoComplete', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                                    {!! Form::close() !!}
                                @else
                                    {{$noCompleteTasks->count()}}
                                @endif
                                &nbsp;| Total: {{$noCompleteTasks->total()}}
                                <a href="#" class="btn btn-success ml-3 updateStatusTask" data-status="forapproval">
                                    <i class="fas fa-fw fa-check"></i> For approval
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @unless($noCompleteTasks->isEmpty())
                    <ul class="slds-timeline pt-2">
                        @foreach($noCompleteTasks as $task)
                            @include('tasks.item_tasks')
                        @endforeach
                    </ul>

                    {{$noCompleteTasks->links()}}
                @else
                    <p>No tasks complete.</p>
                @endunless
            </div>
            <div class="tab-pane fade show" id="futureTab" role="tabpanel" aria-labelledby="future-tab">
                <div class="row mb-4 pages-right">
                    <div class="col-md-12">
                        <div class="float-right">
                            <div class="d-flex align-items-center" style="margin: 11px 0 0 -36px !important;">
                                Page: {{$futureTasks->currentPage()}} | Records:&nbsp;
                                @if (Auth::user()->hasPermission('orders.see-all-orders'))
                                    {!! Form::open(['id' => 'recordsPerPageFormOther', 'route' => 'tasks.recordsPerPage', 'method' => 'GET']) !!}
                                        {!! Form::text('recordsPerPageFuture', $futureTasks->count(), ['id' => 'recordsPerPageFuture', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                                    {!! Form::close() !!}
                                @else
                                    {{$futureTasks->count()}}
                                @endif
                                &nbsp;| Total: {{$futureTasks->total()}}
                                <a href="#" class="btn btn-success ml-3 updateStatusTask" data-status="forapproval">
                                    <i class="fas fa-fw fa-check"></i> For approval
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @unless($futureTasks->isEmpty())
                    <ul class="slds-timeline pt-2">
                        @foreach($futureTasks as $task)
                            @include('tasks.item_tasks')
                        @endforeach
                    </ul>

                    {{$futureTasks->links()}}
                @else
                    <p>No tasks complete.</p>
                @endunless
            </div>
        </div>
    </div>
</div>


@include('tasks.filter_today_tasks_modal', ['modalId' => 'filterTodayTasks'])
@include('tasks.filter_modal', ['modalId' => 'filterTasks'])
@include('tasks.not_complete_modal', ['modalId' => 'notCompleteModal'])

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

    $('#selectAllOther').on('change', function () {
        $(":checkbox.selector_other").prop('checked', this.checked);
    });

    $('#selectAll').on('change', function () {
        var selector = $(this).attr("class");
        $("#" + selector + " :checkbox.selector").prop('checked', this.checked);
    });

    $('#markSelectedTasksAsFinishedOrNot').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to mark the selected tasks as finished/unfinished?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('tasks.markSelectedTasksAsFinishedOrNot') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    //Select2 animal selection
    $('[name=filter_animal_id]').on('change', function () {
        var animalId = $(this).val();

        if(animalId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.animal-by-id') }}",
                data: {
                    id: animalId,
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.animal.common_name.trim(), data.animal.id, true, true);
                    // Append it to the select
                    $('[name=filter_animal_id]').append(newOption);
                }
            });
        }
    });

    $('#deleteSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function () {
            ids.push($(this).val());
        });

        if (ids.length == 0)
            alert("You must select items to delete.");
        else if (confirm("Are you sure that you want to delete the selected taks?")) {
            $("#deleteSelectedItems").html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $.ajax({
                type: 'POST',
                url: "{{ route('tasks.deleteItems') }}",
                data: {items: ids},
                success: function (data) {
                    location.reload();
                }
            });
        }
    });

    $('#myTabTasks a[href="'+$('[name=selectedTasksTab]').val()+'"]').tab('show');

    $('#myTabTasks a').on('click', function (e) {
        e.preventDefault();
        var d = $(this);

        $.ajax({
            type:'POST',
            url:"{{ route('tasks.selectedTasksTab') }}",
            data:{
                taskTab: d.attr('href')
            },
            success:function(data){
                d.tab('show');
            }
        });
    })

    $(".nav-link").on("click", function () {
        var selector = $(this).attr("aria-controls");
        $("#selectAll").removeClass();
        $("#selectAll").addClass(selector);
    });

    function showBodyTask(id) {
        var show = $(".show-body-" + id).attr("data-show");
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);

        if(show == "true"){
            $(".task-body-" + id).removeClass("slds-is-open");
            $(".show-body-" + id).attr("data-show", "false");
        }else{
            $(".task-body-" + id).addClass("slds-is-open");
            $(".show-body-" + id).attr("data-show", "true");
        }
    }

    $(".notCompleteModal").on("click", function(){
        var ids = [];
        $(":checked.selector").each(function () {
            ids.push($(this).val());
        });
        if (ids.length == 0)
            alert("You must select items to update not complete.");
        else {
            $("#notCompleteModal").modal("show");
        }
    });

    $('.updateStatusTask').on('click', function () {
        var ids = [];
        var status = $(this).attr("data-status");
        var button = $(this).html();
        var comment = editor.getData();
        $(":checked.selector-notification").each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0)
            alert("You must select items to update.");
        else if (confirm("Are you sure that you want to update status the selected taks?")) {
            $(this).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $(this).attr("disabled", "disabled");
            $.ajax({
                type: 'POST',
                url: "{{ route('tasks.updateStatus') }}",
                data: {items: ids, status: status, comment: comment},
                success: function (data) {
                    location.reload();
                }
            });
        }
    });

    var editor = CKEDITOR.replace('filter_comment', {
        // Define the toolbar groups as it is a more accessible solution.
        toolbarGroups: [{
            "name": "document",
            "groups": ["mode"]
            },
            {
            "name": "basicstyles",
            "groups": ["basicstyles"]
            },
            {
            "name": "links",
            "groups": ["links"]
            },
            {
            "name": "paragraph",
            "groups": ["list", "align"]
            },
            {
            "name": "insert",
            "groups": ["insert"]
            },
            {
            "name": "styles",
            "groups": ["styles"]
            },
            {
            "name": "colors",
            "groups": ["colors"]
            }
        ],
        extraPlugins: 'stylesheetparser',
        height: 200,
        // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
    });


</script>

@endsection
