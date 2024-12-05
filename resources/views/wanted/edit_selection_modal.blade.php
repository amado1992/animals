<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit selected wanteds" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        {!! Form::open(['route' => 'wanted.editSelectedRecords']) !!}

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit selected wanteds</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    {!! Form::label('origin', 'Origin', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_origin', $origin, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('age_group', 'Age group', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_selection_age_group', $ageGroup, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            @if (!Auth::user()->hasRole('office'))
               <div class="row mb-2">
                  <div class="col-md-12">
                     {!! Form::label('add_to_wanted_lists', 'Add to wanted lists: ', ['class' => 'font-weight-bold']) !!}
                     {!! Form::select('selectionAddToWantedLists', $wantedLists->pluck('name', 'id'), null, ['id' => 'selectionAddToWantedLists', 'class' => 'form-control', 'multiple']) !!}
                 </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::label('remove_from_wanted_lists', 'Remove from wanted lists: ', ['class' => 'font-weight-bold']) !!}
                        {!! Form::select('selectionRemoveFromWantedLists', $wantedLists->pluck('name', 'id'), null, ['id' => 'selectionRemoveFromWantedLists', 'class' => 'form-control', 'multiple']) !!}
                    </div>
                </div>
            @endif
        </div>

        <div class="modal-footer">
          {!! Form::submit('Save wanteds', ['id' => 'sendEditSelectionForm', 'class' => 'btn btn-primary']) !!}
          <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
</div>
