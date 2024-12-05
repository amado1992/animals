@include('components.errorlist')

<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('name', 'Name *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('title', 'Title *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('title', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>
<div class="row mb-3 main-row">
    <div class="col-md-8">
        {!! Form::label('main', 'Main', ['class' => 'font-weight-bold']) !!}
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="customSwitch" name="main"
            @if (!empty($dashboard->main) && $dashboard->main == 1)
                checked
            @endif>
            <label class="custom-control-label" for="customSwitch"></label>
        </div>
    </div>
</div>

<div class="row mb-3 type_style-row">
    <div class="col-md-8">
        {!! Form::label('type_style', 'Type style', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('type_style', $type_style, $dashboard->type_style ?? "Default", ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>
<div class="row mb-3 d-none url-row">
    <div class="col-md-8">
        {!! Form::label('url', 'Url *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('url', $dashboard->url ?? "", ['class' => 'form-control']) !!}
    </div>
</div>
<div class="field-parent d-none">
    <div class="row mb-3">
        <div class="col-md-8">
            {!! Form::label('order', 'Order *', ['class' => 'font-weight-bold']) !!}
            {!! Form::number('order', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-8">
            {!! Form::label('row_color', 'Row Color *', ['class' => 'font-weight-bold']) !!}
            {!! Form::select('row_color', $row_color, $dashboard->row_color ?? null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
        </div>
    </div>

</div>
<div class="row mb-3 filter_data-row">
    <div class="col-md-8">
        {!! Form::label('filter_data', 'Filter Data *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('filter_data', $filter_data, $dashboard->filter_data ?? null, ['class' => 'form-control', 'placeholder' => '- Filter Data -']) !!}
    </div>
</div>
<div class="row mb-3 parent-row">
    <div class="col-md-8">
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-secondary dropdown-toggle button-parent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                -- Select the block to display * --
            </button>
            <div class="dropdown-menu parent-dropdown">
                {!! $parents  !!}
            </div>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('show_only', 'Show only for John:',['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::radio('show_only', 0, ["checked" => "checked"]) !!}
        {!! Form::label('no', 'No', ['class' => 'mr-2']) !!}
        {!! Form::radio('show_only', 1) !!}
        {!! Form::label('yes', 'Yes') !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if (isset($origin))
    <a href="{{ route('origins.show', $origin) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('origins.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">
    if($("#type_style").val() == "Link"){
        $(".url").removeClass("d-none");
        $(".url-row").removeClass("d-none");
    }
    var type = "{{ $type ?? "" }}";
    if(type != ""){
        if(type == "directory"){
            $(".main-row").addClass("d-none");
            $(".type_style-row").addClass("d-none");
            $(".filter_data-row").addClass("d-none");
        }else if(type == "link"){
            $(".main-row").addClass("d-none");
            $("#type_style option[value='Link']").attr("selected",true);
            $(".type_style-row").addClass("d-none");
            $(".url-row").removeClass("d-none");
            $(".filter_data-row").addClass("d-none");
        }else if(type == "main"){
            $(".main-row").addClass("d-none");
            $("#customSwitch").prop('checked',true);
            $(".type_style-row").addClass("d-none");
            $(".filter_data-row").addClass("d-none");
            $(".field-parent").removeClass("d-none");
            $(".parent-row").addClass("d-none");
        }else if(type == "mainLink"){
            $(".main-row").addClass("d-none");
            $("#type_style option[value='Link']").attr("selected",true);
            $(".url-row").removeClass("d-none");
            $("#customSwitch").prop('checked',true);
            $(".type_style-row").addClass("d-none");
            $(".filter_data-row").addClass("d-none");
            $(".field-parent").removeClass("d-none");
            $(".parent-row").addClass("d-none");
        }else if(type == "dataList"){
            $(".main-row").addClass("d-none");
            $(".type_style-row").addClass("d-none");
        }
    }
</script>

@endsection
