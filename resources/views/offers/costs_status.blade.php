
{!! Form::open(['route' => 'api.update-cost-status', 'method' => 'POST', 'class' => 'costform', 'id' => $id]) !!}
{!! Form::select('costselected', [
      'no_entry' => 'No entry',
      'estimated' => 'Estimated',
      'quotation' => 'Quotation',
      'real_costs' => 'Real costs'
    ], $selected, ['class' => 'mb-1']) !!}
{!! Form::hidden('table', $table) !!}
{!! Form::hidden('cost_id', $cost_id) !!}
{!! Form::hidden('route', route('api.update-cost-status')) !!}
{!! Form::close() !!}