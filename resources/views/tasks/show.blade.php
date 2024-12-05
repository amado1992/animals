@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
</ol>
@endsection

@section('main-content')

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header d-inline-flex justify-content-between">
                <h4>Task details</h4>
                <div class="d-flex">
                    <a href="{{ route('tasks.edit', [$task->id]) }}" class="btn btn-dark">
                        <i class="fas fa-fw fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>Assigned by: </b>{{$task->admin->name}}<br>
                            <b>User in charge: </b>{{$task->user->name ?? ""}}<br>
                            <b>Description: </b>{{$task->description}}<br>
                            <b>Action: </b>{{$task->action_field}}<br>
                            <b>Due date: </b>{{ ($task->due_date != null) ? date('d-m-Y', strtotime($task->due_date)) : '' }}
                        </p>
                        <p>
                            <b>Comment: </b><br><span class="text-danger">{!! $task->comment !!}</span><br>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-2">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="email-received-tab" data-toggle="tab" href="#email-received" role="tab" aria-controls="email-received" aria-selected="false">Received emails</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Sent Emails</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show" id="email" role="tabpanel" aria-labelledby="email-tab">
                        @include('inbox.table_show', ['email_show' => $emails])
                    </div>
                    <div class="tab-pane fade show active" id="email-received" role="tabpanel" aria-labelledby="email-received-tab">
                        @include('inbox.table_show', ['email_show' => $emails_received])
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex">
                <h4>Taskable details</h4>
            </div>
            <div class="card-body">
                @if ($task->taskable_type != null)
                    @if ($task->taskable_type === 'offer')
                        <a href="{{ route('offers.show', [$task->taskable_id]) }}" style="color: #4e73df;">{{ Str::upper($task->taskable_type) . ': ' . $task->taskable->full_number }}</a><br>
                    @elseif ($task->taskable_type === 'order')
                        <a href="{{ route('orders.show', [$task->taskable_id]) }}" style="color: #4e73df;">{{ Str::upper($task->taskable_type) . ': ' . $task->taskable->full_number }}</a><br>
                    @endif
                    @if (!empty($task->taskable->client))
                    <b>Client: </b>{{ ($task->taskable->client->full_name) ? $task->taskable->client->full_name : '' }}<br>
                    <b>Phone: </b>{{ ($task->taskable->client->mobile_phone) ? $task->taskable->client->mobile_phone : '' }}<br>
                    <b>Email: </b>{{ ($task->taskable->client->email) ? $task->taskable->client->email : '' }}<br>
                    @endif
                    @if ($task->taskable->supplier !== null)
                    <p>Supplier details</p>
                    <b>Contact: </b>{{ $task->taskable->supplier->full_name }}<br>
                    <b>E-mail: </b>{{ $task->taskable->supplier->email }}<br>
                    <b>Phone: </b>{{ ($task->taskable->supplier->organisation) ? $task->taskable->supplier->organisation->phone : '' }}<br>
                    @endif
                    @if ($task->taskable_type === 'offer')
                        @if ($task->taskable->offer_species->count() == 0)
                            <span style="color: red;">(No species added yet)</span>
                        @else
                            (@foreach ($task->taskable->species_ordered as $species)
                                {{ $species->oursurplus->animal->common_name }}
                                @if ($loop->index == 2) @break @else - @endif
                            @endforeach)
                        @endif
                    @elseif ($task->taskable_type === 'order')
                        @if ($task->taskable->offer->offer_species->count() == 0)
                            <span style="color: red;">(No species added yet)</span>
                        @else
                            (@foreach ($task->taskable->offer->species_ordered as $species)
                                {{ $species->oursurplus->animal->common_name }}
                                @if ($loop->index == 2) @break @else - @endif
                            @endforeach)
                        @endif
                    @endif
                @else
                    GENERAL
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
