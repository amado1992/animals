<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            @if(!empty($task->comment))
                <p>
                    <b>Comment: </b><br>
                    {!! $task->comment !!}
                </p>
            @endif
            <p>
                <a href="{{env("APP_URL")}}/tasks/{{ $task->id }}">{{env("APP_URL")}}/tasks/{{ $task->id }}</a>
            </p>
            <p>
                <b>Assigned by: </b>{{$task->admin->name}}<br>
                <b>User in charge: </b>{{$task->user->name}}<br>
                <b>Description: </b>{{$task->description}}<br>
                <b>Taskable details: </b><br>
                @if ($task->taskable_type != null)
                    <a href="{{ route('offers.show', [$task->taskable_id]) }}">{{ Str::upper($task->taskable_type) . ': ' . $task->taskable->full_number }}</a><br><br>
                    <b>Client: </b>{{ ($task->taskable->client->full_name) ? $task->taskable->client->full_name : '' }}<br>
                    <b>Phone: </b>{{ ($task->taskable->client->mobile_phone) ? $task->taskable->client->mobile_phone : '' }}<br>
                    <b>Email: </b>{{ ($task->taskable->client->email) ? $task->taskable->client->email : '' }}<br>
                    @if ($task->taskable->supplier !== null)
                    <p>Supplier details</p>
                    <b>Contact: </b>{{ $task->taskable->supplier->full_name }}<br>
                    <b>E-mail: </b>{{ $task->taskable->supplier->email }}<br>
                    <b>Phone: </b>{{ ($task->taskable->supplier->organisation) ? $task->taskable->supplier->organisation->phone : '' }}<br>
                    @endif
                    @if ($task->taskable_type === 'offer')
                        @if ($task->taskable->offer_species->count() == 0)
                            <span style="color: red;">(No species added yet)</span>
                        @else
                            (@foreach ($task->taskable->species_ordered as $species)
                                {{ $species->oursurplus->animal->common_name }}
                                @if ($loop->index == 2) @break @else - @endif
                            @endforeach)
                        @endif
                    @elseif ($task->taskable_type === 'order')
                        @if ($task->taskable->offer->offer_species->count() == 0)
                            <span style="color: red;">(No species added yet)</span>
                        @else
                            (@foreach ($task->taskable->offer->species_ordered as $species)
                                {{ $species->oursurplus->animal->common_name }}
                                @if ($loop->index == 2) @break @else - @endif
                            @endforeach)
                        @endif
                    @endif
                @else
                    GENERAL
                @endif
                <br>
                <b>Action: </b>{{$task->action_field}}<br>
                <b>Due date: </b>{{ ($task->due_date != null) ? date('d-m-Y', strtotime($task->due_date)) : '' }}
            </p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
