<li>
    <div class="slds-timeline__item_expandable {{ $task->action == "call" ? "slds-timeline__item_call" : "" }} {{ $task->action == "email" ? "slds-timeline__item_email" : "" }} {{ $task->action == "remind" ? "slds-timeline__item_task" : "" }} {{ $task->action == "bo" ? "slds-timeline__item_event" : "" }} {{ $task->action == "reminder" ? "slds-timeline__item_reminder" : "" }} task-body-{{ $task->id }}">
        <span class="slds-assistive-text">task</span>
        <div class="slds-media">
        <div class="slds-media__figure">
            <button class="slds-button slds-button_icon show-body-{{ $task->id }}" onclick="showBodyTask('{{ $task->id }}')" data-show="false" aria-controls="task-item-expanded-77" aria-expanded="true">
                <i class="fa fa-chevron-right slds-button__icon slds-timeline__details-action-icon" aria-hidden="true"></i>
            </button>
            <div class="slds-icon_container {{ $task->action == "call" ? "slds-icon-standard-log-a-call" : "" }} {{ $task->action == "email" ? "slds-icon-standard-email" : "" }} {{ $task->action == "remind" ? "slds-icon-standard-task" : "" }} {{ $task->action == "bo" ? "slds-icon-standard-events" : "" }} {{ $task->action == "reminder" ? "slds-icon-standard-reminder" : "" }} slds-timeline__icon" title="task">
                @if ($task->action == "remind")
                    <img class="slds-icon slds-icon_small" src="/img/whatsapp.png" style="width: 28px; height: 28px;">
                @else
                    <i class="fa fa-fw {{ $task->action == "call" ? "fa-phone" : "" }} {{ $task->action == "email" ? "fa-envelope" : "" }} {{ $task->action == "bo" ? "fa-edit" : "" }} {{ $task->action == "reminder" ? "fa-clock" : "" }} slds-icon slds-icon_small text-white" aria-hidden="true" style="margin: 8px 2px -3px 2px;"></i>
                @endif
            </div>
        </div>
        <div class="slds-media__body">
            @if (!empty($task->never) && $task->never == 1 && $task->due_date >= date('Y-m-d H:s:i', strtotime('now')))

            @else
                <input type="checkbox" name="options" id="checkbox-unique-id-20" class="selector-notification mr-1" value="{{ $task->id }}" style="float: left; margin: 8px 0 0 0;" />
            @endif
            <div class="slds-grid slds-grid_align-spread slds-timeline__trigger ">
                <div class="slds-grid slds-grid_vertical-align-center slds-truncate_container_75 slds-no-space">
                    <h3 class="slds-truncate" title="{{ $task->description }}">
                        <a href="{{ route('tasks.show', [$task->id]) }}" title="Show">
                            <strong>{{ substr(ucfirst($task->description), 0, 150) }}@if(strlen($task->description) >= 150)...@endif</strong>
                        </a>
                    </h3>
                </div>
                <div class="slds-timeline__actions slds-timeline__actions_inline">
                    @if (!empty($task->never) && $task->never == 1 && $task->due_date >= date('Y-m-d H:s:i', strtotime('now')))
                        <span class="badge mr-2" style="color: #fff; background: #f1556c;">Never should be done by:</span>
                    @endif
                    <p class="slds-timeline__date">{{ date('h:i a | d/m/Y', strtotime($task->due_date))}}</p>
                </div>
            </div>
            @if(!empty($task->comment))<div class="text-danger" style="margin: 7px 0 0 0px;">{!! substr($task->comment, 0, 150) !!}@if(strlen($task->comment) >= 150)...@endif</div>@endif
            <p class="slds-m-horizontal_xx-small">
                <strong>Created By: </strong>{{ !empty($task->admin) ? $task->admin->name : "" }}
            </p>
            <p class="slds-m-horizontal_xx-small">
                <strong>In charge: </strong>{{ !empty($task->user) ? $task->user->name : "" }} (
                @if ($task->action == "email")
                    Send email
                @elseif ($task->action == "remind")
                    Send Whatsapp
                @elseif ($task->action == "bo")
                    Work in BO
                @else
                    {{ ucfirst($task->action) }}
                @endif
                )
            </p>
            <article class="slds-box slds-timeline__item_details slds-theme_shade slds-m-top_x-small slds-m-horizontal_xx-small slds-p-around_medium" id="task-item-expanded-77" aria-hidden="false">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <div>
                            <span class="slds-text-title">In charge</span>
                            <p class="slds-p-top_x-small">{{ !empty($task->user) ? $task->user->name . " " . $task->user->last_name : "" }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <span class="slds-text-title">Type</span>
                            <p class="slds-p-top_x-small">
                                @if ($task->taskable_type != null && !empty($task->taskable))
                                    @if ($task->taskable_type === 'offer')
                                        <a href="{{ route('offers.show', [$task->taskable_id]) }}">{{ Str::upper($task->taskable_type) . ': ' . !empty($task->taskable["full_number"]) ?  $task->taskable["full_number"] : "" }}</a><br>
                                        {{ $task->taskable->client->full_name ?? "" }}
                                        @if ($task->taskable->offer_species->count() == 0)
                                            <span style="color: red;">(No species added yet)</span>
                                        @else
                                            (@foreach ($task->taskable->species_ordered as $species)
                                                {{ $species->oursurplus->animal->common_name }}
                                                @if ($loop->index == 2) @break @else - @endif
                                            @endforeach)
                                        @endif
                                    @elseif ($task->taskable_type === 'order')
                                        <a href="{{ route('orders.show', [$task->taskable_id]) }}">{{ Str::upper($task->taskable_type) . ': ' . !empty($task->taskable["full_number"]) ?  $task->taskable["full_number"] : "" }}</a><br>
                                        {{ $task->taskable->client->full_name }}
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
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <span class="slds-text-title">Description</span>
                            <p class="slds-p-top_x-small">{{ $task->description }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <span class="slds-text-title">Created by</span>
                            <p class="slds-p-top_x-small">{{ ($task->admin != null) ? $task->admin->name : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div>
                            <span class="slds-text-title">Comment</span>
                            <p class="slds-p-top_x-small text-danger">{!! $task->comment !!}</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div>
                            <span class="slds-text-title">Emails</span>
                        </div>
                    </div>
                </div>
                @if (!empty($task->emails))
                    <div class="card shadow mb-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        <span class="slds-text-title">Received emails</span>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        @foreach ($task->emails as $key => $email )
                                            @if ($email->is_send == 0)
                                                @include('tasks.table_show', ['email' => $email])
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        <span class="slds-text-title">Sent Emails</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    @foreach ($task->emails as $key => $email )
                                        @if ($email->is_send == 1)
                                            @include('tasks.table_show', ['email' => $email])
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </article>
        </div>
        </div>
    </div>
</li>
