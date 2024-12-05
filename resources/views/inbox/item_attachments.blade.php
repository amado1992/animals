<h5 class="mb-3 mt-3">Attachments</h5>

<div class="row" style="width: 100%;">
    @foreach ($data_attachments as $row)
        <div class="col-xl-4 mt-2">
            <div class="card mb-0 shadow-none border">
                <div class="p-2">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-secondary rounded text-light">
                                    <i class="mdi mdi-archive font-18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col ps-0">
                            <a href="#" class="text-muted fw-semibold">{{ $row["name"] }}</a>
                            @if (empty($row["path"]))
                                <p class="mb-0">{{ $row['size'] }}</p>
                            @endif
                        </div>
                        <div class="col-auto">
                            <!-- Button -->
                            @if (!empty($row["path"]))
                                <a href="{{Storage::url($row["path"])}}" class="btn btn-link btn-lg text-muted">
                                    <i class="ri-download-2-line"></i>
                                </a>
                            @else
                                <a href="{{ route("inbox.downloadAttachment", [$email_guid, $row["id"], $to_email]) }}" class="btn btn-link btn-lg text-muted">
                                    <i class="ri-download-2-line"></i>
                                </a>
                            @endif
                        </div>
                        <input type="checkbox" class="selector_attachment" value="{{ $row["id"] }}" data_email_guid="{{ $email_guid }}" data_to_email="{{ $to_email }}" style="margin: 19px 0 7px 17px; position: absolute; right: 3px; top: -16px;"/>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    @endforeach
</div>
