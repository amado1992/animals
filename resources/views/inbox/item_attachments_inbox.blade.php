
@foreach ($email->attachments as $row)
    <div class="col-xl-3 mt-2">
        <div class="card mb-0 shadow-none border" style="padding: 0 0 0 25px; border-radius: 50px; height: 39px;">

            <div class="row align-items-center" style="margin-top: -7px;">
                <div class="col-auto">
                    <div class="avatar-sm" style="width: 25px; height: 25px;">
                        <span class="avatar-title bg-secondary rounded text-light" style="border-radius: 50px !important;">
                            <i class="mdi mdi-archive font-18"></i>
                        </span>
                    </div>
                </div>
                <div class='col-7 ps-0'>
                   @php
                     $download = substr($row['name'], 0, 12);
                     $download .= strlen($row['name']) > 13 ? '...' : '';
                   @endphp
                   @if (!empty($row['path']))
                        <a href='{{Storage::url($row['path'])}}' class='text-muted fw-semibold'>
                           {{ $download }}
                        </a>
                    @else
                        <a href='{{ route('inbox.downloadAttachment', [$email['guid'], $row['guid'], $email->to_email]) }}' class='text-muted fw-semibold'>
                            {{ $download }}
                        </a>
                    @endif
                </div>
                <div class='col-2'>
                    @if (!empty($row["path"]))
                        <a href="{{Storage::url($row["path"])}}" class="btn btn-link btn-lg text-muted">
                            <i class="ri-download-2-line"></i>
                        </a>
                    @else
                        <a href="{{ route("inbox.downloadAttachment", [$email["guid"], $row["guid"], $email->to_email]) }}" class="btn btn-link btn-lg text-muted">
                            <i class="ri-download-2-line"></i>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div> <!-- end col -->
@endforeach
