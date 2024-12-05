@foreach ($row["items"] as $item)
    @if (!empty($item->itemable) && $item->itemable_type == "email")
        <div class="row" style="padding: 0 9px; margin: 0 -19px -13px -13px !important;">
            <div class="col-md-12">
                <tr>
                    <td colspan="2">
                        <ul class="message-list">
                            <li class="@if($item->itemable->is_read == 1) unread @endif li-item-{{ $item->itemable->id }}" style="height: 43px !important; ">
                                <input type="hidden" class="guids_email" value="{{ $item->itemable->guid }}" style="margin: 19px 0 7px 17px; position: absolute;"/>
                                <input type="hidden" class="email_id_item_{{ $item->itemable->id }}" value="{{ $item->id }}" style="margin: 19px 0 7px 17px; position: absolute;"/>

                                <div class="col-mail col-mail-1">
                                    @if($item->itemable->from_email == "braulioser9003@gmail.com")
                                        <p class="title email-item-from-{{ $item->itemable->id }}">From: International Zoo Services (info@zoo-services.com)</p>
                                    @else
                                        @if($item->itemable->is_send == 1)
                                            @if(!empty($item->itemable->contact_id))
                                                <a href="{{  route('contacts.show', [$item->itemable->contact_id]) }}" class="title email-item-from-{{ $item->itemable->id }}" style="color: #13769dd1;">To: {{  $item->itemable->name  }} ({{ $item->itemable->to_email }})</a>
                                            @elseif(!empty($item->itemable->organisation_id))
                                                <a href="{{  route('organisations.show', [$item->itemable->organisation_id]) }}" class="title email-item-from-{{ $item->itemable->id }}" style="color: #13769dd1;">To:{{  $item->itemable->name  }} ({{ $item->itemable->to_email }})</a>
                                            @else
                                                <p class="title email-item-from-{{ $item->itemable->id }}">To: {{  $item->itemable->name  }} ({{ $item->itemable->to_email }})</p>
                                            @endif
                                        @else
                                            @if(!empty($item->itemable->contact_id))
                                                <a href="{{  route('contacts.show', [$item->itemable->contact_id]) }}" class="title email-item-from-{{ $item->itemable->id }}" style="color: #13769dd1;">From: {{  $item->itemable->name  }} ({{ $item->itemable->from_email }})</a>
                                            @elseif(!empty($item->itemable->organisation_id))
                                                <a href="{{  route('organisations.show', [$item->itemable->organisation_id]) }}" class="title email-item-from-{{ $item->itemable->id }}" style="color: #13769dd1;">From: {{  $item->itemable->name  }} ({{ $item->itemable->from_email }})</a>
                                            @else
                                                <p class="title email-item-from-{{ $item->itemable->id }}">From: {{  $item->itemable->name  }} ({{ $item->itemable->from_email }})</p>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                                <div class="email-item-{{ $item->itemable->id }}" onclick="showEmailDashboard('{{ $item->itemable->id }}')" data-show="false" data-id="{{ $item->itemable->id }}" style="cursor: pointer">
                                    <div class="col-mail col-mail-2">
                                        <p  class="subject email-item-subject-{{ $item->itemable->id }}">{{ $item->itemable->subject }}</p>
                                        <div class="date">{{ \Carbon\Carbon::parse($item->itemable->created_at)->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </li>
                            <div class="d-none email-content-{{ $item->itemable->id }} mt-3">
                                <div class="body-email" style="margin: 0 0 0 56px;">
                                    <div class="body_length_{{ $item->itemable->id }}" style="font-family: none !important; line-height: initial !important;">

                                    </div>
                                    <iframe class="body_iframe_{{ $item->itemable->id }}" srcdoc="" width="100%" style='border:0px; max-height: 600px; height: 600px !important; font-family: Nunito, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";'>

                                    </iframe>

                                    <div class="attachments_{{ $item->itemable->id }}">

                                    </div>
                                </div>
                            </div>
                        </ul>
                    </td>
                </tr>
                <hr>
            </div>
        </div>
    @endif
@endforeach



