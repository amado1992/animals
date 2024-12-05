<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Packing list" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
        {!! Form::open(['id' => 'packingListDetails', 'route' => 'orders.create_packing_list']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Packing list</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('list_rows', 'List rows', ['class' => 'font-weight-bold']) !!}
                    {!! Form::number('list_rows', null, ['class' => 'form-control', 'required']) !!}
                    {!! Form::hidden('order_id', $order->id, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('awb_number', 'AWB number', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('awb_number', null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('kg_value', 'Kg value', ['class' => 'font-weight-bold']) !!}
                    {!! Form::number('kg_value', null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Create', ['class' => 'btn btn-primary']) !!}
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
