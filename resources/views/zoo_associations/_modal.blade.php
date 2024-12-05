
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter zoo association" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'zoo-associations.filter', 'method' => 'GET']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter zoo association</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="mb-2">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>

        <div class="mb-2">
            {!! Form::label('website', 'Website') !!}
            {!! Form::text('website', null, ['class' => 'form-control']) !!}
        </div>

        <div class="row mb-2">
            <div class="col-md-4">
                {!! Form::label('status', 'Status') !!}
                {!! Form::select('status', ['any' => 'Any', '' => 'Empty', 'Interesting' => 'Interesting', 'Very interesting' => 'Very interesting', 'Not interesting' => 'Not interesting', 'Done' => 'Done', 'Error' => 'Error'], 'any', ['class' => 'form-control']) !!}
            </div>
            <div class="col-md-4">
                {!! Form::label('started_on', 'Started on') !!}
                {!! Form::date('started_date', null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-md-4">
                {!! Form::label('checked_on', 'Checked on') !!}
                {!! Form::date('checked_date', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                {!! Form::label('checked_by', 'Checked by') !!}
                {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
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
