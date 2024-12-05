<!doctype html>
<html>
@include('emails.email-header')
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td class="container">
            <div class="content">
                <p>{{$email_title}},</p>
                <br> 
                <table style="font-size: 13px;" border="0">
                    <tr>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Assigned by
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            User in charge
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px; width: 200px;">
                            Description
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px; width: 200px;">
                            Comment
                        </td>
                        <td style="font-weight: bold; margin-bottom: 20px; width: 100px;">
                            Type
                        </td>
                        <td style="font-weight: bold; margin-bottom: 20px;">
                            Action
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Due date
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Url
                        </td>
                    </tr>
                    @php($date = "")
                    @foreach ($tasks as $task)
                        <tr>
                            <td>
                                {{$task->admin->name ?? ""}}
                            </td>
                            <td>
                                {{$task->user->name ?? ""}}
                            </td>
                            <td>
                                {{$task->description ?? ""}}
                            </td>
                            <td>
                                {!! $task->comment ?? "" !!}
                            </td>
                            <td>
                                @if ($task->taskable_type != null)
                                    <a href="{{ route('offers.show', [$task->taskable_id]) }}">{{ Str::upper($task->taskable_type) . ': ' . $task->taskable->full_number }}</a><br>
                                @else
                                    GENERAL
                                @endif
                            </td>
                            <td>
                                {{$task->action_field ?? ""}}
                            </td>
                            <td>
                                {{ ($task->due_date != null) ? date('d-m-Y', strtotime($task->due_date)) : '' }}
                            </td>
                            <td>
                                <a href="{{env("APP_URL")}}/tasks/{{ $task->id }}">{{env("APP_URL")}}/tasks/{{ $task->id }}</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <br>
                <br>
                @include('emails.email-signature')
            </div>
        </td>
    </tr>
</table>
</body>
</html>
