@php
// Set this variable, since template is called from a lot of places, that not all contain this ordering
$sortselected = !empty($sortselected) ? $sortselected : '';
@endphp
@if (!empty($filter))
<div class="col-12">
   <div class="row">
      <div class="col-8"></div>
      <div class="col-4">
         {!! Form::open(['route' => $filter, 'method' => 'GET']) !!}
         {!! Form::select('sortselected', [
              'date' => 'Date descending',
              'attachment' => 'Emails with attachment'
            ], $sortselected, ['class' => 'form-control form-control-sm', 'onchange' => 'this.form.submit()']) !!}
         {!! Form::hidden('id', ((isset($offer->id)) ? $offer->id : null), ['class' => 'form-control']) !!}
         {!! Form::close() !!}
      </div>
   </div>
</div>
@endif

<ul class="message-list">
    @foreach ($email_show as $key => $email )
        @php
            $height_li = 108;
            if(!empty($email->body_sumary) || !empty($email->body)){
                $height_li = 130;
            }
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
        <li class="@if($email->is_read == 1) unread @endif li-item-{{ $email->id }}" style="height: {{ $height_li }}px !important; {{ !empty($email->color) ? "background: " . $email->color->color . "26 !important; margin: 2px 0px 0px 0;" : "" }}">
            <div class="col-mail col-mail-1">
                <p class="title email-item-from-{{ $email->id }}" style="left: 10px !important; top: 18px;">To: {{ !empty($email->to_recipients) ? $email->to_recipients : $email->to_email }}</p>
            </div>
            <div class="col-mail col-mail-1">
                @if($email->is_send == 1)
                    @if(!empty($email->contact_id))
                    <a href="{{  route('contacts.show', [$email->contact_id]) }}" style="left: 10px !important;" class="title" style="color: #13769dd1;">From: International Zoo Services ({{ $email->from_email }})</a>
                    @elseif(!empty($email->organisation_id))
                        <a href="{{  route('organisations.show', [$email->organisation_id]) }}" style="left: 10px !important;" class="title" style="color: #13769dd1;">From: International Zoo Services ({{ $email->from_email }})</a>
                    @else
                        <p style="left: 10px !important;" class="title">From: International Zoo Services ({{ $email->from_email }})</p>
                    @endif
                @else
                    @if(!empty($email->contact_id))
                        <a href="{{  route('contacts.show', [$email->contact_id]) }}" style="left: 10px !important;" class="title" style="color: #13769dd1;">From: {{  $email->name  }} ({{ $email->from_email }})</a>
                    @elseif(!empty($email->organisation_id))
                        <a href="{{  route('organisations.show', [$email->organisation_id]) }}" style="left: 10px !important;" class="title" style="color: #13769dd1;">From: {{  $email->name  }} ({{ $email->from_email }})</a>
                    @else
                        <p style="left: 10px !important;" class="title">From: {{  $email->name  }} ({{ $email->from_email }})</p>
                    @endif
                @endif

            </div>
            <div>
                <div class="col-mail col-mail-2" style="top: 24px !important; left: 10px !important;">
                    <p class="subject email-item-{{ $email->id }}" style="cursor: pointer;" onclick="showEmail('{{ $email->id }}')" data-show="false" data-id="{{ $email->id }}">{{ $email->subject }}</p>
                    @if(!empty($email->body_sumary) || !empty($email->body))
                        @php
                            $sumary = new \Html2Text\Html2Text($email->body);
                        @endphp
                        <p class="body-sumary" style="margin: 33px 0 0 0;">{{ $email->body_sumary ?? substr(trim($sumary->getText()), 0, 100) }}...</p>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <small class="float-end" style="position: absolute; {{ !empty($email->body_sumary) || !empty($email->body) ? "top: -27px;" : "top: 35px;" }} left: 14px;">{{ \Carbon\Carbon::parse($email->created_at)->toDayDateTimeString() }}</small>
                            <div style="left: 214px; position: absolute; {{ !empty($email->body_sumary) || !empty($email->body) ? "top: -27px;" : "top: 35px;" }}">
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
                    </div>
                    <div class="date" style="top: 0px !important;">{{ \Carbon\Carbon::parse($email->created_at)->diffForHumans() }}</div>
                </div>
            </div>
            @if (!empty($email->attachments))
                <div class="row" style="position: absolute; top: 115px; left: 10px; width: 100%;">
                    @include('inbox.item_attachments_inbox')
                </div>
            @endif
        </li>
        <div class="d-none email-content-{{ $email->id }} mt-3">
            <div class="body-email" style="width: 100%;">
                <div class="body-email-show_{{ $email->id }}"></div>
                <iframe class="body_iframe_{{ $email->id }}" srcdoc="{{ $email->body }}" width="100%" width="100%" style='border:0px; height:600px; max-height: 600px; font-family: Nunito, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";'>

                </iframe>
                <div class="attachments_{{ $email->id }}">

                </div>
            </div>
            <div class="mt-5 d-none">
                <a href="" class="btn btn-secondary me-2"><i class="mdi mdi-reply me-1"></i> Reply</a>
                <a href="" class="btn btn-light">Forward <i class="mdi mdi-forward ms-1"></i></a>
            </div>
            <hr>
        </div>

    @endforeach
</ul>
<div class="row mb-4 mt-3">
    <div class="col-sm-6">
        @if ($email_show->lastPage() > 1)
            <div>
                <h6 class="font-14 text-body">Showing  {{$email_show->currentPage()}} page of {{$email_show->lastPage()}}</h6>
            </div>
        @endif
    </div>
    <div class="col-sm-6">
        <div class="float-sm-end">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 text-center mt-50 email-show-table-pagination">
                    {{ $email_show->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
