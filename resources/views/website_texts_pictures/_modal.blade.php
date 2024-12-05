
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter website text" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'website-texts.filter', 'method' => 'GET']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter website text</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="mb-2">
                {!! Form::label('remarks', 'Remarks') !!}
                {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
            </div>

            <div class="mb-2">
                {!! Form::label('english_text', 'English text') !!}
                {!! Form::text('english_text', null, ['class' => 'form-control']) !!}
            </div>

            <div class="mb-2">
                {!! Form::label('spanish_text', 'Spanish text') !!}
                {!! Form::text('spanish_text', null, ['class' => 'form-control']) !!}
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
