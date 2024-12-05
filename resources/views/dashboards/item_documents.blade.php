<tr>
    <td>
        @if (!empty($item->itemable->type) && ($item->itemable->type == "png" || $item->itemable->type == "JPG" || $item->itemable->type == "image/jpeg"))
        <a href="{{$item->itemable_type == "general_document" ? Storage::url($item->itemable->path) : url('/') . $item->itemable->path}}" class="text-dark"><img src="{{$item->itemable_type == "general_document" ? Storage::url($item->itemable->path) : url('/') . $item->itemable->path}}" class="img-thumbnail "></a>
        @else
            <img src="/img/file-icons/file.svg" height="25" alt="icon" class="me-2">
            <a href="{{$item->itemable_type == "general_document" ? Storage::url($item->itemable->path) : url('/') . $item->itemable->path}}" class="text-dark">{{ $item->itemable->name }}</a>
        @endif

    </td>
    <td>
        @if (Auth::user()->hasRole(['admin']))
            <input type="checkbox" class="delete_item_select" value="{{ $item->id }}" style="float: right;"/>
        @endif
    </td>
</tr>
