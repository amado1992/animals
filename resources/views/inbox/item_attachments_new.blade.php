<div class="row align-items-center item-attachment-{{ $data_attachments["id"] }}">
    <div class="col-auto">
        <div class="avatar-sm">
            <span class="avatar-title bg-secondary rounded text-light">
                <i class="mdi mdi-archive font-18"></i>
            </span>
        </div>
    </div>
    <div class="col ps-0">
        <a href="{{Storage::url($data_attachments["path"])}}" class="text-muted fw-bold" data-dz-name>{{ $data_attachments["name"] }}</a>
    </div>
    <div class="col-auto">
        <!-- Button -->
        <a href="#" class="btn btn-link btn-lg text-muted delete_attachment-{{ $data_attachments["id"] }}" onclick="delete_attachment('{{ $data_attachments['id'] }}')" data-id="{{ $data_attachments["id"] }}" data-dz-remove>
            <i class="fe-x"></i>
        </a>
    </div>
</div>
