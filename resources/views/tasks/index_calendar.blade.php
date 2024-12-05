@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        <a href="{{ route('tasks.createInCalendar') }}" class="btn btn-light">
            <i class="fas fa-fw fa-plus"></i> Add
        </a>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Tasks in calendar') }}</h1>
    <p class="text-white">Here you can manage all your tasks</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">
        @unless($tasksAll->isEmpty())
            <div id='calendar'></div>
        @else
            <p>No tasks found.</p>
        @endunless
    </div>
</div>

@endsection

@section('page-scripts')

<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: "{{ route('tasks.tasksForCalendar') }}",
            selectable: true,
            editable: true,
            eventClick: function (info) {
                let url = "{{route('tasks.editCalendarTask')}}?id="+info.event.id;
                window.location = url;
            },
            eventDrop: function (info) {
                let start = info.event.start;
                let end = info.event.end;
                start = (new Date(start)).toISOString().slice(0, 10);
                end = (new Date(end)).toISOString().slice(0, 10);
                $.ajax({
                    type: "POST",
                    url: "{{ route('tasks.dropAndDragInCanlendar') }}",
                    data: {
                        id: info.event.id,
                        action_date: start,
                        due_date: end
                    },
                    success: function (response) {
                        alert("Updated Successfully");
                    }
                });
            }
        });
        calendar.render();
    });

</script>

@endsection
