@foreach( $list as $item )
    @if( $item->under->isEmpty() )
        <span class="list-group-item" style="padding-left:{{ $depth * 25 }}px">{{ $item->common_name }}</span>

    @else
        <a href="#item-{{ $item->id }}" class="list-group-item" data-toggle="collapse" style="padding-left: {{ $depth * 25 }}px">
          <i class="glyphicon glyphicon-chevron-right"></i>{{ $item->common_name }}
        </a>
        <div class="list-group" id="item-{{ $item->id }}">

          @include('components.nestedList', ['list' => $item->under, 'depth' => $depth + 1])

        </div>
    @endif
@endforeach
