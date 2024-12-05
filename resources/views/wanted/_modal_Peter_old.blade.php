<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Add new wanted" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      {!! Form::open(['route' => 'wanted.store']) !!}

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add new wanted</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">


        <div class="mb-3">
		    {!! Form::label('animal', 'Animal *') !!}
		    {!! Form::text('animal', null, ['class' => 'form-control autocomplete']) !!}
		</div>

		<table class="table table-striped">

		  <thead>
		    <tr>
		        <th></th>
		        <th>Male</th>
		        <th>Feale</th>
		        <th>Unknown</th>
		        <th>Pair</th>
		    </tr>
		  </thead>
		  <tbody>
		    <tr>
		        <td><b>Amount</b></td>
		        <td>
		            <div class="input-group input-group-sm">
		                {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		        <td>
		            <div class="input-group input-group-sm">
		                {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		        <td>
		            <div class="input-group input-group-sm">
		                {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		        <td>
		            <div class="input-group input-group-sm">
		                {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		    </tr>
		    <tr>
		        <td style="width: 80px"><b>Est. Cost</b></td>
		        <td>
		            <div class="input-group input-group-sm">
		              {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		        <td>
		            <div class="input-group input-group-sm">
		              {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		        <td>
		            <div class="input-group input-group-sm">
		              {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		        <td>
		            <div class="input-group input-group-sm">
		              {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		            </div>
		        </td>
		    </tr>
		    
		  </tbody>
		    
		</table>

		<div class="row">

		    <div class="col">

		        <div class="mb-3">
		            {!! Form::label('origin', 'Origin') !!}
		            {!! Form::select('origin', $origins, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
		        </div>

		    </div>

		    <div class="col">

		        <div class="mb-3">
		            {!! Form::label('age', 'Age group') !!}
		            {!! Form::select('age', $agegroups, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
		        </div>

		    </div>

		</div>

		<div class="mb-3">
		    {!! Form::label('continent', 'Continent') !!}
		    {!! Form::select('continent', $continents, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
		</div>

		<div class="mb-3">
		    {!! Form::label('supplier', 'Supplier') !!}
		    {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
		</div>


      </div>
      <div class="modal-footer">

        {!! Form::submit('Save wanted', ['class' => 'btn btn-primary']) !!}
        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</a>

      </div>

      {!! Form::close() !!}

    </div>
  </div>
</div>
