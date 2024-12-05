
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter tasks" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'tasks.updateStatus', 'method' => 'POST']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Comment  tasks</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-12">
                    {!! Form::label('filter_comment', 'Comment') !!}
                    {!! Form::textarea('filter_comment', null, ['class' => 'form-control', 'rows' => '4']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#" class="btn btn-success ml-3 updateStatusComment" data-status="new">
                <i class="fas fa-fw fa-check"></i> Save
            </a>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

      {!! Form::close() !!}

    </div>
  </div>
</div>
