<ul class="message-list">
    <li class="@if($email->is_read == 1) unread @endif li-item-{{ $email->id }}" style="height: 108px !important;">
        <div class="col-mail col-mail-1">
            <p class="title email-item-from-{{ $email->id }}" style="left: 10px !important; top: 18px;">To: {{ !empty($email->to_recipients) ? $email->to_recipients : $email->to_email }}</p>
        </div>
        <div class="col-mail col-mail-1">
            @if($email->is_send == 1)
                @if(!empty($email->contact_id))
                <a href="{{  route('contacts.show', [$email->contact_id]) }}" style="left: 10px !important;" class="title" style="color: #13769dd1;">From: {{  $email->name  }} ({{ $email->to_email }})</a>
                @elseif(!empty($email->organisation_id))
                    <a href="{{  route('organisations.show', [$email->organisation_id]) }}" style="left: 10px !important;" class="title" style="color: #13769dd1;">From: {{  $email->name  }} ({{ $email->to_email }})</a>
                @else
                    <p style="left: 10px !important;" class="title">From: {{  $email->name  }} ({{ $email->to_email }})</p>
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
                <div class="row">
                    <div class="col-md-12">
                        <small class="float-end" style="position: absolute; top: 35px; left: 14px;">{{ \Carbon\Carbon::parse($email->created_at)->toDayDateTimeString() }}</small>
                        <div style="left: 214px; position: absolute; top: 35px;">
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
                                    @elseif ($label->name == "task" && !empty($email->task_id))
                                        <a href="{{  route('tasks.show', [$email->task_id]) }}"><span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }}</span></a>
                                    @else
                                        <span class="badge" style="color: #fff; background: {{ $label->color }};">{{ $label->title }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="date" style="top: 0px !important;">{{ \Carbon\Carbon::parse($email->created_at)->diffForHumans() }}</div>
            </div>
        </div>
    </li>
    <div class="d-none email-content-{{ $email->id }} mt-3">
        <div class="body-email" style="width: 100%;">
            <div class="body-email-show_{{ $email->id }}"></div>
            <iframe class="body_iframe_{{ $email->id }}" srcdoc="{{ $email->body }}" width="100%" style='border:0px; height:600px; max-height: 600px; font-family: Nunito, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";'>

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
</ul>
