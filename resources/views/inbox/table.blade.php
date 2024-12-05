<div class="row">
    <div class="col-md-1" style="margin-top: 4px">
        <input type="checkbox" id="selectAll" name="selectAll" />&nbsp;Select all
        <input type="hidden" id="countInboxVisible" value="{{ ($emails->count() > 0) ? $emails->count() : 0 }}" />
        <input type="hidden" id="general_signature" value="{{ $general_signature ?? ''}}" />
    </div>
    <div class="col-md-5" style="margin-top: 4px">
        <div class='row'>
            <div class="col-md-3" style="text-align: right">Filtered on</div>
            <div class='col-md-9' style="margin-top: 1px;padding-left: 0">
        @foreach ($filterData as $key => $value)
            <a href="{{ route('inbox.removeFromInboxSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1" style="margin-top: -5px"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
        @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-3" style="margin-top: 4px">
       @php
         if (empty($sortselected)) $sortselected = '';
       @endphp
      {!! Form::open(['route' => 'inbox.filterInbox', 'method' => 'GET']) !!}
      <!--filterInbox -->

         <div class='row'>
            <div class="col-md-3" style="text-align: right">
               Sort by
            </div>
            <div class='col-md-8' style="margin-top: -5px;padding-left: 0">
            {!! Form::select('sortselected', [
              'date' => 'Date descending',
              'unread' => 'Emails unread',
              'attachment' => 'Emails with attachment'
            ], $sortselected, ['class' => 'form-control form-control-sm', 'onchange' => 'this.form.submit()']) !!}
            </div>
         </div>
       {!! Form::close() !!}
    </div>
    <div class="col-md-3" style="margin: 0;padding-top: 0">
        <div class="d-flex align-items-center float-right">
            Page: {{$emails->currentPage()}} | Records:&nbsp;
            @if (Auth::user()->hasPermission('orders.see-all-orders'))
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'inbox.recordsPerPage', 'method' => 'GET']) !!}
                    {!! Form::text('recordsPerPage', $emails->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
            @else
                {{$emails->count()}}
            @endif
            &nbsp;| Total: {{$emails->total()}}
        </div>
    </div>
</div>
<ul class="message-list">
@if(!empty($emails))
    @foreach ($emails as $key => $email )
        @php
            $height_li = 140;
            if(count($email->attachments) > 0){
                if(count($email->attachments) > 4){
                    $loop = (int)number_format((count($email->attachments) / 4), 0);
                    for ($i=0; $i < $loop; $i++) {
                        $height_li = $height_li + 50;
                    }
                }else{
                    $height_li = $height_li + 50;
                }
            }
        @endphp
        <li class="@if($email->is_read == 1) unread @endif li-item-{{ $email->id }}" style="height: {{ $height_li }}px !important; {{ !empty($email->color) ? "background: " . $email->color->color . "26 !important; margin: 2px 0px 0px 0;" : "" }} margin: 0 20px 0 0;">
            <input type="checkbox" class="selector" value="{{ $email->id }}" style="margin: 19px 0 7px 17px; position: absolute;"/>
            <input type="hidden" class="guids_email" value="{{ $email->guid }}" style="margin: 19px 0 7px 17px; position: absolute;"/>
            <div class="col-mail col-mail-1">
               <p class="title email-item-from-{{ $email->id }}" style="top: 18px;">
                  <span class="emailref">To:</span>{{ !empty($email->to_recipients) ? $email->to_recipients : $email->to_email }}
@if(!empty($email->cc_email))&nbsp;&nbsp;<span class="emailref">Cc:</span>{{ $email->cc_email }}@endif
                </p>
            </div>
            <div class="col-mail col-mail-1">
                @if($email->is_send == 1)
                    @if(!empty($email->contact_id))
                        <a href="{{  route('contacts.show', [$email->contact_id]) }}" class="title email-item-from-{{ $email->id }} contact"><span class="emailref">From:</span>{{ $email->from_email }}</a>
                    @elseif(!empty($email->organisation_id))
                        <a href="{{  route('organisations.show', [$email->organisation_id]) }}" class="title email-item-from-{{ $email->id }} contact"><span class="emailref">From:</span>{{ $email->from_email }}</a>
                    @else
                        <p class="title email-item-from-{{ $email->id }}"><span class="emailref">From:</span>{{ $email->from_email }}</p>
                    @endif
                @else
                    @if(!empty($email->contact_id))
                        <a href="{{  route('contacts.show', [$email->contact_id]) }}" class="title email-item-from-{{ $email->id }} contact"><span class="emailref">From:</span>{{  $email->name  }} ({{ $email->from_email }})</a>
                    @elseif(!empty($email->organisation_id))
                        <a href="{{  route('organisations.show', [$email->organisation_id]) }}" class="title email-item-from-{{ $email->id }} contact"><span class="emailref">From:</span>{{  $email->name  }} ({{ $email->from_email }})</a>
                    @else
                        <p class="title email-item-from-{{ $email->id }}"><span class="emailref">From:</span>{{  $email->name  }} ({{ $email->from_email }})</p>
                    @endif
                @endif

            </div>
            @if (!empty($email->is_draft) && $email->is_draft == 1)
                <div class="email-item-{{ $email->id }}" onclick="editDraftEmail('{{ $email->id }}')" data-show="false" data-id="{{ $email->id }}" style="cursor: pointer">
                    <div class="col-mail col-mail-2">
                        <p  class="subject email-item-subject-{{ $email->id }}">{{ $email->subject }}</p>
                        <small class="float-end email-item-date-{{ $email->id }}" style="position: absolute; {{ !empty($email->body_sumary) || !empty($email->body) ? 'top: 52px;' : 'top: 35px;' }}">{{ \Carbon\Carbon::parse($email->created_at)->toDayDateTimeString() }}</small>
                        <div class="date">{{ \Carbon\Carbon::parse($email->created_at)->diffForHumans() }}</div>
                    </div>
                </div>
            @else
                <div class="email-item-{{ $email->id }}" onclick="showEmail('{{ $email->id }}')" data-show="false" data-id="{{ $email->id }}" style="cursor: pointer">
                    <div class="col-mail col-mail-2">
                        <p  class="subject email-item-subject-{{ $email->id }}">{{ $email->subject }}</p>
                        @if(!empty($email->body_sumary) || !empty($email->body))
                            @php
                                $sumary = new \Html2Text\Html2Text($email->body);
                            @endphp
                            <p class="body-sumary" style="margin: 33px 0 0 0;">{{ $email->body_sumary ?? substr(trim($sumary->getText()), 0, 100) }}...</p>
                        @endif
                        <small class="float-end email-item-date-{{ $email->id }}" style="position: absolute; {{ !empty($email->body_sumary) || !empty($email->body) ? 'top: 52px;' : 'top: 35px;' }} ">{{ \Carbon\Carbon::parse($email->created_at)->toDayDateTimeString() }}</small>
                        <div class="date">{{ \Carbon\Carbon::parse($email->created_at)->diffForHumans() }}</div>
                    </div>
                </div>
            @endif

            <div class="row" style="position: absolute; {{ !empty($email->body_sumary) || !empty($email->body) ? 'top: 94px;' : 'top: 81px;' }} left: 60px;">
                <div class="col-md-12">
                    @if (!empty($email->labels))
                        @foreach ($email->labels as $label )
                            @if ($label->name == "offer" && !empty($email->offer_id))
                                <a href="{{  route('offers.show', [$email->offer_id]) }}"><span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }} {{ $email->offer->full_number }}</span></a>
                            @elseif ($label->name == "order" && !empty($email->order_id))
                                <a href="{{  route('orders.show', [$email->order_id]) }}"><span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }} {{ $email->order->full_number }}</span></a>
                            @elseif ($label->name == "surplus" && !empty($email->surplu_id))
                                <a href="{{  route('surplus.show', [$email->surplu_id]) }}"><span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }} ({{ $email->surplu->animal->common_name ?? "" }})</span></a>
                            @elseif ($label->name == "wanted" && !empty($email->wanted_id))
                                <a href="{{  route('wanted.show', [$email->wanted_id]) }}"><span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }} ({{ $email->wanted->animal->common_name ?? "" }})</span></a>
                            @elseif ($label->name == "task")
                                @if (!empty($email->tasks))
                                    @foreach ($email->tasks as $task)
                                        <a href="{{  route('tasks.show', [$task->id]) }}"><span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }} @if(!empty($task->user)) to {{ $task->user->name }} {{ $task->user->last_name }} @endif</span></a>
                                    @endforeach
                                @endif
                                @if (!empty($email->task_id))
                                    <a href="{{  route('tasks.show', [$email->task_id]) }}"><span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }} @if(!empty($email->task->user)) to {{ $email->task->user->name }} {{ $email->task->user->last_name }} @endif</span></a>
                                @endif
                            @else
                                <span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }}</span>
                            @endif
                        @endforeach
                    @endif
                    @if (!empty($email->remind_email_id) && !empty($email->remind_due_date))
                        <span class="badge" style="color: #000; font-size: 13px;">Reminder of ago {{ \Carbon\Carbon::parse($email->remind_due_date)->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            @if (!empty($email->attachments))
                <div class="row" style="position: absolute; {{ !empty($email->labels) ? "top: 127px;" : "top: 111px;" }} left: 54px; padding: 0 61px 0 0; width: 100%;">
                    @include('inbox.item_attachments_inbox')
                </div>
            @endif
            @if(!empty($email->contact_id) || !empty($email->organisation_id))
                <button class="btn btn-primary list_contact_assing" data-id="{{ $email->id }}" style="background: #323a46 !important; margin: 70px 0 0 9px; color: #fff; padding: 3px 6px 3px 6px !important;"><i class="mdi mdi-account-check me-2"></i></button>
            @endif

            @foreach ($email->labels as $label )
                @if ($label->name == "offer" && !empty($email->offer_id))
                    <button class="btn btn-primary archive_emails_{{ $email->id }}" onclick="archiveEmailsProject('{{ $email->id }}')" data-id="{{ $email->id }}" style="margin: 10px -30px 0 0; color: #fff; padding: 1px 5px 2px 5px !important; float: right;"><i class="mdi mdi-archive" style="font-size: 12px;"></i></button>
                @endif
            @endforeach
        </li>
        <div class="d-none email-content-{{ $email->id }} mt-3">
            @if($email->is_send != 1)
            <div class="mt-1 mb-4" style="margin: 0 0 0 54px;">
                <a href="#" class="btn btn-secondary me-2 reply-btn-{{ $email->id }}" onclick="replyBtn('{{ $email->id }}')" data-url="{{ route('inbox.sendEmail') }}" data-email="{{ $email->from_email }}" data-id="{{ $email->id }}"><i class="mdi mdi-reply me-1"></i> Reply</a>
                <a href="#" class="btn btn-secondary me-2 forward-btn-{{ $email->id }}" onclick="forwardBtn('{{ $email->id }}')" data-url="{{ route('inbox.forwardEmail') }}" data-email="{{ $email->from_email }}" data-id="{{ $email->id }}">Forward <i class="mdi mdi-forward ms-1"></i></a>
                <a href="#" class="btn btn-danger me-2 create_contact_email-{{ $email->id }}" onclick="createContactEmail('{{ $email->id }}')" data-id="{{ $email->id }}"><i class="fas fa-fw fa-address-card"></i> Cr Contact</a>
                <a href="#" class="btn btn-primary create_institution_email_{{ $email->id }}" onclick="createInstitutionEmail('{{ $email->id }}')" data-id="{{ $email->id }}"><i class="fas fa-fw fa-building"></i> Cr Institution</a>
                <a href="#" class="btn btn-primary create_task_email_{{ $email->id }}" onclick="createTaskEmail('{{ $email->id }}')" style="background: #3bafda !important;" data-id="{{ $email->id }}" data-email="{{ !empty($email->contact_id) ? $email->from_email : "false" }}" data-taskType="{{ !empty($email->order_id) ? 'order' : '' }}{{ !empty($email->offer_id) && empty($email->order_id) ? 'offer' : '' }}" data-offerOrderId="{{ !empty($email->order_id) ? $email->order_id : '' }} {{ !empty($email->offer_id) && empty($email->order_id) ? $email->offer_id : '' }}" data-textTableType="{{ !empty($email->order_id) ? 'Order : ' . $email->order->full_number : '' }} {{ !empty($email->offer_id) && empty($email->order_id) ? 'Offer : ' . $email->offer->full_number : '' }}"><i class="fas fa-fw fa-tasks"></i> Cr Task</a>
                <a href="#" class="btn btn-primary assing_offer_email_{{ $email->id }}" onclick="assingOfferEmail('{{ $email->id }}')" style="background: #323a46 !important;" data-id="{{ $email->id }}" data-email="{{ $email->from_email }}"><i class="fas fa-fw fa-signature"></i> Store in Offer</a>
                <a href="#" class="btn btn-primary assing_order_email_{{ $email->id }}" onclick="assingOrderEmail('{{ $email->id }}')" style="background: #323a46 !important;" data-id="{{ $email->id }}" data-email="{{ $email->from_email }}"><i class="fas fa-fw fa-suitcase"></i> Store in Order</a>
                <a href="#" class="btn btn-primary assing_surplu_email_{{ $email->id }}" onclick="assingSurpluEmail('{{ $email->id }}')" style="background: #323a46 !important;" data-id="{{ $email->id }}" data-email="{{ $email->from_email }}"><i class="fas fa-fw fa-store"></i> Store in Surplu</a>
                <a href="#" class="btn btn-primary assing_wanted_email_{{ $email->id }}" onclick="assingWantedEmail('{{ $email->id }}')" style="background: #323a46 !important;" data-id="{{ $email->id }}" data-email="{{ $email->from_email }}"><i class="fas fa-fw fa-hand-paper"></i> Store in Wanted</a>
                <a href="#" class="btn btn-primary archive_emails_{{ $email->id }}" onclick="archiveEmails('{{ $email->id }}')" data-id="{{ $email->id }}"><i class="mdi mdi-archive font-18"> Archive</i></a>
            </div>
            @endif
            <div class="body-email" style="margin: 0 0 0 56px;">
                <div class="body_length_{{ $email->id }}" style="font-family: none !important; line-height: initial !important;">

                </div>
                <iframe class="body_iframe_{{ $email->id }}" srcdoc="" width="100%" style='border:0px; max-height: 600px; font-family: Nunito, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";'>

                </iframe>

                <div class="attachments_{{ $email->id }}">

                </div>
            </div>


            <!-- end row-->


            <div class="mt-5">
                <a href="#" class="btn btn-secondary me-2 reply-btn-{{ $email->id }}" onclick="replyBtn('{{ $email->id }}')" data-url="{{ route('inbox.sendEmail') }}" data-email="{{ $email->from_email }}" data-id="{{ $email->id }}"><i class="mdi mdi-reply me-1"></i> Reply</a>
                <a href="#" class="btn btn-secondary me-2 forward-btn-{{ $email->id }}" onclick="forwardBtn('{{ $email->id }}')" data-url="{{ route('inbox.forwardEmail') }}" data-email="{{ $email->from_email }}" data-id="{{ $email->id }}">Forward <i class="mdi mdi-forward ms-1"></i></a>
            </div>
            <hr>
        </div>
    @endforeach
@endif
</ul>
