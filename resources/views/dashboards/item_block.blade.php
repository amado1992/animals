
<div class="col-md-2 mb-2 {{ $dashboard->show_only == 1 && Auth::user()->id != 2 ? "d-none" : "" }}">
    <div class="card show_block_{{ $dashboard->id }} {{ $dashboard->type_style != "Link" ? "show_data" : "" }} {{ $dashboard->name == "Emails" ? "show_email_inbox" : "" }}" style="border-bottom: 6px solid {{ !empty($dashboard->row_color) ? $widget['row_color'][$dashboard->row_color] : "#f7b84bb5" }};" data-id="{{ $dashboard->id }}" data-show="true">
        @if (!empty($total[$dashboard->title]['total']))
           <p class="info-number total">{{ $total[$dashboard->title]['total'] ?? '' }}</p>
        @endif
        <div class="card-header {{ $dashboard->name == "Emails" ? "header_inbox" : "" }}" style="padding: 19px 21px 0 21px; background-color: {{ !empty($dashboard->row_color) ? $widget['row_color'][$dashboard->row_color] : "#f7b84bb5" }}; border-bottom: 0px solid {{ !empty($dashboard->row_color) ? $widget['row_color'][$dashboard->row_color] : "#f7b84bb5" }};">
            <div class="dropdown ml-2">
                <a class="btn btn-sm menu-item-{{ $dashboard->id }}" style="float: right; margin: -9px 0 2px 0;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-dots-horizontal m-0 text-muted h3"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item delete_item" data-id="{{ $dashboard->id }}" href="#">Delete</a>
                    <a class="dropdown-item add-document" data-id="{{ $dashboard->id }}" href="#">Add Document</a>
                </div>
            </div>
            @if ($dashboard->type_style == "Link")
                <h4 class="header-title" style="font-size: 17px; font-weight: 500;"><a href="{{ $dashboard["url"] }}">{{ $dashboard["title"] }}</a></h4>
            @else
                <h4 class="header-title" style="font-size: 17px; font-weight: 500;">{{ strtoupper($dashboard->title) }}</h4>
            @endif
       </div>
        <div class="card-body {{ $dashboard->name == "Emails" ? "body_email_inbox_block d-none" : "" }}" style="padding: 0px 20px 0px 10px; border-top: 11px solid {{ !empty($dashboard->row_color) ? $widget['row_color'][$dashboard->row_color] : "#f7b84bb5" }};">
            @if ($dashboard->name != "Emails")
                <div class="accordion accordion-flush data-card-{{ $dashboard->id }} data_block_hide" id="accordionFlushExample" style="margin: 27px 0 29px 0;">
                    @foreach ($dashboard->dashboards as $key => $row)
                        @if ($row->type_style != "Link")
                           @if (!empty($total[$dashboard->title][$row->filter_data]))
                              <p class="info-number subtotal">{{ $total[$dashboard->title][$row->filter_data] ?? '' }}</p>
                           @endif
                            <div class="accordion-item {{ $row->show_only == 1 && Auth::user()->id != 2 ? "d-none" : "" }}">
                                <h2 class="accordion-header {{ !empty($row->filter_data) ? 'filter_data_action' : '' }}" data-id="{{ $row->id }}" data-show="true" id="flush-heading-{{ $row->id }}">
                                    <button class="btn btn-sm btn-block" type="button" data-toggle="collapse" data-target="#flush-collapse-{{ $row->id }}" aria-expanded="false" aria-controls="flush-collapse-{{ $key }}">
                                        {{ $row["title"] }}
                                    </button>
                                </h2>
                                <div id="flush-collapse-{{ $row->id }}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                                    data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        @if (empty($row->filter_data))
                                            @foreach ($row->items as $item)
                                                @if ((!empty($item->itemable) && $item->itemable_type != "email") && (($dashboard->type_style == "Document" || $dashboard->type_style == "Default") || $item->itemable_type == "general_document" || $item->itemable_type == "attachment"))
                                                    <div class="row mt-3 mb-3" style="padding: 0 14px;">
                                                        <div class="col-md-12">
                                                            @include('dashboards.item_documents')
                                                        </div>
                                                    </div>
                                                    <hr>
                                                @endif
                                            @endforeach
                                            @include('dashboards.item_emails')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @foreach ($dashboard->dashboards as $key => $row)
                        @if ($row->type_style == "Link")
                            <div class="row {{ $row->show_only == 1 && Auth::user()->id != 2 ? "d-none" : "" }}">
                                <div class="col-lg-12">
                                    <a href="{{ $row["url"] }}" class="btn btn-soft-primary btn-rounded waves-effect waves-light">{{ $row["title"] }} <i class="fas fa-external-link-alt" style="font-size: 11px;"></i></a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="spinner_load">

                </div>
                <iframe class="email_inbox" src="{{ route("inbox.emailDashboard") }}?dashboard_show=true" width="100%" style='border:0px; max-height: 750px; height: 750px !important;'>

                </iframe>
            @endif
        </div>
    </div>
    <a href="#" class="close-block d-none close-show-block_{{ $dashboard->id }}" data-id="{{ $dashboard->id }}"><i class="mdi mdi-close-circle-outline m-0 text-muted h3"></i></a>
</div>
