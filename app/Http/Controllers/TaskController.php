<?php

namespace App\Http\Controllers;

use App\Enums\TaskActions;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Mail\SendGeneralEmail;
use App\Models\Animal;
use App\Models\Contact;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\ItemDashboard;
use App\Models\Labels;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Organisation;
use App\Services\GraphService;
use Illuminate\Support\Facades\App;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $allTasks = Task::orderBy('due_date');

        $filterData = $this->filterData($allTasks)["filterData"];

        $resultArray = $this->getTodayTasks($request);
        $todayTasks = $resultArray['todayTasks'];

        $resultForApprovalArray = $this->forApprovalTasks($request);
        $forApprovalTasks = $resultForApprovalArray['forApprovalTasks'];

        $resultCompleteArray = $this->completeTasks($request);
        $completeTasks = $resultCompleteArray['completeTasks'];

        $resultNoCompleteArray = $this->noCompleteTasks($request);
        $noCompleteTasks = $resultNoCompleteArray['noCompleteTasks'];

        $resultFutureArray = $this->futureTasks($request);
        $futureTasks = $resultFutureArray['futureTasks'];

        $roles   = Role::where('name', '<>', 'website-user')->get();
        $users   = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $actions = TaskActions::get();

        if (!empty($request->status_email) && $request->status_email == "forapproval") {
            $selectedTasksTab = "#forapprovalTab";
        } else {
            $selectedTasksTab = '#todayTab';
            if (session()->has('task.filter')) {
                $sessionData = session('task.filter');

                if (isset($sessionData['taskTab'])) {
                    $selectedTasksTab = $sessionData['taskTab'];
                }
            }
        }

        if (isset($request) && isset($request['recordsPerPageOther'])) {
            $allTasks = $allTasks->paginate($request['recordsPerPageOther']);
        } else {
            $allTasks = $allTasks->paginate(50);
        }
        //dump(DB::getQueryLog()); // Show results of log
        return view('tasks.index', compact(
            'todayTasks',
            'forApprovalTasks',
            'completeTasks',
            'noCompleteTasks',
            'futureTasks',
            'allTasks',
            'users',
            'actions',
            'filterData',
            'selectedTasksTab'
        ));
    }

    /**
     * Get array admin user tasks.
     */
    public function filterData($allTasks)
    {
        $filterData = [];
        // Check if filter is set on session
        if (session()->has('task.filter')) {
            $request = session('task.filter');
            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_description'])) {
                $allTasks->where('description', 'like', '%' . $request['filter_description'] . '%');

                $filterData = Arr::add($filterData, 'filter_description', 'Description: ' . $request['filter_description']);
            }

            if (isset($request['filter_action'])) {
                $allTasks->where('action', $request['filter_action']);

                $filterData = Arr::add($filterData, 'filter_action', 'Action: ' . $request['filter_action']);
            }

            if (isset($request['filter_due_date'])) {
                $allTasks->where('due_date', '>=', $request['filter_due_date']);

                $filterData = Arr::add($filterData, 'filter_due_date', 'Action ready on: ' . $request['filter_due_date']);
            }

            if (isset($request['filter_animal_id'])) {
                $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                $allTasks->where(function ($query) use ($filterAnimal) {
                    $query->whereHasMorph('taskable', ['App\Models\Offer'], function ($query) use ($filterAnimal) {
                        $query->whereHas('offer_species.oursurplus', function ($query) use ($filterAnimal) {
                            $query->where('animal_id', $filterAnimal->id);
                        });
                    })
                        ->orWhereHasMorph('taskable', ['App\Models\Order'], function ($query) use ($filterAnimal) {
                            $query->whereHas('offer.offer_species.oursurplus', function ($query) use ($filterAnimal) {
                                $query->where('our_surplus.animal_id', $filterAnimal->id);
                            });
                        });
                });

                $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
            }

            if (isset($request['filter_finished_tasks'])) {
                $allTasks->whereNotNull('finished_at');

                $filterData = Arr::add($filterData, 'filter_finished_tasks', 'Finished: ' . $request['filter_finished_tasks']);
            }

            if (isset($request['filter_user'])) {
                $filterUser = User::where('id', $request['filter_user'])->first();

                $allTasks->where('user_id', $filterUser->id);

                $filterData = Arr::add($filterData, 'filter_user', 'User in charge: ' . $filterUser->name);
            }

            if (isset($request['filter_created_by'])) {
                $filterCreatedBy = User::where('id', $request['filter_created_by'])->first();

                $allTasks->where('created_by', $filterCreatedBy->id);

                $filterData = Arr::add($filterData, 'filter_created_by', 'Created by: ' . $filterCreatedBy->name);
            }
        }

        return ["filterData" => $filterData, "allTasks" => $allTasks];
    }

    /**
     * Get array admin user tasks.
     */
    public static function getAdminTasks()
    {
        $user      = User::where('id', Auth::id())->first();
        $user_role = $user->roles()->first()->name;

        if (Auth::user()->hasPermission('tasks.view-all') || $user_role == "admin") {
            return Task::whereNull('finished_at')->where("status", "new")->orderBy('due_date')->get();
        } else {
            return Task::whereNull('finished_at')->where("status", "new")->where('user_id', Auth::id())->orderBy('due_date')->get();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTodayTasks($request_data)
    {
        $user = Auth::user()->toArray();
        if (Auth::user()->hasPermission('tasks.view-all') || Auth::user()->hasRole(['admin'])) {
            $result = Task::whereDate('due_date', Carbon::now()->format('Y-m-d'))
                ->whereNull('finished_at')
                ->where('status', 'new')
                ->orderBy('due_date', 'ASC');
        } else {
            $result = Task::whereDate('due_date', Carbon::now()->format('Y-m-d'))
                ->whereNull('finished_at')
                ->where('status', 'new')
                ->where("user_id", $user["id"])
                ->orderBy('due_date', 'ASC');
        }

        $filter = $this->filterData($result);

        $result = $filter["allTasks"];

        if (session()->has('task.filter')) {
            $request = session('task.filter');
        }

        if (isset($request) && isset($request['recordsPerPageToday']))
            $resultArray["todayTasks"] = $result->paginate($request['recordsPerPageToday'])->appends($request_data->except('page'));
        else
            $resultArray["todayTasks"] = $result->paginate(10)->appends($request_data->except('page'));

        return $resultArray;
    }

    /**
     * Get today tasks.
     */
    public function forApprovalTasks($request_data)
    {
        $user = Auth::user()->toArray();
        if (Auth::user()->hasPermission('tasks.view-all') || Auth::user()->hasRole(['admin'])) {
            $result = Task::whereNull('finished_at')
                ->where('status', 'forapproval')
                ->orderBy('due_date', 'DESC');
        } else {
            $result = Task::whereNull('finished_at')
                ->where('status', 'forapproval')
                ->where("created_by", $user["id"])
                ->orderBy('due_date', 'DESC');
        }

        $filter = $this->filterData($result);

        $result = $filter["allTasks"];

        if (session()->has('task.filter')) {
            $request = session('task.filter');
        }

        if (isset($request) && isset($request['recordsPerPageForApproval']))
            $resultArray["forApprovalTasks"] = $result->paginate($request['recordsPerPageForApproval'])->appends($request_data->except('page'));
        else
            $resultArray["forApprovalTasks"] = $result->paginate(10)->appends($request_data->except('page'));


        return $resultArray;
    }

    /**
     * Get today tasks.
     */
    public function completeTasks($request_data)
    {
        $user = Auth::user()->toArray();
        if (Auth::user()->hasPermission('tasks.view-all') || Auth::user()->hasRole(['admin'])) {
            $result = Task::whereNotNull('finished_at')
                ->where('status', 'complete')
                ->orderBy('due_date', 'DESC');
        } else {
            $result = Task::whereNotNull('finished_at')
                ->where('status', 'complete')
                ->where("user_id", $user["id"])
                ->orderBy('due_date', 'DESC');
        }

        $filter = $this->filterData($result);

        $result = $filter["allTasks"];

        if (session()->has('task.filter')) {
            $request = session('task.filter');
        }

        if (isset($request) && isset($request['recordsPerPageComplete']))
            $resultArray["completeTasks"] = $result->paginate($request['recordsPerPageComplete'])->appends($request_data->except('page'));
        else
            $resultArray["completeTasks"] = $result->paginate(10)->appends($request_data->except('page'));

        return $resultArray;
    }

    /**
     * Get today tasks.
     */
    public function noCompleteTasks($request_data)
    {
        $user = Auth::user()->toArray();
        if (Auth::user()->hasPermission('tasks.view-all') || Auth::user()->hasRole(['admin'])) {
            $result = Task::whereDate('due_date', '<', Carbon::now()->format('Y-m-d'))
                ->whereNull('finished_at')
                ->where('status', '!=', 'complete')
                ->where('status', '!=', 'forapproval')
                ->orWhere('status', 'incomplete')
                ->orderBy('due_date', 'DESC');
        } else {
            $result = Task::whereDate('due_date', '<', Carbon::now()->format('Y-m-d'))
                ->whereNull('finished_at')
                ->where('status', '!=', 'complete')
                ->where('status', '!=', 'forapproval')
                ->where("user_id", $user["id"])
                ->orWhere('status', 'incomplete')
                ->orderBy('due_date', 'DESC');
        }

        $filter = $this->filterData($result);

        $result = $filter["allTasks"];

        if (session()->has('task.filter')) {
            $request = session('task.filter');
        }

        if (isset($request) && isset($request['recordsPerPageNoComplete']))
            $resultArray["noCompleteTasks"] = $result->paginate($request['recordsPerPageNoComplete'])->appends($request_data->except('page'));
        else
            $resultArray["noCompleteTasks"] = $result->paginate(10)->appends($request_data->except('page'));

        return $resultArray;
    }

    /**
     * Get today tasks.
     */
    public function futureTasks($request_data)
    {
        $user = Auth::user()->toArray();
        if (Auth::user()->hasPermission('tasks.view-all') || Auth::user()->hasRole(['admin'])) {
            $result = Task::whereDate('due_date', '>', Carbon::now()->format('Y-m-d'))
                ->whereNull('finished_at')
                ->where('status', 'new')
                ->where('status', '!=', 'complete')
                ->where('status', '!=', 'forapproval')
                ->where('status', '!=', 'incomplete')
                ->orderBy('due_date', 'DESC');
        } else {
            $result = Task::whereDate('due_date', '>', Carbon::now()->format('Y-m-d'))
                ->whereNull('finished_at')
                ->where('status', 'new')
                ->where('status', '!=', 'complete')
                ->where('status', '!=', 'forapproval')
                ->where('status', '!=', 'incomplete')
                ->where("user_id", $user["id"])
                ->orderBy('due_date');
        }

        $filter = $this->filterData($result);

        $result = $filter["allTasks"];

        if (session()->has('task.filter')) {
            $request = session('task.filter');
        }

        if (isset($request) && isset($request['recordsPerPageFuture']))
            $resultArray["futureTasks"] = $result->paginate($request['recordsPerPageFuture'])->appends($request_data->except('page'));
        else
            $resultArray["futureTasks"] = $result->paginate(10)->appends($request_data->except('page'));

        return $resultArray;
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('task.filter');

        return redirect(route('tasks.index'));
    }

    /**
     * Show all today tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllTodayTasks()
    {
        session()->forget('task.filter_today_tasks');

        return redirect(route('tasks.index'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCalendar()
    {
        $tasksAll = Task::whereNull('finished_at')->orderBy('action_date')->get();

        return view('tasks.index_calendar', compact('tasksAll'));
    }

    /**
     * Get tasks for calendar.
     *
     * @return \Illuminate\Http\Response
     */
    public function tasksForCalendar()
    {
        $tasksAll = Task::whereNull('finished_at')->orderBy('action_date')->get();

        $calendar_tasks = [];
        foreach ($tasksAll as $task) {
            array_push($calendar_tasks, ['id' => $task->id, 'title' => ($task->description != null) ? $task->description : 'Empty', 'start' => date('Y-m-d', strtotime($task->action_date))]);
        }

        return response()->json($calendar_tasks);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function personal()
    {
        $tasks      = Task::where('user_id', Auth::id())->orderBy('due_date')->get();
        $tasksToday = Task::where('user_id', Auth::id())->get();

        return view('tasks.personal', compact('tasks', 'tasksToday'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $calendar_view = false;
        $roles         = Role::where('name', '<>', 'website-user')->get();
        $users         = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $actions       = TaskActions::get();

        return view('tasks.create', compact('users', 'actions', 'calendar_view'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createInCalendar()
    {
        $calendar_view = true;
        $roles         = Role::where('name', '<>', 'website-user')->get();
        $users         = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');

        return view('tasks.create', compact('users', 'calendar_view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TaskCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskCreateRequest $request)
    {
        if ($request->calendar_view) {
            $request['finished_at'] = $request->input('finish_task') ? Carbon::now()->format('Y-m-d H:i:s') : null;
        }

        $now      = Carbon::now();
        $due_date = $now;
        switch ($request->quick_action_dates) {
            case 'today':
                $due_date         = $now;
                $request['never'] = 0;
                break;
            case 'tomorrow':
                $due_date         = $now->addDays(1);
                $request['never'] = 0;
                break;
            case 'week':
                $due_date         = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d H:i');
                $request['never'] = 0;
                break;
            case 'month':
                $due_date         = $now->endOfMonth()->format('Y-m-d H:i');
                $request['never'] = 0;
                break;
            case 'specific':
                $due_date         = $request->due_date;
                $request['never'] = 0;
                break;
            case 'never':
                $due_date         = $request->due_date_never;
                $request['never'] = 1;
                break;
            case 'default':
                $due_date         = null;
                $request['never'] = 0;
                break;
        }

        if (!empty($request["action"]) && $request["action"] == 'reminder' && empty($due_date)) {
            $request['due_date'] = $now->addDays(7);
        } else {
            $request['due_date'] = $due_date;
        }

        $request['created_by'] = Auth::id();
        $email_inbox           = Email::find($request->items_email_task);

        $sender = !empty($request->user_id) ? $request->user_id == 'sender' : null;
        $label  = Labels::where("name", "task")->first();
        if (!empty($request->items_email_task)) {
            if (!empty($sender)) {
                $request["user_id"] = null;
                $task               = Task::create($request->all());
                $task["contact_id"] = $email_inbox->contact_id ?? null;
                $task->save();
            } else {
                $task = Task::create($request->all());
            }

            if (!empty($email_inbox["order_id"]) && $email_inbox["order_id"]) {
                $order = Order::findOrFail($email_inbox["order_id"]);
                $order->tasks()->save($task);
                $order->refresh();
                $task["taskable_type"] = $request->task_type;
                $task["taskable_id"]   = $email_inbox["order_id"];
                $task->save();
            } elseif (!empty($email_inbox["offer_id"]) && $email_inbox["offer_id"]) {
                $offer = Offer::findOrFail($email_inbox["offer_id"]);
                $offer->tasks()->save($task);
                $offer->refresh();
                $task["taskable_type"] = $request->task_type;
                $task["taskable_id"]   = $email_inbox["offer_id"];
                $task->save();
            }

            $email_inbox->tasks()->attach($task);
            $email_inbox->labels()->detach($label);
            $email_inbox->labels()->attach($label);
            $email_inbox->save();
        } else {
            $task = Task::create($request->all());
        }

        if ($request->offer_order_id != null) {
            if ($request->task_type == 'offer') {
                $offer = Offer::findOrFail($request->offer_order_id);
                $offer->tasks()->save($task);
                $offer->refresh();
                $task["taskable_type"] = $request->task_type;
                $task["taskable_id"]   = $request->offer_order_id;
                $task->save();
            } elseif ($request->task_type == 'order') {
                $order = Order::findOrFail($request->offer_order_id);
                $order->tasks()->save($task);
                $order->refresh();
                $task["taskable_type"] = $request->task_type;
                $task["taskable_id"]   = $request->offer_order_id;
                $task->save();
            }
        }

        if ($task->user_id != null && !empty($request["action"]) && $request["action"] != 'reminder') {
            $email_from    = ($task->admin && $task->admin->email_domain == "zoo-services.com") ? $task->admin->email : 'info@zoo-services.com';
            $number_email  = "#TS-" . $task->id;
            $email_subject = substr($task->description, 0, 100) . "... " . $number_email;
            $email_body = view('emails.task-to-user', compact('task'))->render();

            try{
                $email_create = $this->createSentEmail($email_subject, $email_from, $task->user->email, $email_body, $task->id);
                if($task->user->email != 'johnrens@zoo-services.com'){
                    $email_options = new SendGeneralEmail($email_from, $email_subject, $email_body, $email_create["id"]);
                    if (App::environment('production')) {
                        $email_options->sendEmail($task->user->email, $email_options->build());
                    }else{
                        Mail::to($task->user->email)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                    }
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Failed to send mail correctly');
            }
        }

        if (!empty($task->contact_id) && !empty($task->contact->email)) {
            $email_from    = ($task->admin && $task->admin->email_domain == "zoo-services.com") ? $task->admin->email : 'info@zoo-services.com';
            $number_email  = "#TS-" . $task->id;
            $email_subject = substr($task->description, 0, 100) . "... " . $number_email;
            $email_body = view('emails.task-to-contact', compact('task'))->render();


            try{
                $email_create = $this->createSentEmail($email_subject, $email_from, $task->contact->email, $email_body, $task->id);
                if($task->user->email != 'johnrens@zoo-services.com'){
                    $email_options = new SendGeneralEmail($email_from, $email_subject, $email_body, $email_create["id"]);
                    if (App::environment('production')) {
                        $email_options->sendEmail($task->contact->email, $email_options->build());
                    }else{
                        Mail::to($task->contact->email)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                    }
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Failed to send mail correctly');
            }
        }else{
            if(!empty($sender) && !empty($email_inbox->from_email)){
                $email_from = ($task->admin && $task->admin->email_domain == "zoo-services.com") ? $task->admin->email : 'info@zoo-services.com';
                $number_email = "#TS-" . $task->id;
                $email_subject = substr($task->description, 0, 100) . "... " . $number_email;
                $email_body = view('emails.task-to-contact', compact('task', 'email_inbox'))->render();


                try{
                    $email_create = $this->createSentEmail($email_subject, $email_from, $email_inbox->from_email, $email_body, $task->id);
                    if($task->user->email != 'johnrens@zoo-services.com'){
                        $email_options = new SendGeneralEmail($email_from, $email_subject, $email_body, $email_create["id"]);
                        if (App::environment('production')) {
                            $email_options->sendEmail($email_inbox->from_email, $email_options->build());
                        }else{
                            Mail::to($email_inbox->from_email)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                        }
                    }
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Failed to send mail correctly');
                }
            }
        }

        if (!empty($request->items_email_task)) {
            return redirect(route('inbox.index'));
        }

        if ($request->calendar_view) {
            return redirect(route('tasks.indexCalendar'));
        } else {
            return redirect(route('tasks.index'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $emails_received = Email::where(function ($query) use ($task) {
            $query->orWhereHas('tasks', function ($query) use ($task) {
                $query->where('task_id', $task->id);
            });
            $query->orWhere("task_id", $task->id);
        })
        ->where("is_send", 0)
        ->orderBy("created_at", "DESC")
        ->paginate(10);

        $emails = Email::where(function ($query) use ($task) {
            $query->orWhereHas('tasks', function ($query) use ($task) {
                $query->where('task_id', $task->id);
            });
            $query->orWhere("task_id", $task->id);
        })
        ->where("is_send", 1)
        ->orderBy("created_at", "DESC")
        ->paginate(10);

        return view('tasks.show', compact('task', 'emails_received', 'emails'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $roles   = Role::where('name', '<>', 'website-user')->get();
        $users   = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $actions = TaskActions::get();

        $task->action_date = ($task->action_date) ? date("Y-m-d", strtotime($task->action_date)) : null;
        $task->due_date    = ($task->due_date) ? date("Y-m-d", strtotime($task->due_date)) : null;

        $calendar_view = false;

        return view('tasks.edit', compact('task', 'users', 'actions', 'calendar_view'));
    }

    /**
     * Edit task from calendar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editCalendarTask(Request $request)
    {
        $task  = Task::findOrFail($request->id);
        $roles = Role::where('name', '<>', 'website-user')->get();
        $users = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');

        $task->action_date = date("Y-m-d", strtotime($task->action_date));
        $task->due_date    = date("Y-m-d", strtotime($task->due_date));

        $calendar_view = true;

        return view('tasks.edit', compact('task', 'users', 'calendar_view'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TaskUpdateRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $previous_user_in_charge = $task->user;

        if ($request->calendar_view) {
            $request['finished_at'] = $request->input('finish_task') ? Carbon::now()->format('Y-m-d H:i:s') : null;
        }

        $now = Carbon::now();
        switch ($request->quick_action_dates) {
            case 'today':
                $due_date         = $now;
                $request['never'] = 0;
                break;
            case 'tomorrow':
                $due_date         = $now->addDays(1);
                $request['never'] = 0;
                break;
            case 'week':
                $due_date         = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d H:i');
                $request['never'] = 0;
                break;
            case 'month':
                $due_date         = $now->endOfMonth()->format('Y-m-d H:i');
                $request['never'] = 0;
                break;
            case 'specific':
                $due_date         = $request->due_date;
                $request['never'] = 0;
                break;
            case 'never':
                $due_date         = $request->due_date_never;
                $request['never'] = 1;
                break;
            case 'none':
                $due_date         = null;
                $request['never'] = 0;
                break;
            default:
                $due_date         = $task->due_date;
                $request['never'] = 0;
                break;
        }
        $request['due_date'] = $due_date;

        $task->update($request->all());

        if ($request->offer_order_id != null && ($task->taskable_type != $request->task_type || $task->taskable_id != $request->offer_order_id)) {
            if ($request->task_type == 'offer') {
                $offer = Offer::findOrFail($request->offer_order_id);
                $offer->tasks()->save($task);
                $offer->refresh();
            } elseif ($request->task_type == 'order') {
                $order = Order::findOrFail($request->offer_order_id);
                $order->tasks()->save($task);
                $order->refresh();
            }
        }

        if (!empty($request->user_id) && $previous_user_in_charge->id != $request->user_id) {
            $email_from    = ($task->admin && $task->admin->email_domain == "zoo-services.com") ? $task->admin->email : 'info@zoo-services.com';
            $email_body    = view('emails.task-to-user', compact('task'))->render();
            $number_email  = "#TS-" . $task->id;
            $email_subject = "Task updated in back-office. " . $number_email;

            try{
                $email_create = $this->createSentEmail($email_subject, $email_from, $task->user->email, $email_body, $task->id);
                if($task->user->email != 'johnrens@zoo-services.com'){
                    $email_options = new SendGeneralEmail($email_from, $email_subject, $email_body, $email_create["id"]);
                    if (App::environment('production')) {
                        $email_options->sendEmail($task->user->email, $email_options->build());
                    }else{
                        Mail::to($task->user->email)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                    }
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Failed to send mail correctly');
            }
        }

        if ($request->calendar_view) {
            return redirect(route('tasks.indexCalendar'));
        } else {
            return redirect(route('tasks.index'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dropAndDragInCanlendar(Request $request)
    {
        $task              = Task::findOrFail($request->id);
        $task->action_date = $request->action_date;
        $task->due_date    = $request->due_date;
        $task->update();

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect(route('tasks.index'));
    }

    /**
     * Mark selected tasks as finished.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markSelectedTasksAsFinishedOrNot(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $task = Task::findOrFail($id);

                $task->update(['finished_at' => ($task->finished_at == null) ? Carbon::now()->format('Y-m-d H:i:s') : null]);
            }
        }

        return response()->json(["tasksCounter" => $this->getTodayTasks()->count()]);
    }

    /**
     * Filter tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        // Set session code filter
        session(['task.filter' => $request->query()]);

        return redirect(route('tasks.index'));
    }

    /**
     * Filter today tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterTodayTasks(Request $request)
    {
        // Set session code filter
        session(['task.filter_today_tasks' => $request->query()]);

        return redirect(route('tasks.index'));
    }

    /**
     * Remove from task session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromTaskSession($key)
    {
        $query = session('task.filter');
        Arr::forget($query, $key);
        session(['task.filter' => $query]);

        return redirect(route('tasks.index'));
    }

    /**
     * Remove from today task session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromTodayTaskSession($key)
    {
        $query = session('task.filter_today_tasks');
        Arr::forget($query, $key);
        session(['task.filter_today_tasks' => $query]);

        return redirect(route('tasks.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query = session('task.filter');
        if (!empty($request->recordsPerPageToday)) {
            $query['recordsPerPageToday'] = $request->recordsPerPageToday;
            session(['task.filter' => $query]);
        }
        if (!empty($request->recordsPerPageForApproval)) {
            $query['recordsPerPageForApproval'] = $request->recordsPerPageForApproval;
            session(['task.filter' => $query]);
        }
        if (!empty($request->recordsPerPageComplete)) {
            $query['recordsPerPageComplete'] = $request->recordsPerPageComplete;
            session(['task.filter' => $query]);
        }
        if (!empty($request->recordsPerPageNoComplete)) {
            $query['recordsPerPageNoComplete'] = $request->recordsPerPageNoComplete;
            session(['task.filter' => $query]);
        }
        if (!empty($request->recordsPerPageFuture)) {
            $query['recordsPerPageFuture'] = $request->recordsPerPageFuture;
            session(['task.filter' => $query]);
        }

        return redirect(route('tasks.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $emails     = Email::where("task_id", $id)->delete();
                $deleteItem = Task::findOrFail($id);
                $deleteItem->delete();
            }
        }

        return response()->json();
    }

    /**
     * Save offer selected tab in session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectedTasksTab(Request $request)
    {
        $query            = session('task.filter');
        $query['taskTab'] = $request->taskTab;
        session(['task.filter' => $query]);

        return response()->json();
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $updateTask = Task::findOrFail($id);
                if ($request->status == "complete") {
                    $updateTask["finished_at"]      = Carbon::now()->format('Y-m-d H:i:s');
                    $updateTask["send_complete"]    = 1;
                    $updateTask["send_forapproval"] = 0;
                    if(!empty($updateTask->emails) && $updateTask->action == "reminder"){
                        $email_tast = Email::where("is_remind", 1)->where("task_id", $updateTask->id)->get();
                        if(!empty($email_tast)){
                            foreach ($email_tast as $email){
                                $email["is_remind"] = 0;
                                $email->save();
                            }
                        }
                    }
                }
                if ($request->status == "forapproval") {
                    $updateTask["send_forapproval"] = 1;
                }
                if (!empty($request->comment)) {
                    $updateTask["send_incomplete"] = 1;
                    $updateTask["comment"]         = $request->comment;
                }
                $updateTask["status"] = $request->status;
                $updateTask->save();

                if (!empty($request->comment)) {
                    $task          = $updateTask;
                    $email_from    = ($task->admin && $task->admin->email_domain == "zoo-services.com") ? $task->admin->email : 'info@zoo-services.com';
                    $email_body    = view('emails.task-to-user', compact('task'))->render();
                    $number_email  = "#TS-" . $task->id;
                    $email_subject = "Task not complete in back-office. " . $number_email;

                    try{
                        $email_create = $this->createSentEmail($email_subject, $email_from, $task->user->email, $email_body, $task->id);
                        $email_options = new SendGeneralEmail($email_from, $email_subject, $email_body, $email_create["id"]);
                        if (App::environment('production')) {
                            $email_options->sendEmail($task->user->email, $email_options->build());
                        }else{
                            Mail::to($task->user->email)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                        }
                    } catch (\Throwable $th) {
                        return redirect()->back()->with('error', 'Failed to send mail correctly');
                    }
                }
            }
        }

        return response()->json();
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetListEmailTasksComplete()
    {
        $task = Task::where("send_complete", 1)->get();
        if (!empty($task)) {
            foreach ($task as $row) {
                $row["send_complete"] = 0;
                $row->save();
            }
        }

        $title_dash = "Tasks";

        return view('components.reset_list_email_new', compact('title_dash'));
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createSentEmail($subject, $from, $email, $body, $id = null)
    {
        $label     = Labels::where("name", "task")->first();
        $task      = Task::find($id);
        $new_email = new Email();
        if (!empty($task->user)) {
            $name = $task->user->name . " " . $task->user->last_name;
        } elseif (!empty($task->contact)) {
            $name = $task->contact->full_name;
        } else {
            $name = "";
        }

        $new_email["from_email"] = $from;
        $new_email["to_email"]   = $email;
        $new_email["body"]       = $body;
        $new_email["guid"]       = rand(1, 100);
        $new_email["subject"]    = $subject;
        $new_email["name"]       = $name;

        if(!empty($task->taskable_type) && $task->taskable_type == "offer"){
            $new_email["offer_id"] = $task->taskable_id;
            $label_offer = Labels::where("name", "offer")->first();
        }
        if(!empty($task->taskable_type) && $task->taskable_type == "order"){
            $new_email["order_id"] = $task->taskable_id;
            $label_order = Labels::where("name", "order")->first();
        }
        $new_email["is_send"] = 1;
        $new_email->save();
        $new_email->labels()->attach($label);
        if(!empty($label_offer)){
            $new_email->labels()->attach($label_offer);
        }
        if(!empty($label_order)){
            $new_email->labels()->attach($label_order);
        }
        if (!empty($id)) {
            $new_email->tasks()->attach($task);
        }

        $dashboard = Dashboard::where("filter_data", "tasks_by_me_user")->first();
        if (!empty($dashboard)) {
            $new_item_dashboard                  = new ItemDashboard();
            $new_item_dashboard["itemable_id"]   = $new_email->id;
            $new_item_dashboard["itemable_type"] = "email";
            $new_item_dashboard["dashboard_id"]  = $dashboard->id;
            $new_item_dashboard->save();
        }

        return $new_email;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function saveSentEmail($email)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        if(!empty($email)){
            $userToken = $GraphService->getAllUserToken();
            if(!empty($userToken)){
                foreach ($userToken as $row){
                    $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                    $user_id = $GraphService->getUserByEmail($token, $email["from_email"]);
                    if(!empty($token)){
                        $email_attachment = [];
                        if(!empty($email->attachments)){
                            foreach ($email->attachments as $key => $attachment) {
                                $email_attachment[$key]["name"] = $attachment->name;
                                $email_attachment[$key]["type"] = $attachment->type;
                                $email_attachment[$key]["content"] = file_get_contents(Storage::disk('')->path($attachment->path));
                            }
                        }
                        $email_cc_array = [];
                        if ($email["cc_email"] != null)
                            $email_cc_array = array_map('trim', explode(',', $email["cc_email"]));

                        $email_bcc_array = [];
                        if ($email["bcc_email"] != null)
                            $email_bcc_array = array_map('trim', explode(',', $email["bcc_email"]));
                        $result = $GraphService->saveSentItems($token,  $user_id->getId(), $email["subject"], $email["body"], $email["to_email"],  $email_cc_array,  $email_bcc_array, $email_attachment);
                        $email["guid"] = $result["id"];
                        $email->save();
                        if(!empty($result)){
                            $result = $GraphService->updateIsDraftEmailInbox($token,  $user_id->getId(), $result["id"]);
                        }
                    }
                }
            }
        }
    }
}
