<?php

namespace App\Http\Controllers;

use App\Enums\FilterDataDashboard;
use App\Enums\RowColorDashboard;
use App\Enums\TypeDashboard;
use App\Http\Requests\DashboardCreateRequest;
use App\Http\Requests\DashboardUpdateRequest;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\EmailToken;
use App\Models\ItemDashboard;
use Beta\Microsoft\Graph\Model\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class DashboardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dashboards = Dashboard::orderBy('id', 'ASC')->orderBy("parent_id", "ASC")->get();

        return view('dashboards.index', compact('dashboards'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $type_style  = TypeDashboard::get();
        $row_color   = RowColorDashboard::get();
        $filter_data = FilterDataDashboard::get();

        $parents = Dashboard::where("main", 1)->get();
        $type = $request->type ?? "";
        $html = '<div class="custom-dd dd" id="nestable_list_1">
                    <ol class="dd-list">
                        ';
        $html = $this->getHtmlDashboarSon($parents, $html);
        $html .= '</div>
        </ol>
            ';
        $parents = $html;

        return view('dashboards.create', compact('parents', "type_style", "row_color", "filter_data", "type"));
    }

    public function getHtmlDashboarSon($parents, $html)
    {
        foreach ($parents as $key => $parent) {
            if (!empty($parent->parent_id) && $parent->type_style == 'default') {
                $html .= '<li class="dd-item" data-id="1">
                            <div class="dd-handle">
                                <input type="radio" class="selector-parent" style="margin: -1px 7px 0px 0;" name="parent_id" value="' . $parent->id . '" />' . $parent->name . '
                            </div>
                        ';
            } else {
                $html .= '<li class="dd-item" data-id="1">
                            <div class="dd-handle">
                                <input type="radio" class="selector-parent" style="margin: -1px 7px 0px 0;" name="parent_id" value="' . $parent->id . '" />' . $parent->name . '
                            </div>
                        ';
            }

            if (!empty($parent->dashboards->toArray())) {
                $html .= '<ol class="dd-list">';
                $html = $this->getHtmlDashboarSon($parent->dashboards, $html);
                $html .= '</li>';
            } else {
                $html .= '</li>';
            }
        }
        $html .= '</ol>';

        return $html;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\DashboardCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DashboardCreateRequest $request)
    {
        if (!empty($request->main)) {
            $request["main"] = 1;
        } else {
            $request["main"] = 0;
        }
        Dashboard::create($request->all());

        return redirect(route('dashboards.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dashboard  $dashboard
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Dashboard $dashboard)
    {
        $type_style  = TypeDashboard::get();
        $row_color   = RowColorDashboard::get();
        $filter_data = FilterDataDashboard::get();
        $parents     = Dashboard::where("main", 1)->get();
        $html        = '<div class="custom-dd dd" id="nestable_list_1">
                    <ol class="dd-list">
                        ';
        $html = $this->getHtmlDashboarSon($parents, $html);
        $html .= '</div>
        </ol>
            ';
        $parents = $html;

        return view('dashboards.edit', compact('dashboard', 'parents', 'type_style', 'row_color', 'filter_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\DashboardUpdateRequest  $request
     * @param  \App\Models\Dashboard  $region
     * @return \Illuminate\Http\Response
     */
    public function update(DashboardUpdateRequest $request, Dashboard $dashboard)
    {
        if (!empty($request->main)) {
            $request["main"] = 1;
        } else {
            $request["main"] = 0;
        }
        $dashboard->update($request->all());

        return redirect(route('dashboards.index'));
    }

    public function getDashboardParent(Request $request)
    {
        if (!empty($request->dataId)) {
            $dashboards = Dashboard::where("parent_id", $request->dataId)->where("type_style", "default")->where("type_style", "!=", "Link")->get();
            $html       = '<div class="custom-dd dd" id="nestable_list_1">
                    <ol class="dd-list">
                        ';
            $html = $this->getHtmlDashboarSon($dashboards, $html);
            $html .= '</div>

                ';

            return response()->json(['error' => false, 'dashboards' => $html]);
        }
    }

    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $item_dashboard = ItemDashboard::findOrFail($id);

                if (!empty($item_dashboard)) {
                    $item_dashboard->delete();

                    return response()->json(['error' => false, 'message' => 'The item was successfully removed from the dashboard']);
                } else {
                    return response()->json(['error' => true, 'message' => 'The item was not deleted correctly from the dashboard']);
                }
            }
        }
    }

    public function delete_dashboard(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $dashboard = Dashboard::findOrFail($id);
                if (!empty($dashboard)) {
                    if ($dashboard["main"] == 1) {
                        $dasboard_relation = Dashboard::where("parent_id", $dashboard["id"])->get();
                        if (!empty($dasboard_relation)) {
                            foreach ($dasboard_relation as $row) {
                                $item_dashboard = ItemDashboard::where("dashboard_id", $row["id"])->get();
                                if (!empty($item_dashboard)) {
                                    foreach ($item_dashboard as $item) {
                                        $item->delete();
                                    }
                                }
                                $row->delete();
                            }
                        }
                        $dashboard->delete();
                    } else {
                        $item_dashboard = ItemDashboard::where("dashboard_id", $id)->get();
                        if (!empty($item_dashboard)) {
                            foreach ($item_dashboard as $item) {
                                $item->delete();
                            }
                        }
                        $dashboard->delete();
                    }
                } else {
                    return response()->json(['error' => true, 'message' => 'The item was not deleted correctly from the dashboard']);
                }
            }

            return response()->json(['error' => false, 'message' => 'The item was successfully removed from the dashboard']);
        }
    }

    public function getItemBlock(Request $request)
    {
        if (!empty($request->id)) {
            $data["dashboard"] = Dashboard::find($request->id);
            $view              = View::make('dashboards.item_block', $data);
            $html['error']     = false;
            $html['content']   = $view->render();

            return response()->json($html);
        }
    }

    public function getFilterData(Request $request)
    {
        if (!empty($request->id)) {
            $dashboard = Dashboard::find($request->id);
            if (!empty($dashboard->filter_data)) {
                $user        = Auth::user();
                $email_token = EmailToken::where("user_id", $user->id)->first();
                switch ($dashboard->filter_data) {
                    case 'inbox_sender_user':
                        if (empty($email_token)) {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = true;

                            return response()->json($html);
                        }
                        $data["emails"] = Email::where("is_delete", 0)->where("is_send", 0)->where("is_draft", 0)->where("to_email", $user->email)->where("archive", 0)->whereNull("directorie_id")->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));

                        if (!empty($data["emails"])) {
                            $view            = View::make('dashboards.item_emails_action', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                            $html['account'] = false;
                        }
                        break;
                    case 'inbox_sender_info':
                        if (empty($email_token)) {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = true;

                            return response()->json($html);
                        }
                        $data["emails"] = Email::where("is_delete", 0)->where("is_send", 0)->where("is_draft", 0)->where("to_email", "info@zoo-services.com")->where("archive", 0)->whereNull("directorie_id")->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));

                        if (!empty($data["emails"])) {
                            $view            = View::make('dashboards.item_emails_action', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'inbox_send':
                        if (empty($email_token)) {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = true;

                            return response()->json($html);
                        }
                        $data["emails"] = Email::where(function ($query) use ($request, $user) {
                            $query->where("from_email", $user->email)
                                ->orWhere("from_email", "info@zoo-services.com");
                        })->where("is_send", 1)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));

                        if (!empty($data["emails"])) {
                            $view            = View::make('dashboards.item_emails_action', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'inbox_draft':
                        if (empty($email_token)) {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = true;

                            return response()->json($html);
                        }
                        $data["emails"] = Email::where(function ($query) use ($request, $user) {
                            $query->where("from_email", $user->email)
                                ->orWhere("from_email", "info@zoo-services.com");
                        })->where("is_draft", 1)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));

                        if (!empty($data["emails"])) {
                            $view            = View::make('dashboards.item_emails_action', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'new_inserted_surplus':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'new_inserted_wanted':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'new_inserted_contacts_institutes':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'offers_sent':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'new_orders':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'tasks_have_been_sent_out':
                        $items["items"] = ItemDashboard::select("*", "item_dashboards.id AS id", "item_dashboards.created_at AS created_at", "item_dashboards.updated_at AS updated_at")
                            ->where("item_dashboards.itemable_type", "email")->where("item_dashboards.dashboard_id", $dashboard->id)
                            ->join('emails', 'item_dashboards.itemable_id', '=', 'emails.id')
                            ->where("emails.to_email", $user->email)
                            ->orderBy('item_dashboards.created_at', "DESC")->paginate(50)->appends($request->except('page'));

                        $data["row"] = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'offers_approve':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'offers_remind':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }
                        break;
                    case 'offers_inquiry':
                        $items["items"] = ItemDashboard::where("itemable_type", "email")->where("dashboard_id", $dashboard->id)->orderBy('created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"]    = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }

                        break;
                    case 'tasks_by_me_user':
                        $items["items"] = ItemDashboard::select("*", "item_dashboards.id AS id", "item_dashboards.created_at AS created_at", "item_dashboards.updated_at AS updated_at")
                            ->where("item_dashboards.itemable_type", "email")->where("item_dashboards.dashboard_id", $dashboard->id)
                            ->join('emails', 'item_dashboards.itemable_id', '=', 'emails.id')
                            ->where("emails.to_email", $user->email)
                            ->orderBy('item_dashboards.created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"] = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }

                        break;
                    case 'tasks_by_me_today_user':
                        $dashboard_today = Dashboard::where("filter_data", "tasks_by_me_user")->first();
                        $items["items"] = ItemDashboard::select("*", "item_dashboards.id AS id", "item_dashboards.created_at AS created_at", "item_dashboards.updated_at AS updated_at")
                            ->where("item_dashboards.itemable_type", "email")
                            ->where("item_dashboards.dashboard_id", $dashboard_today["id"])
                            ->join('emails', 'item_dashboards.itemable_id', '=', 'emails.id')
                            ->where("emails.to_email", $user->email)
                            ->whereDate("emails.created_at", Carbon::now()->format('Y-m-d'))
                            ->orderBy('item_dashboards.created_at', "DESC")
                            ->paginate(50)
                            ->appends($request->except('page'));
                        $data["row"] = $items;
                        if (!empty($items["items"])) {
                            if($items["items"]->count() > 0){
                                $view            = View::make('dashboards.item_emails', $data);
                                $html['error']   = false;
                                $html['content'] = $view->render();
                            }else{
                                $html['error']   = false;
                                $html['content'] = '<p style="margin: 0 0 0 11px;">There are no tasks to do</p>';
                            }
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }

                        break;
                    case 'tasks_over_time_remind':
                        $items["items"] = ItemDashboard::select("*", "item_dashboards.id AS id", "item_dashboards.created_at AS created_at", "item_dashboards.updated_at AS updated_at")
                            ->where("item_dashboards.itemable_type", "email")->where("item_dashboards.dashboard_id", $dashboard->id)
                            ->join('emails', 'item_dashboards.itemable_id', '=', 'emails.id')
                            ->where("emails.to_email", $user->email)
                            ->orderBy('item_dashboards.created_at', "DESC")->paginate(50)->appends($request->except('page'));
                        $data["row"] = $items;
                        if (!empty($items["items"])) {
                            $view            = View::make('dashboards.item_emails', $data);
                            $html['error']   = false;
                            $html['content'] = $view->render();
                        } else {
                            $html['error']   = true;
                            $html['content'] = [];
                            $html['account'] = false;
                        }

                        break;
                    default:
                        $html['error']   = true;
                        $html['content'] = [];
                        $html['account'] = false;
                        break;
                }
            } else {
                $html['error']   = true;
                $html['content'] = [];
                $html['account'] = false;
            }
        } else {
            $html['error']   = true;
            $html['content'] = [];
            $html['account'] = false;
        }

        return response()->json($html);
    }

    /**
     * Calculate totals of some dashboard items to display them as alerts on the dashboard blocks
     * Note: 
     * Queries to calculate the totals are the same as in function getFilterData ^
     * When updating those queries ^, please also update queries in this function v
     *
     * @param \App\Models\Dashboard $dashboard
     * @return array
     */
    public function getFilterDataTotal(\App\Models\Dashboard $dashboard)
    {
         if (!empty($dashboard)) {
            $filter_data = Dashboard::where('parent_id', $dashboard->id)->get();

            if (!empty($filter_data)) {

               $user = Auth::user();
               $dashboard_items = Dashboard::query();

               foreach ($filter_data as $item) {

                  $arr_dailynews = [
                     'new_inserted_surplus',
                     'new_inserted_wanted',
                     'new_inserted_contacts_institutes',
                     'offers_sent',
                     'new_orders',
                     'invoices_sent_out',
                  ];
                  $arr_tasksoffers = [
                     'offers_remind',
                     'offers_inquiry',
                     'offers_approve',
                  ];
                  $arr_tasksbyme = [
                     'tasks_have_been_sent_out',
                     'tasks_by_me_user',
                     'tasks_over_time_remind',
                  ];
                  $arr_taskstoday = [
                     'tasks_by_me_today_user',
                  ];
                  if (in_array($item->filter_data, $arr_dailynews)) {
                     $data['total'][$item->filter_data] = ItemDashboard::where('itemable_type', 'email')->where('dashboard_id', $item->id)->paginate(50)->total();
                  }
                  if (in_array($item->filter_data, $arr_tasksoffers)) {
                     $data['total'][$item->filter_data] = ItemDashboard::where('itemable_type', 'email')->where('dashboard_id', $item->id)->paginate(50)->total();
                  }
                  if (in_array($item->filter_data, $arr_tasksbyme)) {
                     $data['total'][$item->filter_data] = ItemDashboard::select('*', 'item_dashboards.id AS id', 'item_dashboards.created_at AS created_at', 'item_dashboards.updated_at AS updated_at')
                        ->where('item_dashboards.itemable_type', 'email')->where('item_dashboards.dashboard_id', $item->id)
                        ->join('emails', 'item_dashboards.itemable_id', '=', 'emails.id')
                        ->where('emails.to_email', $user->email)->paginate(50)->total();
                  }
                  if (in_array($item->filter_data, $arr_taskstoday)) {
                        $dashboard_today = Dashboard::where('filter_data', 'tasks_by_me_user')->first();
                        $data['total'][$item->filter_data] = ItemDashboard::select('*', 'item_dashboards.id AS id', 'item_dashboards.created_at AS created_at', 'item_dashboards.updated_at AS updated_at')
                            ->where('item_dashboards.itemable_type', 'email')
                            ->where('item_dashboards.dashboard_id', $dashboard_today['id'])
                            ->join('emails', 'item_dashboards.itemable_id', '=', 'emails.id')
                            ->where('emails.to_email', $user->email)
                            ->whereDate('emails.created_at', Carbon::now()->format('Y-m-d'))
                            ->paginate(50)->total();
                  }
               }
            }
        }
        return $data;
    }
}
