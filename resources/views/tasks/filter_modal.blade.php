
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter tasks" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'tasks.filter', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter tasks</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-9">
                    {!! Form::label('filter_description', 'Description') !!}
                    {!! Form::text('filter_description', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_action', 'Action') !!}
                    {!! Form::select('filter_action', $actions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('filter_due_date', 'Action ready on') !!}
                    {!! Form::date('filter_due_date', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_user', 'User in charge') !!}
                    {!! Form::select('filter_user', $users, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_created_by', 'Created by') !!}
                    {!! Form::select('filter_created_by', $users, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-9">
                    {!! Form::label('filter_offer_order_species', 'Offer/Order species') !!}
                    <select class="animal-select2 form-control" type="default" style="width: 100%" name="filter_animal_id"></select>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-6">
                    {!! Form::checkbox('filter_finished_tasks', null) !!}
                    {!! Form::label('filter_finished_tasks', 'Finished') !!}
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
