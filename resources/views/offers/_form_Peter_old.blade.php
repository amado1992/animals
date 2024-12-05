
  <div class="form-group">
      {!! Form::label('client_id', 'Client *') !!}
      {!! Form::select('client_id', $customers, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
  </div>

  <div class="form-group">
      {!! Form::label('country', 'Destination country *') !!}
      {!! Form::select('country', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
  </div>

  <div class="form-group">
      {!! Form::label('city', 'Destination city *') !!}
      {!! Form::select('city', $cities, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
  </div>

  <div class="form-group">
      {!! Form::label('requested_at', 'Request date') !!}
      {!! Form::date('requested_at', null, ['class' => 'form-control']) !!}
  </div>

  <div class="form-group">
      {!! Form::label('remarks', 'Open remarks') !!}
      {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => '3']) !!}
  </div>

  <div class="form-group">
      {!! Form::label('interal_remarks', 'Internal remarks') !!}
      {!! Form::textarea('interal_remarks', null, ['class' => 'form-control', 'rows' => '3']) !!}
  </div>

<div class="form-inline mt-5">

  <table class="table" width="100%" cellspacing="0">
    <thead>
      <tr class="table-active">
        <th>QUANTITY</th>
        <th><i class="fas fa-fw fa-paw"></i> SPECIES</th>
        <th></th>
        <th class="text-center">COST PRICES</th>
        <th class="text-center">SALES PRICES</th>
        <th></th>
      </tr>
      <tr>
        <th style="width: 180px">
          <span style="display:inline-block; width: 40px" class="text-center">M</span>
          <span style="display:inline-block; width: 40px" class="text-center">F</span>
          <span style="display:inline-block; width: 40px" class="text-center">U</span>
        </th>
        <th></th>
        <th style="width: 20px"></th>
        <th class="text-right" style="width: 280px">
          <span style="display:inline-block; width: 80px" class="text-center">M</span>
          <span style="display:inline-block; width: 80px" class="text-center">F</span>
          <span style="display:inline-block; width: 80px" class="text-center">U</span>
        </th>
        <th class="text-right" style="width: 280px">
          <span style="display:inline-block; width: 80px" class="text-center">M</span>
          <span style="display:inline-block; width: 80px" class="text-center">F</span>
          <span style="display:inline-block; width: 80px" class="text-center">U</span>
        </th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {!! Form::text('icao_code', null, ['class' => 'form-control-sm form-control', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
        </td>
        <td class="align-middle">Emperor Tamarin <i>(Saguinus imperator)</i></td>
        <td class="align-middle">&euro;</td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          <a href="#" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
            <i class="fas fa-fw fa-minus"></i>
          </a>
        </td>
      </tr>
      <tr>
        <td>
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
        </td>
        <td class="align-middle">Emperor Tamarin <i>(Saguinus imperator)</i></td>
        <td class="align-middle">&euro;</td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          <a href="#" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
            <i class="fas fa-fw fa-minus"></i>
          </a>
        </td>
      </tr>

    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="text-right font-weight-bold">Total</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td colspan="6" class="text-right">
          <a href="#" class="btn btn-sm btn-primary">
            <i class="fas fa-fw fa-plus"></i>
          </a>
        </td>
      </tr>
    </tfoot>
  </table>

  <table class="table" width="100%" cellspacing="0">
    <thead>
      <tr class="table-active">
        <th style="width: 180px">QUANTITY</th>
        <th><i class="fas fa-fw fa-box-open"></i> CRATES</th>
        <th style="width: 20px"></th>
        <th style="width: 280px" class="text-center">COST PRICES</th>
        <th style="width: 280px" class="text-center">SALES PRICES</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
        </td>
        <td class="align-middle">Crate 31 <i>(130 x 85 x 110 cm)</i></td>
        <td class="align-middle">&euro;</td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          <a href="#" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
            <i class="fas fa-fw fa-minus"></i>
          </a>
        </td>
      </tr>

    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="text-right font-weight-bold">Total</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td colspan="6" class="text-right">
          <a href="#" class="btn btn-sm btn-primary">
            <i class="fas fa-fw fa-plus"></i>
          </a>
        </td>
      </tr>
    </tfoot>
  </table>

  <table class="table" width="100%" cellspacing="0">
    <thead>
      <tr class="table-active">
        <th style="width: 180px">QUANTITY</th>
        <th><i class="fas fa-fw fa-luggage-cart"></i> AIRFREIGHT</th>
        <th style="width: 20px"></th>
        <th style="width: 280px" class="text-center">COST PRICES</th>
        <th style="width: 280px" class="text-center">SALES PRICES</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
        </td>
        <td class="align-middle">Crate 31 <i>(130 x 85 x 110 cm)</i></td>
        <td class="align-middle">&euro;</td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          <a href="#" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
            <i class="fas fa-fw fa-minus"></i>
          </a>
        </td>
      </tr>

    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="text-right font-weight-bold">Total</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td colspan="6" class="text-right">
          <a href="#" class="btn btn-sm btn-primary">
            <i class="fas fa-fw fa-plus"></i>
          </a>
        </td>
      </tr>
    </tfoot>
  </table>

  <table class="table" width="100%" cellspacing="0">
    <thead>
      <tr class="table-active">
        <th style="width: 180px">QUANTITY</th>
        <th>OTHER COSTS</th>
        <th style="width: 20px"></th>
        <th style="width: 280px" class="text-center">COST PRICES</th>
        <th style="width: 280px" class="text-center">SALES PRICES</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 3, 'style' => 'width: 40px']) !!}
        </td>
        <td class="align-middle">Crate 31 <i>(130 x 85 x 110 cm)</i></td>
        <td class="align-middle">&euro;</td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          {!! Form::text('icao_code', null, ['class' => 'form-control form-control-sm', 'maxlength' => 5, 'style' => 'width: 80px']) !!}
        </td>
        <td class="text-right">
          <a href="#" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">
            <i class="fas fa-fw fa-minus"></i>
          </a>
        </td>
      </tr>

    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="text-right font-weight-bold">Total</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td class="text-right"> &euro; 19,800.00</td>
        <td colspan="6" class="text-right">
          <a href="#" class="btn btn-sm btn-primary">
            <i class="fas fa-fw fa-plus"></i>
          </a>
        </td>
      </tr>
    </tfoot>
  </table>
</div>

<hr class="mb-4">

@include('components.errorlist')

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('offers.index') }}" class="btn btn-link" type="button">Cancel</a>
