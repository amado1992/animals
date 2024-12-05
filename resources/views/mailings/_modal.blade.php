
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter mailings" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'mailings.filter', 'method' => 'GET']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter mailings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="mb-2">
            {!! Form::label('subject', 'Subject', ['class' => 'font-weight-bold']) !!}
            {!! Form::text('subject', null, ['class' => 'form-control']) !!}
        </div>

        <div class="row">
            <div class="col-md-12 text-center">
                {!! Form::label('date_created_period', 'Date created period', ['class' => 'font-weight-bold']) !!}
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6 text-center">
                {!! Form::label('created_at_from', 'Start date', ['class' => 'font-weight-bold']) !!}
                {!! Form::date('filter_created_at_from', null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-md-6 text-center">
                {!! Form::label('created_at_to', 'End date', ['class' => 'font-weight-bold']) !!}
                {!! Form::date('filter_created_at_to', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-center">
                {!! Form::label('date_sent_period', 'Date sent out period', ['class' => 'font-weight-bold']) !!}
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6 text-center">
                {!! Form::label('sent_at_from', 'Start date', ['class' => 'font-weight-bold']) !!}
                {!! Form::date('filter_sent_at_from', null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-md-6 text-center">
                {!! Form::label('sent_at_to', 'End date', ['class' => 'font-weight-bold']) !!}
                {!! Form::date('filter_sent_at_to', null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div>
            {!! Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
            {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
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
