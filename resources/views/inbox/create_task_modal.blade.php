<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter wanted" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {!! Form::open(['route' => 'tasks.store']) !!}

                        @include('inbox.task_form', ['submitButtonText' => 'Create task'])
                        <input type="hidden" name="items_email_task" id="items_email_task">

                    {!! Form::close() !!}
                </div>
        </div>
    </div>
</div>
