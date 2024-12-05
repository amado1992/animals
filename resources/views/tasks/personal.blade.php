@extends('layouts.admin')

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header">
        <h1>Tasks for today</h1>
    </div>
    <div class="card-body">

      @unless($tasksToday->isEmpty())
      <div class="table-responsive">
        <table class="table table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Tasks</th>
              <th>Action</th>
              <th>Deadline</th>
              <th>Who</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $tasksToday as $task )
            <tr>
              <td><a href="{{ route('tasks.edit', [$task->id]) }}">{{ $task->label }}</a></td>
              <td>{{ $task->next_action }}</td>
              <td>{{ $task->due_date }}</td>
              <td>{{ $task->user->name }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p> No tasks anymore. You can go ;-) </p>

      @endunless
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header">
        <h1>Other tasks</h1>
    </div>
    <div class="card-body">

      @unless($tasks->isEmpty())
      <div class="table-responsive">
        <table class="table table-bordered" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Tasks</th>
              <th>Next action</th>
              <td>Deadline</td>
              <th>Who</th>
            </tr>
          </thead>
          <tbody>
            @foreach( $tasks as $task )
            <tr>
              <td><a href="{{ route('tasks.edit', [$task->id]) }}">{{ $task->label }}</a></td>
              <td>{{ $task->next_action }}</td>
              <td>{{ $task->due_date }}</td>
              <td>{{ $task->user->name }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else

        <p> No tasks anymore. You can go ;-) </p>

      @endunless
    </div>
  </div>

@endsection

