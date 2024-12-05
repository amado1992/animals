
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter countries" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'countries.filter', 'method' => 'GET']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter countries</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row mb-2">
            <div class="col-md-6">
                {!! Form::label('filter_name', 'Name') !!}
                {!! Form::text('filter_name', null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-md-6">
                {!! Form::label('filter_region', 'Region') !!}
                {!! Form::select('filter_region', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                {!! Form::label('filter_language', 'Language') !!}
                {!! Form::select('filter_language', ['EN' => 'English', 'ES' => 'Spanish'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
            </div>
        </div>
      </div>

      <div class="modal-footer">
        {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
      </div>

      {!! Form::close() !!}

    </div>
  </div>
</div>
