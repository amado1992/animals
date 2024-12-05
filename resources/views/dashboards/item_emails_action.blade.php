@if (!empty($emails))
    @foreach ($emails as $row)
        <div class="row" style="padding: 0 9px; margin: 0 -19px -13px -13px !important;">
            <div class="col-md-12">
                <tr>
                    <td colspan="2">
                        <ul class="message-list">
                            <li class="@if($row->is_read == 1) unread @endif li-item-{{ $row->id }}" style="height: 43px !important; ">
                                <input type="hidden" class="guids_email" value="{{ $row->guid }}" style="margin: 19px 0 7px 17px; position: absolute;"/>

                                <div class="col-mail col-mail-1">
                                    @if($row->is_send == 1)
                                        @if(!empty($row->contact_id))
                                            <a href="{{  route('contacts.show', [$row->contact_id]) }}" class="title email-item-from-{{ $row->id }}" style="color: #13769dd1;">To: {{  $row->name  }} ({{ $row->to_email }})</a>
                                        @elseif(!empty($row->organisation_id))
                                            <a href="{{  route('organisations.show', [$row->organisation_id]) }}" class="title email-item-from-{{ $row->id }}" style="color: #13769dd1;">To:{{  $row->name  }} ({{ $row->to_email }})</a>
                                        @else
                                            <p class="title email-item-from-{{ $row->id }}">To: {{  $row->name  }} ({{ $row->to_email }})</p>
                                        @endif
                                    @else
                                        @if(!empty($row->contact_id))
                                            <a href="{{  route('contacts.show', [$row->contact_id]) }}" class="title email-item-from-{{ $row->id }}" style="color: #13769dd1;">From: {{  $row->name  }} ({{ $row->from_email }})</a>
                                        @elseif(!empty($row->organisation_id))
                                            <a href="{{  route('organisations.show', [$row->organisation_id]) }}" class="title email-item-from-{{ $row->id }}" style="color: #13769dd1;">From: {{  $row->name  }} ({{ $row->from_email }})</a>
                                        @else
                                            <p class="title email-item-from-{{ $row->id }}">From: {{  $row->name  }} ({{ $row->from_email }})</p>
                                        @endif
                                    @endif

                                </div>
                                <div class="email-item-{{ $row->id }}" onclick="showEmailDashboard('{{ $row->id }}')" data-show="false" data-id="{{ $row->id }}" style="cursor: pointer">
                                    <div class="col-mail col-mail-2">
                                        <p  class="subject email-item-subject-{{ $row->id }}">{{ $row->subject }}</p>
                                        <div class="date">{{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </li>
                            <div class="d-none email-content-{{ $row->id }} mt-3">
                                <div class="body-email" style="margin: 0 0 0 56px;">
                                    <div class="body_length_{{ $row->id }}" style="font-family: none !important; line-height: initial !important;">

                                    </div>
                                    <iframe class="body_iframe_{{ $row->id }}" srcdoc="" width="100%" style='border:0px; max-height: 600px; height: 600px !important; font-family: Nunito, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";'>

                                    </iframe>

                                    <div class="attachments_{{ $row->id }}">

                                    </div>
                                </div>
                            </div>
                        </ul>
                    </td>
                </tr>
                <hr>
            </div>
        </div>
    @endforeach
@endif




