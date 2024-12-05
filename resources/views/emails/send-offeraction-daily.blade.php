<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
		  <td class="container">
          <div class="content">
		  <p>There are offers acttion to remind, see below:</p>
		<table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 100px; text-align: left;">Project No.</th>
                    <th style="width: 200px; text-align: left;">Institution Name</th>
                    <th style="width: 100px; text-align: left;">Animal Quantities</th>
                    <th style="width: 250px; text-align: left;">Animal Name</th>
                    <th style="width: 250px; text-align: left;">Supplier</th>
                    <th style="width: 200px; text-align: left;">Remark</th>
                </tr>
            </thead>
			<tbody >

			 @foreach ($offerActions as $offerAction)
			 @php
			 $rowCount = count($offerAction->offer->species_ordered)
			 @endphp
				<tr >
					<td style="width: 50px;"><a href="{{ url('offers', $offerAction->offer->id) }}">{{ url('offers', $offerAction->offer->id)}}</a></td>
					<td style="width: 200px">{{ ($offerAction->offer->client->organisation) ? $offerAction->offer->client->organisation->name : '' }}</td>
					<td style="width: 150px">{{count($offerAction->offer->species_ordered)}}</td>
					<td style="width: 300px">
						@php
						$species = $offerAction->offer->species_ordered[0]
						@endphp
					{{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
					</td>
					<td>{{$offerAction->offer->supplier->full_name}}</td>
					<td>{{$offerAction->remark}}</td>
				</tr>
               @for ($i=1; $i<$rowCount; $i++)
				 @php
					$speciesRow = $offerAction->offer->species_ordered[$i]
				  @endphp
				  <tr>
					<td colspan=3></td>
				    <td>{{$speciesRow->oursurplus->animal->common_name}} ({{$speciesRow->oursurplus->animal->scientific_name}})</td>
					<td colspan=2></td>
				  </tr>
				@endfor

			 @endforeach
			</tbody>
	</table>
	@include('emails.email-signature')
	  </body>

			</div>
        </td>
	  </tr>
    </table>
</html>
