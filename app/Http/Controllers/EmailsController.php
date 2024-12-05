<?php

namespace App\Http\Controllers;

use App\Enums\BankAccountOwner;
use App\Enums\ConfirmOptions;
use App\Enums\ContactMailingCategory;
use App\Enums\InboxDirectories;
use App\Enums\OrganisationInfoStatus;
use App\Enums\OrganisationLevel;
use App\Enums\ShipmentTerms;
use App\Enums\SurplusStatus;
use App\Enums\TaskActions;
use App\Exports\InboxExport;
use App\Helpers\FileSizeHelper;
use App\Http\Requests\SendEmailRequest;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Association;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Dashboard;
use App\Models\DefaultTextTask;
use App\Models\Directorie;
use App\Models\Email;
use App\Models\EmailToken;
use App\Models\GeneralDocument;
use App\Models\InterestSection;
use App\Models\ItemDashboard;
use App\Models\Labels;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Organisation;
use App\Models\OrganisationType;
use App\Models\Origin;
use App\Models\OurWanted;
use App\Models\Role;
use App\Models\Surplus;
use App\Models\StdText;
use App\Models\Task;
use App\Models\User;
use App\Models\Wanted;
use App\Services\GraphService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// use App\Helpers\FileSizeHelper;
use App\Http\Requests\SendEmailBulkRequest;
use App\Models\Color;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;


class EmailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data                        = $this->get_data_by_request($request);
        $user                        = User::where('id', Auth::id())->first();
        $roles                       = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $data['labels']              = Labels::orderBy('title', 'ASC')->get();
        $data['directories']         = Directorie::orderBy('title', 'ASC')->get();
        $data['countries']           = Country::orderBy('name')->pluck('name', 'id');
        $data['matchedInstitutions'] = [];
        $data['mailing_categories']  = ContactMailingCategory::get();
        $data['interest_sections']   = InterestSection::orderBy('key', 'desc')->get();
        $data['organization_types']  = OrganisationType::orderBy('key')->pluck('label', 'key');
        $data['organization_levels'] = OrganisationLevel::get();
        $data['infoStatuses']        = OrganisationInfoStatus::get();
        $data['associations']        = Association::orderBy('key')->get();
        $data['allOffer']            = Offer::orderByDesc(DB::raw('YEAR(created_at)'))->orderByDesc('offer_number')->get();
        $data['is_send']             = $request->is_send ?? 0;
        $data['price_type']          = ShipmentTerms::get();
        $data['confirm_options']     = ConfirmOptions::get();
        $data['admins']              = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $data['companies']           = BankAccountOwner::get();
        $data['classes']             = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');
        $data['origin']              = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $data['areas']               = AreaRegion::pluck('name', 'id');
        $data['surplus_status']      = SurplusStatus::get();
        $data['actions']             = TaskActions::get();
        $data['users']               = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $data['calendar_view']       = false;
        $data['user_token']          = EmailToken::where('user_id', Auth::id())->get()->count();
        $data['email_from']          = 'info@zoo-services.com';
        $data['general_signature']   = '<br><br>' . view('emails.email-signature')->render();
        $data['email_body']          = $data['general_signature'];
        $data['label']               = $request->label ?? 'false';
        $data['default_text']        = DefaultTextTask::orderBy('text', 'ASC')->get();
        $data['std_text']            = StdText::where('category','email')->orderBy('name', 'ASC')->get();
        $dashboards                  = Dashboard::where('main', 1)->orderBy('order', 'ASC')->get();
        $html                        = '<div class="custom-dd dd" id="nestable_list_1">
                    <ol class="dd-list">
                        ';
        $html = $this->getHtmlDashboarSon($dashboards, $html);
        $html .= '</div>
        </ol>
            ';

        $data['dashboards'] = $html;
        $data['colors'] = Color::all();

        if (session()->has('inbox.filter')) {
            $filter = session('inbox.filter');
        }
        $data['sortselected'] = !empty($filter['sortselected']) ? $filter['sortselected'] : '';

        return view('inbox.index', $data);
    }


    public function emailDashboard(Request $request)
    {
        $data = $this->get_data_by_request($request);
        $user = User::where('id', Auth::id())->first();
        $roles = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $data["labels"] = Labels::orderBy("title", "ASC")->get();
        $data["directories"] = Directorie::orderBy("title", "ASC")->get();
        $data["countries"] = Country::orderBy('name')->pluck('name', 'id');
        $data["matchedInstitutions"] = [];
        $data["mailing_categories"] = ContactMailingCategory::get();
        $data["interest_sections"] = InterestSection::orderBy('key', 'desc')->get();
        $data["organization_types"] = OrganisationType::orderBy('key')->pluck('label', 'key');
        $data["organization_levels"] = OrganisationLevel::get();
        $data["infoStatuses"] = OrganisationInfoStatus::get();
        $data["associations"] = Association::orderBy('key')->get();
        $data["allOffer"] = Offer::orderByDesc(DB::raw("YEAR(created_at)"))->orderByDesc('offer_number')->get();
        $data["is_send"] = $request->is_send ?? 0;
        $data["price_type"] = ShipmentTerms::get();
        $data["confirm_options"] = ConfirmOptions::get();
        $data["admins"] = User::whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $data["companies"] = BankAccountOwner::get();
        $data["classes"] = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');
        $data["origin"] = Origin::orderBy("id", "ASC")->pluck("name", "short_cut");
        $data["areas"] = AreaRegion::pluck('name', 'id');
        $data["surplus_status"] = SurplusStatus::get();
        $data["actions"] = TaskActions::get();
        $data["users"] = User::orderBy('name')->whereRoleIs(Arr::pluck($roles, 'name'))->pluck('name', 'id');
        $data["calendar_view"] = false;
        $data["user_token"] = EmailToken::where("user_id", Auth::id())->get()->count();
        $data["email_from"] = "info@zoo-services.com";
        $data['general_signature'] = '<br><br>' . view('emails.email-signature')->render();
        $data['email_body'] = $data['general_signature'];
        $data["label"] = $request->label ?? "false";
        $data["default_text"] = DefaultTextTask::orderBy('text', "ASC")->get();
        $data['std_text'] = StdText::where('category','email')->orderBy('name', 'ASC')->get();
        $dashboards = Dashboard::where("main", 1)->orderBy("order", "ASC")->get();
        $html = '<div class="custom-dd dd" id="nestable_list_1">
                    <ol class="dd-list">
                        ';
        $html = $this->getHtmlDashboarSon($dashboards, $html);
        $html .= '</div>
        </ol>
            ';

        $data['dashboards'] = $html;
        $data['colors'] = Color::all();

        return view('inbox.dashboard_inbox', $data);
    }

    protected function updateEmailStatus($emails, $acount_show)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        $userToken = $GraphService->getAllUserToken();
        if (!empty($userToken)) {
            foreach ($userToken as $row) {
                $token   = $GraphService->getUserToken($row['id'], json_decode($row['token']));
                $user_id = $GraphService->getUserByEmail($token, $acount_show);
                if (!empty($token)) {
                    foreach ($emails as $value) {
                        $result = $GraphService->getEmailInfo($token, $user_id->getId(), $value['guid']);
                        if (empty($result)) {
                            $email            = Email::find($value['id']);
                            $email['archive'] = 1;
                            $email->save();
                        }
                    }
                }
            }
        }
    }

    public function get_data_by_request($request)
    {
        if (session()->has('inbox.filter')) {
            $filter = session('inbox.filter');
        } else {
            $filter = [];
        }

        if ($request->acount_show) {
            $filter['acount_show'] = $request->acount_show;
        } elseif (empty($request->acount_show) && empty($filter) && empty($filter['acount_show'])) {
            $filter['acount_show'] = 'info@zoo-services.com';
        } else {
        }

        $user               = User::where('id', Auth::id())->first();
        $data['test_token'] = EmailToken::where('user_id', $user->id)->where('email', 'test@zoo-services.com')->get()->count();
        if ($data['test_token'] == 0) {
            $data['test_token'] = -1;
        }

        $permission = $user->hasPermission('inbox.info');
        $acount     = [];
        array_push($acount, 'info@zoo-services.com');
        array_push($acount, $user->email);

        if($user->hasRole('admin') && $data["test_token"] > 0){
            array_push($acount, "test@zoo-services.com");
        }
        $data['page_number'] = (!empty($request->page)) ? $request->page : 1;
        if(!empty($request->archive)){
            $data["type_page"] = "archive";
        }elseif(!empty($request->is_delete)){
            $data["type_page"] = "deleteditems";
        }elseif(!empty($request->is_send)){
            $data["type_page"] = "sentitems";
        }elseif(!empty($request->is_draft)){
            $data["type_page"] = "drafts";
        }elseif(!empty($request->is_spam)){
            $data["type_page"] = "junkemail";
        }else{
            $data["type_page"] = "inbox";
        }

        if (isset($filter) && isset($filter['acount_show'])) {
            $data['acount_show'] = $filter['acount_show'];
        }

        $data['acount'] = $acount;

        if (isset($filter) && isset($filter['sortselected'])) {
            $data['sortselected'] = $filter['sortselected'];
        }

        $this->getUpdateEmail($request, $data);

        if (isset($filter) && isset($filter['recordsPerPage'])) {
            $paginate = $filter['recordsPerPage'];
        } else {
            $paginate = 50;
        }

        $email = Email::query();

        $filterData = [];

        if (isset($filter['filter_email'])) {
            $filterData = Arr::add($filterData, 'filter_email', 'Email: ' . $filter['filter_email']);

            $email->where('from_email', 'like', '%' . $filter['filter_email'] . '%');
        }
        if (isset($filter['filter_keyword'])) {
            $filterData = Arr::add($filterData, 'filter_keyword', 'Keyword: ' . $filter['filter_keyword']);

            $email->where('subject', 'like', '%' . $filter['filter_keyword'] . '%')
                  ->orWhere('body_sumary', 'like', '%' . $filter['filter_keyword'] . '%');
        }

        if (isset($filter['filter_cc'])) {
            $data['filter_cc'] = $filter['filter_cc'];

            if ($filter['filter_cc'] == 'false') {
                $email->where(function ($query) use ($filter) {
                    $query->where('to_recipients', $filter['acount_show']);
                    $query->orWhereNull('to_recipients');
                    $query->orWhere('to_recipients', '');
                });
            }
        }

        session(['inbox.filter' => $filter]);

        if (!empty($data['sortselected'])) {
            if ($data['sortselected'] === 'attachment') {
               $email->whereRaw('id IN (SELECT email_id from attachments)');
               $email->orderBy('created_at', 'DESC');
            } elseif ($data['sortselected'] === 'unread') {
               $email->orderBy('is_read' , 'DESC');
               $email->orderBy('created_at', 'DESC');
            } else {
               $email->orderBy('created_at', 'DESC');
            }
        } else {
           $email->orderBy('created_at', 'DESC');
        }

        if($request->hasAny(['label', 'directorie', 'is_delete', 'is_send', 'archive', 'is_draft', 'is_spam']) || $request->type_page == "archive" || $request->type_page == "deleteditems" || $request->type_page == "drafts" || $request->type_page == "sentitems" || $request->type_page == "junkemail") {
            if(!empty($request->label)) {
                $email->whereHas('labels', function ($query) use ($request) {
                    $query->where('labels_id', $request->label);
                });
                $data['emails'] = $email->where('to_email', $data['acount_show'])->where('is_spam', '!=', 1)->where('is_delete', '!=', 1)->paginate($paginate)->appends($request->except('page'));
            }
            if (!empty($request->directorie)) {
                $email->whereHas('directory', function ($query) use ($request) {
                    $query->where('id', $request->directorie);
                });
                $data['emails'] = $email->where('to_email', $data['acount_show'])->paginate($paginate)->appends($request->except('page'));
            }
            if (!empty($request->is_delete) || $request->type_page == 'deleteditems') {
                $email->where('is_delete', 1);
                $data['emails'] = $email->where('to_email', $data['acount_show'])->paginate($paginate)->appends($request->except('page'));
            }
            if (!empty($request->is_draft) || $request->type_page == 'drafts') {
                $email->where('is_draft', 1);
                $data['emails'] = $email->where('from_email', $data['acount_show'])->paginate($paginate)->appends($request->except('page'));
            }
            if (!empty($request->is_send) || $request->type_page == 'sentitems') {
                $email->where('is_send', 1);
                $data['emails'] = $email->where('from_email', $data['acount_show'])->paginate($paginate)->appends($request->except('page'));
            }
            if (!empty($request->archive) || $request->type_page == 'archive') {
                $email->where('archive', 1);
                $data['emails'] = $email->where('to_email', $data['acount_show'])->paginate($paginate)->appends($request->except('page'));
            }
            if(!empty($request->is_spam) || $request->type_page == "junkemail") {
                $email->where("is_spam", 1);
                $data['emails'] = $email->where('to_email', $data['acount_show'])->paginate($paginate)->appends($request->except('page'));
            }

            $data["filterData"] = $filterData;
            $data["total_unread"] = $email->where("is_delete", 0)->where("is_draft", 0)->where("is_spam", 0)->where("to_email", $data["acount_show"])->where("is_send", 0)->where("archive", 0)->whereNull("directorie_id")->where('is_read', 1)->count();
            return $data;
        }

        $data["filterData"] = $filterData;
        $data["emails"] =  $email->where("is_delete", 0)->where("is_spam", 0)->where("is_send", 0)->where("is_draft", 0)->where("to_email", $data["acount_show"])->where("archive", 0)->whereNull("directorie_id")->orderBy('created_at', "DESC")->paginate($paginate)->appends($request->except('page'));
        $data["total_unread"] = Email::where("is_delete", 0)->where("is_draft", 0)->where("is_spam", 0)->where("to_email", $data["acount_show"])->where("is_send", 0)->where("archive", 0)->whereNull("directorie_id")->where('is_read', 1)->count();
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
                $email = Email::findOrFail($id);

                $GraphService = new GraphService();
                $GraphService->initializeGraphForUserAuth();

                $userToken = $GraphService->getAllUserToken();
                if (!empty($userToken)) {
                    foreach ($userToken as $row) {
                        $token   = $GraphService->getUserToken($row['id'], json_decode($row['token']));
                        $user_id = $GraphService->getUserByEmail($token, $request['account']);
                        if (!empty($token)) {
                            if (empty($email['guid'])) {
                                $email->delete();
                            }else{
                                $result = $GraphService->updateDelete($token,  $user_id->getId(), $email["guid"]);
                                if(!empty($result)){
                                    $email["is_delete"] = 1;
                                    $email["archive"] = 0;
                                    $email["is_send"] = 0;
                                    $email["is_draft"] = 0;
                                    $email["is_spam"] = 0;
                                    $email["guid"] = $result["id"];
                                    $email->save();

                                    if(!empty($email->remind_email_id)){
                                        $email_remind = Email::find($email->remind_email_id);
                                        $email_remind["is_remind"] = 0;
                                        $email_remind->save();
                                    }
                                }else{
                                    return response()->json(['error' =>true, 'message' => "The email was not delete successfully"]);
                                }
                            }
                        }
                    }
                }
            }

            return response()->json(['error' => false, 'message' => 'The email was dalete successfully']);
        } else {
            return response()->json(['error' => false, 'message' => 'The email was not dalete successfully']);
        }
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_address_items(Request $request)
    {
        if (count($request->items) > 0) {
            $GraphService = new GraphService();
            $GraphService->initializeGraphForUserAuth();
            $user_login = Auth::user();
            foreach ($request->items as $id) {
                $email   = Email::findOrFail($id);
                $address = Email::where('from_email', $email['from_email'])->where('is_delete', 0)->get();
                if (!empty($address)) {
                    foreach ($address as $addres) {
                        $addres['delete_email_address'] = json_encode(['user' => $user_login->id, 'account' => $request->account]);
                        $addres->save();
                    }
                }
            }

            return response()->json(['error' => false, 'message' => 'The deletion of emails from deleted email addresses has been scheduled.']);
        } else {
            return response()->json(['error' => false, 'message' => 'The email was not dalete successfully']);
        }
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function archiveItems(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $email        = Email::find($id);
                $GraphService = new GraphService();
                $GraphService->initializeGraphForUserAuth();

                $userToken = $GraphService->getAllUserToken();
                if(!empty($userToken)){
                    foreach ($userToken as $row){
                        $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                        $user_id = $GraphService->getUserByEmail($token, $request["account"]);
                        if(!empty($token)){
                            $result = $GraphService->updateArchive($token,  $user_id->getId(), $email["guid"]);
                            if(!empty($result)){
                                $email["archive"] = 1;
                                $email["is_draft"] = 0;
                                $email["is_spam"] = 0;
                                $email["is_delete"] = 0;
                                $email["guid"] = $result["id"];
                                $email->save();
                                if(!empty($email->remind_email_id)){
                                    $email_remind = Email::find($email->remind_email_id);
                                    $email_remind["is_remind"] = 0;
                                    $email_remind->save();
                                }
                            }else{
                                return response()->json(['error' =>true, 'message' => "The email was not archived successfully"]);
                            }
                        }
                    }
                }
            }

            return response()->json(['error' =>false, 'message' => "The email was archived successfully"]);
        }else{
            return response()->json(['error' =>true, 'message' => "The email was not archived successfully"]);
        }
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSpam(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $email = Email::find($id);
                $GraphService = new GraphService();
                $GraphService->initializeGraphForUserAuth();

                $userToken = $GraphService->getAllUserToken();
                if(!empty($userToken)){
                    foreach ($userToken as $row){
                        $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                        $user_id = $GraphService->getUserByEmail($token, $request["account"]);
                        if(!empty($token)){
                            $result = $GraphService->updateSpam($token,  $user_id->getId(), $email["guid"]);
                            if(!empty($result)){
                                $email["is_spam"] = 1;
                                $email["is_draft"] = 0;
                                $email["archive"] = 0;
                                $email["is_delete"] = 0;
                                $email["guid"] = $result["id"];
                                $email->save();

                                if(!empty($email->remind_email_id)){
                                    $email_remind = Email::find($email->remind_email_id);
                                    $email_remind["is_remind"] = 0;
                                    $email_remind->save();
                                }
                                foreach($email->labels as $label){
                                    $label = Labels::find($label);
                                    $email->labels()->detach($label);
                                }

                            }else{
                                return response()->json(['error' =>true, 'message' => "The email was not archived successfully"]);
                            }
                        }
                    }
                }
            }

            return response()->json(['error' => false, 'message' => 'The email was archived successfully']);
        } else {
            return response()->json(['error' => true, 'message' => 'The email was not archived successfully']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUpdateEmail(Request $request, $data = [])
    {
        if (!empty($data)) {
            $request = $data;
        }
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        $user_acount = User::find(Auth::user()->id);
        if (!empty($user_acount)) {
            $userToken = $GraphService->getAllUserToken($user_acount->id);
        } else {
            $userToken = $GraphService->getAllUserToken();
        }

        if (!empty($userToken)) {
            $email_save = false;
            foreach ($userToken as $row){
                $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                $user_id = $GraphService->getUserByEmail($token, $request["acount_show"]);
                if(!empty($token) && !empty($user_id)){
                    if($request["type_page"] == "archive"){
                        $last_email = Email::where("to_email", $request["acount_show"])->where("archive", 1)->orderBy("created_at", "DESC")->first();
                    }elseif($request["type_page"] == "deleteditems"){
                        $last_email = Email::where("to_email", $request["acount_show"])->where("is_delete", 1)->orderBy("created_at", "DESC")->first();
                    }elseif($request["type_page"] == "drafts"){
                        $last_email = Email::where("to_email", $request["acount_show"])->where("is_draft", 1)->orderBy("created_at", "DESC")->first();
                    }elseif($request["type_page"] == "sentitems"){
                        $last_email = Email::where("to_email", $request["acount_show"])->where("is_send", 1)->orderBy("created_at", "DESC")->first();
                    }elseif($request["type_page"] == "junkemail"){
                        $last_email = Email::where("to_email", $request["acount_show"])->where("is_spam", 1)->orderBy("created_at", "DESC")->first();
                    }else{
                        $last_email = Email::where("to_email", $request["acount_show"])->where("archive", 0)->where("is_spam", 0)->where("is_send", 0)->where("is_draft", 0)->where("is_delete", 0)->orderBy("created_at", "DESC")->first();
                    }
                    if (!empty($last_email)) {
                        $date_start = $last_email['created_at'];
                        $last_date  = date('Y-m-dTH:i', strtotime($date_start));
                        $last_date  = str_replace('UTC', 'T', (string) $last_date) . 'Z';
                    } else {
                        $last_date = '';
                    }

                    $messages = $GraphService->getUnreadInboxByUserText($token, $user_id->getId(), $last_date, $request["type_page"]);

                    if (!empty($messages)) {
                        try {
                            foreach ($messages->getPage() as $message) {
                                $email       = [];
                                $exist_email = Email::where('guid', $message->getId())->first();

                                if (empty($exist_email)) {
                                    $email_save = true;
                                    if (!empty($message->getFrom())) {
                                        if (strpos($message->getFrom()->getEmailAddress()->getAddress(), '@') === 0) {
                                            $email['from_email'] = $message->getFrom()->getEmailAddress()->getAddress();
                                        } else {
                                            $email['from_email'] = substr($message->getFrom()->getEmailAddress()->getAddress(), 0, 100);
                                        }
                                        $email['name'] = $message->getFrom()->getEmailAddress()->getName();
                                    } else {
                                        $email['from_email'] = $request['acount_show'];
                                        $email['name']       = '';
                                    }

                                    $body = $message->getBody()->getContent();
                                    $body = str_replace('\r\n', "", $body);
                                    $email["body"] = "";
                                    $email["body_sumary"] = utf8_encode(substr($body, 0, 100));
                                    $email["guid"] = $message->getId();
                                    $email["subject"] = utf8_encode($message->getSubject() ?? "");
                                    $email["created_at"] = $message->getReceivedDateTime();
                                    $email["updated_at"] = $message->getReceivedDateTime();
                                    $email["to_email"] = $request["acount_show"];
                                    $email["to_recipients"] = $message->getToRecipients()[0]["emailAddress"]["address"] ?? "";
                                    $cc_email = "";

                                    // Recipients can be more than 1
                                    $to_recipients = [];
                                    if(!empty($message->getToRecipients()[0])){
                                       foreach($message->getToRecipients() as $torecipient){
                                          $to_recipients[] = $torecipient['emailAddress']['address'];
                                       }
                                    }
                                    $email['to_recipients'] = implode(';', $to_recipients);

                                    $cc_email = [];
                                    if(!empty($message->getCcRecipients()[0])){
                                        foreach($message->getCcRecipients() as $CcRecipient){
                                           $cc_email[] = $CcRecipient['emailAddress']['address'];
                                        }
                                    }

                                    $email['cc_email'] = implode(';', $cc_email);

                                    if (!empty($email['from_email']) && !empty($message->getFrom())) {
                                        $contact      = Contact::where('email', $message->getFrom()->getEmailAddress()->getAddress())->first();
                                        $organisation = Organisation::where('email', $message->getFrom()->getEmailAddress()->getAddress())->first();
                                        $user         = User::where('email', $message->getFrom()->getEmailAddress()->getAddress())->first();
                                        if (!empty($contact)) {
                                            $email['contact_id']      = $contact['id'];
                                            $email['organisation_id'] = $contact['organisation_id'];
                                        }
                                        if (!empty($organisation)) {
                                            $email['organisation_id'] = $organisation['id'];
                                            if (!empty($organisation->contacts)) {
                                                foreach ($organisation->contacts as $row_contact) {
                                                    if ($row_contact->email == $email['from_email']) {
                                                        $email['contact_id'] = $row_contact->id;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $label_offer  = false;
                                    $label_oder   = false;
                                    $label_surplu = false;
                                    $label_wanted = false;
                                    $label_task   = false;
                                    if (!empty($email['subject'])) {
                                        if (strpos($email['subject'], 'offer') === false && strpos($email['subject'], 'Offer') === false) {
                                        } else {
                                            $subject = explode(' ', $email['subject']);
                                            foreach ($subject as $value) {
                                                $offer_filter = explode('-', $value);
                                                if (count($offer_filter) > 1) {
                                                    $offer = Offer::whereYear('created_at', '=', $offer_filter[0])->where('offer_number', $offer_filter[1])->first();
                                                    if (!empty($offer)) {
                                                        $email['offer_id'] = $offer['id'];
                                                        $label_offer       = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if (strpos($email['subject'], '#OF') === false) {
                                        } else {
                                            $subject = explode('#', $email['subject']);
                                            if (!empty($subject[1])) {
                                                $offer_filter = explode('-', $subject[1]);
                                                if (count($offer_filter) > 1) {
                                                    $offer = Offer::whereYear('created_at', '=', $offer_filter[1])->where('offer_number', $offer_filter[2])->first();
                                                    if (!empty($offer)) {
                                                        $email['offer_id'] = $offer['id'];
                                                        $label_offer       = true;
                                                    }
                                                }
                                            }
                                        }
                                        if (strpos($email['subject'], 'order') === false && strpos($email['subject'], 'Order') === false) {
                                        } else {
                                            $subject = explode(' ', $email['subject']);
                                            foreach ($subject as $value) {
                                                $order_filter = explode('-', $value);
                                                if (count($order_filter) > 1) {
                                                    $order = Order::whereYear('created_at', '=', $order_filter[0])->where('order_number', $order_filter[1])->first();
                                                    if (!empty($order)) {
                                                        $email['order_id'] = $order['id'];
                                                        if (!empty($order['offer_id'])) {
                                                            $email['offer_id'] = $order['offer_id'];
                                                            $label_offer       = true;
                                                        }
                                                        $label_oder = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if (strpos($email['subject'], '#OR') === false) {
                                        } else {
                                            $subject = explode('#', $email['subject']);
                                            if (!empty($subject[1])) {
                                                $order_filter = explode('-', $subject[1]);
                                                if (count($order_filter) > 1) {
                                                    $order = Order::whereYear('created_at', '=', $order_filter[1])->where('order_number', $order_filter[2])->first();
                                                    if (!empty($order)) {
                                                        $email['order_id'] = $order['id'];
                                                        if (!empty($order['offer_id'])) {
                                                            $email['offer_id'] = $order['offer_id'];
                                                            $label_offer       = true;
                                                        }
                                                        $label_oder = true;
                                                    }
                                                }
                                            }
                                        }
                                        if (strpos($email['subject'], '#SU') === false) {
                                        } else {
                                            $subject = explode('#', $email['subject']);
                                            if (!empty($subject[1])) {
                                                $filter = explode('-', $subject[1]);
                                                if (count($filter) > 1) {
                                                    $result = Surplus::find($filter[1]);
                                                    if (!empty($result)) {
                                                        $email['surplu_id'] = $result['id'];
                                                        $label_surplu       = true;
                                                    }
                                                }
                                            }
                                        }
                                        if (strpos($email['subject'], '#WA') === false) {
                                        } else {
                                            $subject = explode('#', $email['subject']);
                                            if (!empty($subject[1])) {
                                                $filter = explode('-', $subject[1]);
                                                if (count($filter) > 1) {
                                                    $result = Wanted::find($filter[1]);
                                                    if (!empty($result)) {
                                                        $email['wanted_id'] = $result['id'];
                                                        $label_wanted       = true;
                                                    }
                                                }
                                            }
                                        }

                                        if(strpos($email["subject"], "#RM") === false){
                                        }else{
                                            $subject = explode("#R", $email["subject"]);
                                            if(!empty($subject[1])){
                                                $filter = explode("-", $subject[1]);
                                                if(count($filter) > 1){
                                                    $result = Email::find($filter[1]);
                                                    if(!empty($result)){
                                                        $email["task_id"] = $result["task_id"] ?? null;
                                                        $label_task = true;
                                                        $email["offer_id"] = $result["offer_id"] ?? null;
                                                        $label_offer = true;
                                                        $email["remind_email_id"] = $result["id"] ?? null;
                                                        $email["remind_due_date"] = $result["remind_due_date"] ?? null;
                                                    }
                                                }
                                            }
                                        }
                                        $filter_color = Color::whereNotNull("filter_email")->get();
                                        if(!empty($filter_color)){
                                            foreach($filter_color as $color_row){
                                                if(strpos($email["subject"], $color_row["filter_email"]) === false){
                                                }else{
                                                    $email["color_id"] = $color_row["id"];
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    $email_new = Email::create($email);

                                    if (strpos($email['subject'], '#TS') === false) {
                                    } else {
                                        $subject = explode('#', $email['subject']);
                                        if (!empty($subject[1])) {
                                            $filter = explode('-', $subject[1]);
                                            if (count($filter) > 1) {
                                                $result = Task::find($filter[1]);
                                                if (!empty($result)) {
                                                    $email_new->tasks()->attach($result);
                                                    $label_task       = true;
                                                }
                                            }
                                        }
                                    }

                                    $Attachments = $GraphService->getAttachmentsByEmail($token, $user_id->getId(), $email_new["guid"])->getPage();

                                    if(!empty($Attachments)){
                                        foreach ($Attachments as $key => $value) {
                                            if($value->getIsInline() == false && $value->getName() != "image001.jpg"){
                                                $data_attachments = new Attachment();
                                                $data_attachments["email_id"] = $email_new["id"];
                                                $data_attachments["guid"] = $value->getId();
                                                $data_attachments["name"] = $value->getName();
                                                $data_attachments["type"] = $value->getContentType();
                                                $data_attachments->save();
                                            }
                                        }
                                    }

                                    if($request["type_page"] == "archive"){
                                        $email_new["archive"] = 1;
                                    }
                                    if ($request['type_page'] == 'deleteditems') {
                                        $email_new['is_delete'] = 1;
                                    }
                                    if ($request['type_page'] == 'drafts') {
                                        $email_new['body']              = $message->getBody()->getcontent();
                                        $email_new['is_draft']          = 1;
                                        $email_new['type_draft']        = 'new';
                                        $email_new['to_email']          = $message->getToRecipients()[0]['emailAddress']['address'] ?? '';
                                        $Attachments                    = $GraphService->getAttachmentsByEmail($token, $user_id->getId(), $email['guid'])->getPage();
                                        $html_attachments               = $this->getHtmlAttachment($Attachments, $email_new, true);
                                        $email_new['attachments_draft'] = $html_attachments;
                                    }
                                    if ($request['type_page'] == 'sentitems') {
                                        $email_new['is_send']  = 1;
                                        $email_new['to_email'] = $message->getToRecipients()[0]['emailAddress']['address'] ?? '';
                                    }
                                    if($request["type_page"] == "junkemail"){
                                        $email_new["is_spam"] = 1;
                                    }

                                    $email_new->save();

                                    if (empty($organisation) && empty($contact) && empty($user)) {
                                        $label = Labels::where('name', 'new_contact')->first();
                                        $email_new->labels()->attach($label);
                                    }

                                    if ($label_offer) {
                                        $label = Labels::where('name', 'offer')->first();
                                        $email_new->labels()->attach($label);
                                    }

                                    if ($label_oder) {
                                        $label = Labels::where('name', 'order')->first();
                                        $email_new->labels()->attach($label);
                                    }

                                    if ($label_surplu) {
                                        $label = Labels::where('name', 'surplus')->first();
                                        $email_new->labels()->attach($label);
                                    }

                                    if ($label_wanted) {
                                        $label = Labels::where('name', 'wanted')->first();
                                        $email_new->labels()->attach($label);
                                    }

                                    if ($label_task) {
                                        $label = Labels::where('name', 'task')->first();
                                        $email_new->labels()->attach($label);
                                    }

                                    $filter_label = Labels::whereNotNull("filter_email")->get();
                                    if(!empty($filter_label)){
                                        foreach($filter_label as $label_row){
                                            if(strpos($email_new["subject"], $label_row["filter_email"]) === false){
                                            }else{
                                                $label = Labels::find($label_row["id"]);
                                                $email_new->labels()->attach($label);
                                            }
                                        }
                                    }
                                }else{
                                    if($request["type_page"] == "deleteditems"){
                                        $exist_email_delete = Email::where("guid", $message->getId())->where("is_delete", 0)->first();
                                        if(!empty($exist_email_delete)){
                                            $$exist_email_delete["is_delete"] = 1;
                                            $$exist_email_delete->save();
                                            $email_save = true;
                                        }
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                        }
                    }
                } else {
                    $html['error']   = true;
                    $html['content'] = '';

                    return json_encode($html);
                }
            }

            if ($email_save) {
                if (!is_array($request) && $request->ajax()) {
                    $data                 = $this->get_data_by_request($request);
                    $view                 = View::make('inbox.table', $data);
                    $html['error']        = false;
                    $html['content']      = $view->render();
                    $html['total_unread'] = $data['total_unread'];

                    return json_encode($html);
                } else {
                    $html['error']   = true;
                    $html['content'] = '';

                    return json_encode($html);
                }
            } else {
                $html['error']   = true;
                $html['content'] = '';

                return json_encode($html);
            }
        } else {
            $html['error']   = true;
            $html['content'] = '';

            return json_encode($html);
        }
    }

    public function downloadAttachment($email_guid, $attachment_id, $to_email)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $userToken = $GraphService->getAllUserToken();
        if (!empty($userToken)) {
            foreach ($userToken as $row) {
                $token   = $GraphService->getUserToken($row['id'], json_decode($row['token']));
                $user_id = $GraphService->getUserByEmail($token, $to_email);
                if (!empty($token)) {
                    $attachment = $GraphService->getAttachmentsInfoByEmail($token, $user_id->getId(), $email_guid, $attachment_id)->getPage();
                    if (!empty($attachment)) {
                        $path     = public_path() . '/' . $attachment->getname();
                        $contents = base64_decode($attachment->getContentBytes());

                        //store file temporarily
                        file_put_contents($path, $contents);

                        //download file and delete it
                        return response()->download($path)->deleteFileAfterSend(true);
                    } else {
                        return redirect()->back()->with('error', 'Failed to download attachment');
                    }
                }
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function updateIsReaad(Request $request)
    {
        if ($request->ajax()) {
            $GraphService = new GraphService();
            $GraphService->initializeGraphForUserAuth();

            $email = Email::find($request['id']);
            if ($email['is_read'] === 1) {
                $email['is_read'] = 0;
                $email->save();
                if ($email['is_read'] != 1) {
                    $userToken = $GraphService->getAllUserToken();
                    if (!empty($userToken)) {
                        foreach ($userToken as $row) {
                            $token   = $GraphService->getUserToken($row['id'], json_decode($row['token']));
                            $user_id = $GraphService->getUserByEmail($token, $email['to_email']);
                            if (!empty($token)) {
                                $result = $GraphService->updateIsReadEmailInbox($token, $user_id->getId(), $email['guid']);
                            }
                        }
                    }
                }
                return 1;
            }
        }
        return 0;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getBodyEmail(Request $request)
    {
        if ($request->ajax()) {
            $GraphService = new GraphService();
            $GraphService->initializeGraphForUserAuth();

            $email = Email::find($request['id']);
            if (!empty($email['body'])) {
                $result = $email['body'];
                $html   = '';
                if (!empty($email['attachments'])) {
                    $Attachments = $email['attachments'];
                    foreach ($Attachments as $key => $value) {
                        $data_attachments[$key]["name"] = $value["name"];
                        $data_attachments[$key]["path"] = $value["path"];
                        $data_attachments[$key]["id"] = $value["id"];
                    }
                    if(!empty($data_attachments)){
                        $data["data_attachments"] = $data_attachments;
                        $data["email_guid"] = $email["guid"];
                        $data["to_email"] = $email["to_email"];
                        $view = View::make('inbox.item_attachments', $data);
                        $html = $view->render();
                    }
                }
                return response()->json(['error' =>false, 'body' => $result, 'email' => $email, 'attachments' => $html]);
            }else{
                if(!empty($email)){
                    $userToken = $GraphService->getAllUserToken();
                    if(!empty($userToken)){
                        foreach ($userToken as $row){
                            $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                            $user_id = $GraphService->getUserByEmail($token, $email["to_email"]);
                            if(!empty($token)){
                                $result = $GraphService->getEmailInfo($token,  $user_id->getId(), $email["guid"]);
                                if(!empty($result)){
                                    $Attachments = $GraphService->getAttachmentsByEmail($token, $user_id->getId(), $email["guid"])->getPage();
                                    $html = $this->getHtmlAttachment($Attachments, $email);
                                    return response()->json(['error' =>false, 'body' => $result->getBody()->getContent(),  'email' => $email, 'attachments' => $html]);
                                }else{
                                    return response()->json(['error' =>true]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getBodyEmailSumary($data)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $email = Email::find($data["id"]);
        if(!empty($email)){
            $userToken = $GraphService->getAllUserToken();
            if(!empty($userToken)){
                foreach ($userToken as $row){
                    $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                    $user_id = $GraphService->getUserByEmail($token, $email["to_email"]);
                    if(!empty($token)){
                        $result = $GraphService->getEmailInfoText($token,  $user_id->getId(), $email["guid"]);
                        if(!empty($result)){
                            return $result->getBody()->getContent();
                        }else{
                            return "";
                        }
                    }
                }
            }
        }
    }

    public function getHtmlAttachment($Attachments, $email, $draft = false){
        if(!empty($Attachments)){
            $data_attachments = [];
            $html             = '';
            if ($draft) {
                $attachments_save = Attachment::where('guid', $email['guid'])->get();
                if (!empty($attachments_save)) {
                    foreach ($attachments_save as $key => $row) {
                        $data_attachments         = [];
                        $data_attachments['id']   = $row['id'];
                        $data_attachments['name'] = $row['name'];
                        $data_attachments['path'] = $row['path'];

                        $data['data_attachments'] = $data_attachments;
                        $data['email_guid']       = $email['guid'];
                        $data['to_email']         = $email['to_email'];
                        $view                     = View::make('inbox.item_attachments_new', $data);
                        $html .= $view->render();
                    }
                }
            } else {
                foreach ($Attachments as $key => $value) {
                    if ($value->getIsInline() == false && $value->getName() != 'image001.jpg') {
                        $data_attachments[$key]['id']   = $value->getId();
                        $data_attachments[$key]['name'] = $value->getName();
                        $data_attachments[$key]['type'] = $value->getContentType();
                        $data_attachments[$key]['size'] = $this->convertBystes($value->getSize());
                    }
                }
                if (!empty($data_attachments)) {
                    $data['data_attachments'] = $data_attachments;
                    $data['email_guid']       = $email['guid'];
                    $data['to_email']         = $email['to_email'];
                    $view                     = View::make('inbox.item_attachments', $data);
                    $html                     = $view->render();
                } else {
                    $html = '';
                }
            }
        } else {
            $html = '';
        }

        return $html;
    }

    public function convertBystes($bytes)
    {
        if ($bytes > 0) {
            $i     = floor(log($bytes) / log(1024));
            $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return sprintf('%.02F', round($bytes / pow(1024, $i), 1)) * 1 . ' ' . @$sizes[$i];
        } else {
            return 0;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function updateEmailsChanges(Request $request)
    {

            if(!empty($request->guids)){
                $GraphService = new GraphService();
                $GraphService->initializeGraphForUserAuth();

                $userToken = $GraphService->getAllUserToken();
                if(!empty($userToken)){
                    foreach ($userToken as $row){
                        $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                        $user_id = $GraphService->getUserByEmail($token, $request->acount_show);
                        if(!empty($token)){
                            $update = false;
                            $directores = InboxDirectories::get();
                            foreach ($request->guids as $key => $value) {
                                if(!empty($value)){
                                    try {
                                        $result = $GraphService->getEmailInfoFolder($token,  $request["type_page"], $user_id->getId(), $value);
                                        $folder = $GraphService->getFoldersName($token,  $user_id->getId(), $request["type_page"]);
                                        if(!empty($result->getParentFolderId())){
                                            if((empty($result) && $result != "false") || (!empty($result) && !empty($folder) && $folder->getId() != $result->getParentFolderId())){
                                                $email = Email::where("guid", $value)->first();
                                                if(!empty($email)){
                                                    $change_email = false;
                                                    foreach ($directores as $directory) {
                                                        $folder_change = $GraphService->getFoldersName($token,  $user_id->getId(), $directory);
                                                        if(!empty($folder_change) && $folder_change->getId() == $result->getParentFolderId()){
                                                            if($directory == "inbox"){
                                                                $email["is_delete"] = 0;
                                                                $email["archive"] = 0;
                                                                $email["is_send"] = 0;
                                                                $email["is_draft"] = 0;
                                                                $email->save();
                                                                $update = true;
                                                                $change_email = true;
                                                            }elseif($directory == "sentitems"){
                                                                $email["is_delete"] = 0;
                                                                $email["archive"] = 0;
                                                                $email["is_send"] = 1;
                                                                $email["is_draft"] = 0;
                                                                $email->save();
                                                                $update = true;
                                                                $change_email = true;
                                                            }elseif($directory == "deleteditems"){
                                                                $email["is_delete"] = 1;
                                                                $email["archive"] = 0;
                                                                $email["is_send"] = 0;
                                                                $email["is_draft"] = 0;
                                                                $email->save();
                                                                $update = true;
                                                                $change_email = true;
                                                            }elseif($directory == "archive"){
                                                                $email["is_delete"] = 0;
                                                                $email["archive"] = 1;
                                                                $email["is_draft"] = 0;
                                                                $email->save();
                                                                $update = true;
                                                                $change_email = true;
                                                            }elseif($directory == "drafts"){
                                                                $email["is_delete"] = 0;
                                                                $email["archive"] = 0;
                                                                $email["is_draft"] = 1;
                                                                $email->save();
                                                                $update = true;
                                                                $change_email = true;
                                                            }else{
                                                                $email["is_delete"] = 1;
                                                                $email["archive"] = 0;
                                                                $email["is_send"] = 0;
                                                                $email["is_draft"] = 0;
                                                                $email->save();
                                                                $update = true;
                                                                $change_email = true;
                                                            }
                                                        }
                                                    }
                                                    if(!$change_email){
                                                        $email["is_delete"] = 1;
                                                        $email["archive"] = 0;
                                                        $email["is_send"] = 0;
                                                        $email["is_draft"] = 0;
                                                        $email->save();
                                                        $update = true;
                                                    }
                                                }
                                            }
                                        }
                                    } catch (\Throwable $th) {
                                        $html['error'] = true;
                                        $html['content'] = "";
                                        return json_encode($html);
                                    }

                                }
                            }

                            if($update){
                                $data = $this->get_data_by_request($request);
                                $view = View::make('inbox.table', $data);
                                $html['error'] = false;
                                $html['content'] = $view->render();
                                $html['total_unread'] = $data['total_unread'];
                                return json_encode($html);
                            }else{
                                $html['error'] = true;
                                $html['content'] = "";
                                return json_encode($html);
                            }
                        }
                    }
                }

            }else{
                $html['error'] = true;
                $html['content'] = "";
                return json_encode($html);
            }

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function updateLabels(Request $request)
    {
        if (count($request->items) > 0) {
            $label = Labels::find($request->label);
            if (!empty($label)) {
                foreach ($request->items as $id) {
                    $email = Email::find($id);
                    $email->labels()->attach($label);
                }

                return response()->json(['error' => false, 'message' => 'The label was updated successfully']);
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function deleteLabels(Request $request)
    {
        if (count($request->items) > 0) {
            $label = Labels::find($request->label);
            if (!empty($label)) {
                foreach ($request->items as $id) {
                    $email = Email::find($id);
                    if ($email) {
                        $email->labels()->detach($label->id);
                    }
                }

                return response()->json(['error' => false, 'message' => 'The label was removed from emails successfully']);
            } else {
                return response()->json(['error' => true, 'message' => 'Label not found']);
            }
        } else {
            return response()->json(['error' => true, 'message' => 'No items provided']);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function updateDirectory(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $email                  = Email::find($id);
                $email['directorie_id'] = $request->directory;
                $email->save();
            }

            return response()->json(['error' => false, 'message' => 'The directory was updated successfully']);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getSelectEmailCreate(Request $request)
    {
        if (!empty($request->items)) {
            $id    = $request->items[0];
            $email = Email::find($id);

            return response()->json(['error' => false, 'email' => $email]);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function addAccount(Request $request)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        $getDeviceCode = $GraphService->getDeviceCode();

        $message = 'To sign in, use a web browser to open the page <a class="text-danger" target="_blank" href="' . $getDeviceCode->verification_uri . '">' . $getDeviceCode->verification_uri . '</a> and enter the code "' . $getDeviceCode->user_code . '" to authenticate.';

        return response()->json(['error' => false, 'device_message' => $message]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function authGraph(Request $request)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        $GraphService->authGraph();

        return response()->json(['error' => false, 'message' => 'The email account has been added successfully']);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function assingOffer(Request $request)
    {
        if (count($request->items) > 0) {
            $items = explode(',', (string) $request->items[0]);
            foreach ($items as $id) {
                $email = Email::find($id);
                if (!empty($email)) {
                    $label             = Labels::where('name', 'offer')->first();
                    $email['offer_id'] = (int) $request->offer_id;
                    $email->labels()->detach($label);
                    $email->labels()->attach($label);
                    $email->save();
                }
            }

            return response()->json(['error' => false, 'message' => 'The email was updated successfully']);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function assingOrder(Request $request)
    {
        if (count($request->items) > 0) {
            $items = explode(',', (string) $request->items[0]);
            foreach ($items as $id) {
                $email             = Email::find($id);
                $label             = Labels::where('name', 'order')->first();
                $email['order_id'] = (int) $request->order_id;
                $email->labels()->detach($label);
                $email->labels()->attach($label);
                $email->save();
            }

            return response()->json(['error' => false, 'message' => 'The email was updated successfully']);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function assingSurplu(Request $request)
    {
        if (count($request->items) > 0) {
            $items = explode(',', (string) $request->items[0]);
            foreach ($items as $id) {
                $email              = Email::find($id);
                $label              = Labels::where('name', 'surplus')->first();
                $email['surplu_id'] = (int) $request->surplu_id;
                $email->labels()->detach($label);
                $email->labels()->attach($label);
                $email->save();
            }

            return response()->json(['error' => false, 'message' => 'The email was updated successfully']);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function assingWanted(Request $request)
    {
        if (count($request->items) > 0) {
            $items = explode(',', (string) $request->items[0]);
            foreach ($items as $id) {
                $email              = Email::find($id);
                $label              = Labels::where('name', 'wanted')->first();
                $email['wanted_id'] = (int) $request->wanted_id;
                $email->labels()->detach($label);
                $email->labels()->attach($label);
                $email->save();
            }

            return response()->json(['error' => false, 'message' => 'The email was updated successfully']);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function changeContact(Request $request)
    {
        if (!empty($request->items)) {
            $email = Email::find($request->items);
            if ($request->type == 'Contact') {
                $email['contact_id'] = (int) $request->change_id;
            } else {
                $email['organisation_id'] = (int) $request->change_id;
            }

            $email->save();

            return response()->json(['error' => false, 'message' => 'The email was updated successfully']);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getOfferAssing(Request $request)
    {
        $bAll = true;
        $user = User::where('id', Auth::id())->first();
        if ($user->hasPermission('offers.see-all-offers')) {
            $offers = Offer::select('*', 'offers.id as offerId', 'offers.created_at as created_date');
        } else {
            $offers = Offer::select('*', 'offers.id as offerId', 'offers.created_at as created_date');
        }

        if (!empty($request['filter_request_number'])) {
            $bAll = false;
            $offers->where('offer_number', $request['filter_request_number']);
        }

        if (!empty($request['filter_offer_year'])) {
            $bAll = false;
            $offers->whereYear('offers.created_at', $request['filter_offer_year']);
        }

        if (!empty($request['filter_country'])) {
            $bAll = false;
            $filterCountry = Country::where('id', $request['filter_country'])->first();
            $offers->where('delivery_country_id', $request['filter_country']);
        }

        if (!empty($request['filter_animal_id'])) {
            $bAll = false;
            $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

            $offers->whereHas('offer_species.oursurplus', function ($query) use ($filterAnimal) {
                $query->where('our_surplus.animal_id', $filterAnimal->id);
            });
        }

        if (!empty($request['filter_client_id'])) {
            $bAll = false;
            $offers->where('client_id', $request['filter_client_id']);

            $offers->orWhere('institution_id', $request['filter_client_id'])->whereNull('client_id');
        }

        if (!empty($request['filter_supplier_id']) && !empty($request['filter_supplier_id'])) {
            $filterSupplier = Contact::where('id', $request['filter_supplier_id'])->first();

            $offers->where('supplier_id', $filterSupplier->id);
        }

        // Only select offers when there are conditions (filters)
        if ($bAll) {
            $html['error']   = true;
            $html['content'] = '';
            return json_encode($html);
        }

        $offers = $offers->get();

        if ($request->ajax() && !empty($offers->toArray())) {
            $data['header_table'] = ['Status', 'Req. No', 'Quant. & Species', 'Client'];
            foreach ($offers as $key => $value) {
                if (!empty($value->status_level)) {
                    if ($value->status_level === 'Forapproval') {
                        $status = '(For&nbsp;approval)';
                    } elseif ($value->status_level === 'Tosearch') {
                        $status = '(To&nbsp;search)';
                    } elseif ($value->status_level === 'Sendoffer') {
                        $status = '(Send&nbsp;Offer)';
                    } else {
                        $status = $value->status_level;
                    }
                }
                if (!empty($value->client)) {
                    $client = ($value->client->organisation && $value->client->organisation->name) ? $value->client->organisation->name : $value->client->full_name;
                    $client = $client . ' (' . $value->client->email . ')';
                } else {
                    if (!empty($value->organisation)) {
                        $client = ($value->organisation && $value->organisation->name) ? $value->organisation->name : '';
                        $client = $client . ' (' . !empty($value->organisation) ? $value->organisation->email : '' . ')';
                    } else {
                        $client = '';
                    }
                }
                $data['body_table'][$key]['id']              = $value->offerId;
                $data['body_table'][$key]['status']          = $status;
                $data['body_table'][$key]['req_no']          = $value->full_number;
                $data['body_table'][$key]['species_ordered'] = $value->species_ordered ?? [];
                $data['body_table'][$key]['client']          = $client;
            }

            $view            = View::make('inbox.table_assing', $data);
            $html['error']   = false;
            $html['content'] = $view->render();

            return json_encode($html);
        } else {
            $html['error']   = true;
            $html['content'] = '';

            return json_encode($html);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getOrderAssing(Request $request)
    {
        $bAll = true;
        $user   = User::where('id', Auth::id())->first();
        $orders = Order::orderBy('created_at', 'DESC');
        //DB::enableQueryLog(); // Enable query log
        if (!empty($request['filter_order_number'])) {
            $bAll = false;
            $orders->where('order_number', $request['filter_order_number']);
        }

        if (!empty($request['filter_order_year'])) {
            $bAll = false;
            $orders->whereYear('orders.created_at', $request['filter_order_year']);
        }

        if (!empty($request['filter_realized_year'])) {
            $bAll = false;
            $orders->whereYear('realized_date', $request['filter_realized_year']);
        }

        if (!empty($request['filter_project_manager'])) {
            $bAll = false;
            $filterUser = User::where('id', $request['filter_project_manager'])->first();

            $orders->where('manager_id', $filterUser['id']);
        }

        if (!empty($request['filter_order_company'])) {
            $bAll = false;
            $orders->where('company', $request['filter_order_company']);
        }

        if (!empty($request['filter_animal_id'])) {
            $bAll = false;
            $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

            $orders->whereHas('offer.offer_species.oursurplus', function ($query) use ($filterAnimal) {
                $query->where('our_surplus.animal_id', $filterAnimal->id);
            });
        }

        if (!empty($request['filter_client_id'])) {
            $bAll = false;
            $filterClient = Contact::where('id', $request['filter_client_id'])->first();

            $orders->where('client_id', $filterClient->id);
        }

        if (!empty($request['filter_supplier_id'])) {
            $bAll = false;
            $filterSupplier = Contact::where('id', $request['filter_supplier_id'])->first();

            $orders->where('supplier_id', $filterSupplier->id);
        }

        // Only select orders when there are conditions (filters)
        if ($bAll) {
            $html['error']   = true;
            $html['content'] = '';
            return json_encode($html);
        }

        $orders = $orders->get();

        if ($request->ajax() && !empty($orders->toArray())) {
            $data['header_table'] = ['Status', 'Order No.', 'Quant. & Species', 'Client'];
            foreach ($orders as $key => $value) {
                if (!empty($value->client)) {
                    $client = ($value->client->organisation && $value->client->organisation->name) ? $value->client->organisation->name : $value->client->full_name;
                    $client = $client . ' (' . $value->client->email . ')';
                } else {
                    if (!empty($value->organisation)) {
                        $client = ($value->organisation && $value->organisation->name) ? $value->organisation->name : '';
                        $client = $client . ' (' . !empty($value->organisation) ? $value->organisation->email : '' . ')';
                    } else {
                        $client = '';
                    }
                }
                $data['body_table'][$key]['id']              = $value->id;
                $data['body_table'][$key]['order_status']    = $value->order_status;
                $data['body_table'][$key]['order_number']    = $value->full_number;
                $data['body_table'][$key]['species_ordered'] = $value->offer->species_ordered ?? [];
                $data['body_table'][$key]['client']          = $client;
            }

            $view            = View::make('inbox.table_assing', $data);
            $html['error']   = false;
            $html['content'] = $view->render();

            return json_encode($html);
        } else {
            $html['error']   = true;
            $html['content'] = '';

            return json_encode($html);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getSurpluAssing(Request $request)
    {
        $surpluses = Surplus::with(['animal'])->where('surplus_status', '<>', 'collection')->orderByDesc('updated_at');
        if (!empty($request['filter_animal_option'])) {
            if ($request['filter_animal_option'] === 'by_id') {
                if (!empty($request['filter_animal_id'])) {
                    $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                    $surpluses->where('animal_id', $filterAnimal->id);
                }
            } elseif ($request['filter_animal_option'] === 'by_name') {
                if (!empty($request['filter_animal_name'])) {
                    $surpluses->whereHas('animal', function ($query) use ($request) {
                        $query->where('common_name', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('common_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('scientific_name', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('scientific_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('spanish_name', 'like', '%' . $request['filter_animal_name'] . '%');
                    });
                }
            } else {
                $surpluses->whereNull('animal_id');
            }
        }

        if (!empty($request['empty_institution'])) {
            $surpluses->whereNull('organisation_id');
        } elseif (!empty($request['filter_institution_id'])) {
            $institutionFilter = Organisation::where('id', $request['filter_institution_id'])->first();

            $surpluses->where('organisation_id', $institutionFilter->id);
        }

        if (!empty($request['empty_contact'])) {
            $surpluses->whereNull('contact_id');
        } elseif (!empty($request['filter_supplier_id'])) {
            $contactFilter = Contact::where('id', $request['filter_supplier_id'])->first();

            $surpluses->where('contact_id', $contactFilter->id);
        }

        if (!empty($request['filter_animal_class'])) {
            $ids_genera = [];

            $class  = Classification::where('id', $request['filter_animal_class'])->first();
            $order  = null;
            $family = null;
            $genus  = null;

            if (!empty($request['filter_animal_order'])) {
                $order = $class->under->where('id', $request['filter_animal_order'])->first();
            }
            if (!empty($request['filter_animal_family'])) {
                $family = $order->under->where('id', $request['filter_animal_family'])->first();
            }
            if (!empty($request['filter_animal_genus'])) {
                $genus = $family->under->where('id', $request['filter_animal_genus'])->first();
            }

            if ($genus != null) {
                array_push($ids_genera, $genus->id);
                $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            } elseif ($family != null) {
                $genera = $family->under->toArray();
                foreach ($genera as $family_genus) {
                    array_push($ids_genera, $family_genus['id']);
                }
                $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            } elseif ($order != null) {
                $families = $order->under->all();
                foreach ($families as $order_family) {
                    $order_family_genera = $order_family->under->toArray();
                    foreach ($order_family_genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                }
                $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            } elseif ($class != null) {
                $orders = $class->under->all();
                foreach ($orders as $class_order) {
                    $class_order_families = $class_order->under->all();
                    foreach ($class_order_families as $class_order_family) {
                        $class_order_family_genera = $class_order_family->under->toArray();
                        foreach ($class_order_family_genera as $family_genus) {
                            array_push($ids_genera, $family_genus['id']);
                        }
                    }
                }
                $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            }
        }

        if (!empty($request['filter_origin'])) {
            if ($request['filter_origin'] === 'empty') {
                $surpluses->whereNull('origin');
            } else {
                $surpluses->where('origin', $request['filter_origin']);
            }
        }

        if (!empty($request['filter_country'])) {
            $filterCountry = Country::where('id', $request['filter_country'])->first();

            if ($request['filter_country'] == 0) {
                $surpluses->whereNull('country_id');
            } else {
                $surpluses->where('country_id', $filterCountry->id);
            }
        }

        if (!empty($request['filter_area'])) {
            $filterArea = AreaRegion::where('id', $request['filter_area'])->first();

            if ($request['filter_area'] == 0) {
                $surpluses->whereNull('area_region_id');
            } else {
                $surpluses->where('area_region_id', $filterArea->id);
            }
        }

        if (!empty($request['filter_surplus_status'])) {
            if ($request['filter_surplus_status'] === 'empty') {
                $surpluses->whereNull('surplus_status');
            } else {
                $surpluses->where('surplus_status', $request['filter_surplus_status']);
            }
        }

        if (!empty($request['filter_have_cost_prices'])) {
            if ($request['filter_have_cost_prices'] == 'yes') {
                $surpluses->where(function ($query) {
                    $query->where('costPriceM', '<>', '0')
                        ->orWhere('costPriceF', '<>', '0')
                        ->orWhere('costPriceU', '<>', '0')
                        ->orWhere('costPriceP', '<>', '0');
                });
            } else {
                $surpluses->where([
                    ['costPriceM', '=', '0'],
                    ['costPriceF', '=', '0'],
                    ['costPriceU', '=', '0'],
                    ['costPriceP', '=', '0'], ]);
            }
        }

        if (!empty($request['filter_have_sale_prices'])) {
            if ($request['filter_have_sale_prices'] == 'yes') {
                $surpluses->where(function ($query) {
                    $query->where('salePriceM', '<>', '0')
                        ->orWhere('salePriceF', '<>', '0')
                        ->orWhere('salePriceU', '<>', '0')
                        ->orWhere('salePriceP', '<>', '0');
                });
            } else {
                $surpluses->where([
                    ['salePriceM', '=', '0'],
                    ['salePriceF', '=', '0'],
                    ['salePriceU', '=', '0'],
                    ['salePriceP', '=', '0'], ]);
            }
        }

        //This option need to be checked according to the logic between the surplus and the standard surplus.
        //The rule is that surplus need to match with the standard surplus by same species and continent.
        if (!empty($request['filter_have_standard_surplus'])) {
            $surpluses_matched = Surplus::join('our_surplus', function ($join) {
                $join->on('surplus.animal_id', '=', 'our_surplus.animal_id')
                    ->on('surplus.area_region_id', '=', 'our_surplus.area_region_id');
            })
                ->pluck('surplus.id');

            if ($request['filter_have_standard_surplus'] == 'yes') {
                $surpluses->whereIn('id', $surpluses_matched);
            } else {
                $surpluses->whereNotIn('id', $surpluses_matched);
            }
        }

        if (!empty($request['filter_to_members'])) {
            $surpluses->where('to_members', ($request['filter_to_members'] == 'yes') ? true : false);
        }

        if (!empty($request['filter_remarks'])) {
            $surpluses->where('remarks', 'like', '%' . $request['filter_remarks'] . '%');
        }

        if (!empty($request['filter_intern_remarks'])) {
            $surpluses->where('intern_remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');
        }

        if (!empty($request['filter_updated_at_from'])) {
            $surpluses->whereDate('updated_at', '>=', $request['filter_updated_at_from']);
        }

        if (!empty($request['filter_updated_at_to'])) {
            $surpluses->whereDate('updated_at', '<=', $request['filter_updated_at_to']);
        }

        if (!empty($request['filter_imagen_species'])) {
            if ($request['filter_imagen_species'] === 'yes') {
                $surpluses->whereHas('animal', function ($query) {
                    $query->whereNotNull('catalog_pic')->orWhere('catalog_pic', '!=', '');
                });
                $noImages = [];
                foreach ($surpluses->get() as $row) {
                    if (!empty($row['animal']) && !empty($row['animal']->imagen_first)) {
                        array_push($noImages, $row['id']);
                    }
                }
                if (!empty($noImages)) {
                    $surpluses->orwhereIn('id', $noImages);
                }
            } else {
                $surpluses->whereHas('animal', function ($query) {
                    $query->whereNull('catalog_pic')->orWhere('catalog_pic', '');
                });
                $noImages = [];
                foreach ($surpluses->get() as $row) {
                    if (!empty($row['animal']) && !empty($row['animal']->imagen_first)) {
                        array_push($noImages, $row['id']);
                    }
                }
                if (!empty($noImages)) {
                    $surpluses->whereNotIn('id', $noImages);
                }
            }
        }

        if (!empty($request['filter_institution_level'])) {
            if ($request['filter_institution_level'] === 'empty') {
                $surpluses->whereHas('organisation', function ($query) use ($request) {
                    $query->whereNull('level');
                });
            } else {
                $surpluses->whereHas('organisation', function ($query) use ($request) {
                    $query->where('level', $request['filter_institution_level']);
                });
            }
        }

        $surpluses = $surpluses->get();

        if ($request->ajax() && !empty($surpluses->toArray())) {
            $data['header_table'] = ['Animal', 'Region', 'Country'];
            foreach ($surpluses as $key => $value) {
                $data['body_table'][$key]['id']      = $value->id;
                $data['body_table'][$key]['animal']  = $value->animal->common_name   ?? '';
                $data['body_table'][$key]['region']  = $value->country->region->name ?? '';
                $data['body_table'][$key]['country'] = $value->country->name         ?? '';
            }

            $view            = View::make('inbox.table_assing', $data);
            $html['error']   = false;
            $html['content'] = $view->render();

            return json_encode($html);
        } else {
            $html['error']   = true;
            $html['content'] = '';

            return json_encode($html);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getWantedAssing(Request $request)
    {
        $wanteds = Wanted::with(['animal'])->orderByDesc('updated_at');

        if (!empty($request['filter_animal_option'])) {
            if ($request['filter_animal_option'] === 'by_id') {
                if (!empty($request['filter_animal_id'])) {
                    $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                    $wanteds->where('animal_id', $filterAnimal->id);
                }
            } elseif ($request['filter_animal_option'] === 'by_name') {
                if (!empty($request['filter_animal_name'])) {
                    $wanteds->whereHas('animal', function ($query) use ($request) {
                        $query->where('common_name', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('common_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('scientific_name', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('scientific_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                            ->orWhere('spanish_name', 'like', '%' . $request['filter_animal_name'] . '%');
                    });
                }
            } else {
                $wanteds->whereNull('animal_id');
            }
        }

        if (!empty($request['empty_institution'])) {
            $wanteds->whereNull('organisation_id');
        } elseif (!empty($request['filter_institution_id'])) {
            $institutionFilter = Organisation::where('id', $request['filter_institution_id'])->first();

            $wanteds->where('organisation_id', $institutionFilter->id);
        }

        if (!empty($request['empty_client'])) {
            $wanteds->whereNull('contact_id');
        } elseif (!empty($request['filter_client_id'])) {
            $contactFilter = Contact::where('id', $request['filter_client_id'])->first();

            $wanteds->where('client_id', $request['filter_client_id']);
        }

        if (!empty($request['filter_animal_class'])) {
            $ids_genera = [];

            $class  = Classification::where('id', $request['filter_animal_class'])->first();
            $order  = null;
            $family = null;
            $genus  = null;

            if (!empty($request['filter_animal_order'])) {
                $order = $class->under->where('id', $request['filter_animal_order'])->first();
            }
            if (!empty($request['filter_animal_family'])) {
                $family = $order->under->where('id', $request['filter_animal_family'])->first();
            }
            if (!empty($request['filter_animal_genus'])) {
                $genus = $family->under->where('id', $request['filter_animal_genus'])->first();
            }

            if ($genus != null) {
                array_push($ids_genera, $genus->id);
                $wanteds->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            } elseif ($family != null) {
                $genera = $family->under->toArray();
                foreach ($genera as $family_genus) {
                    array_push($ids_genera, $family_genus['id']);
                }
                $wanteds->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            } elseif ($order != null) {
                $families = $order->under->all();
                foreach ($families as $order_family) {
                    $order_family_genera = $order_family->under->toArray();
                    foreach ($order_family_genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                }
                $wanteds->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            } elseif ($class != null) {
                $orders = $class->under->all();
                foreach ($orders as $class_order) {
                    $class_order_families = $class_order->under->all();
                    foreach ($class_order_families as $class_order_family) {
                        $class_order_family_genera = $class_order_family->under->toArray();
                        foreach ($class_order_family_genera as $family_genus) {
                            array_push($ids_genera, $family_genus['id']);
                        }
                    }
                }
                $wanteds->whereHas('animal', function ($query) use ($ids_genera) {
                    $query->whereIn('genus_id', $ids_genera);
                });
            }
        }

        if (!empty($request['filter_have_standard_wanted'])) {
            $ourWanteds = OurWanted::pluck('animal_id');
            if ($request['filter_have_standard_wanted'] == 'yes') {
                $wanteds->whereIn('animal_id', $ourWanteds);
            } else {
                $wanteds->whereNotIn('animal_id', $ourWanteds);
            }
        }

        if (!empty($request['filter_remarks'])) {
            $wanteds->where('remarks', 'like', '%' . $request['filter_remarks'] . '%');
        }

        if (!empty($request['filter_intern_remarks'])) {
            $wanteds->where('intern_remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');
        }

        if (!empty($request['filter_updated_at_from'])) {
            $wanteds->whereDate('updated_at', '>=', $request['filter_updated_at_from']);
        }

        if (!empty($request['filter_updated_at_to'])) {
            $wanteds->whereDate('updated_at', '<=', $request['filter_updated_at_to']);
        }

        if (!empty($request['filter_imagen_species'])) {
            if ($request['filter_imagen_species'] === 'yes') {
                $wanteds->whereHas('animal', function ($query) {
                    $query->whereNotNull('catalog_pic')->orWhere('catalog_pic', '!=', '');
                });
                $noImages = [];
                foreach ($wanteds->get() as $row) {
                    if (!empty($row['animal']) && !empty($row['animal']->imagen_first)) {
                        array_push($noImages, $row['id']);
                    }
                }
                if (!empty($noImages)) {
                    $wanteds->orwhereIn('id', $noImages);
                }
            } else {
                $wanteds->whereHas('animal', function ($query) {
                    $query->whereNull('catalog_pic')->orWhere('catalog_pic', '');
                });
                $noImages = [];
                foreach ($wanteds->get() as $row) {
                    if (!empty($row['animal']) && !empty($row['animal']->imagen_first)) {
                        array_push($noImages, $row['id']);
                    }
                }
                if (!empty($noImages)) {
                    $wanteds->whereNotIn('id', $noImages);
                }
            }
        }

        $wanteds = $wanteds->get();

        if ($request->ajax() && !empty($wanteds->toArray())) {
            $data['header_table'] = ['Animal', 'Origin', 'age'];
            foreach ($wanteds as $key => $value) {
                $data['body_table'][$key]['id']     = $value->id;
                $data['body_table'][$key]['animal'] = $value->animal->common_name ?? '';
                $data['body_table'][$key]['origin'] = $value->origin              ?? '';
                $data['body_table'][$key]['age']    = $value->age_group           ?? '';
            }

            $view            = View::make('inbox.table_assing', $data);
            $html['error']   = false;
            $html['content'] = $view->render();

            return json_encode($html);
        } else {
            $html['error']   = true;
            $html['content'] = '';

            return json_encode($html);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function multipleContact(Request $request)
    {
        $email = Email::find($request->id);
        if (!empty($email) && !empty($email['contact_id']) || !empty($email['organisation_id'])) {
            $contacts      = Contact::where('email', $email['from_email'])->get();
            $organisations = Organisation::where('email', $email['from_email'])->get();

            $result = array_merge($contacts->toArray(), $organisations->toArray());
            if ($request->ajax() && !empty($result)) {
                $data['header_table'] = ['Type', 'Name', 'Email'];
                foreach ($result as $key => $value) {
                    $type = '';
                    if (!empty($value['first_name'])) {
                        $last_name = $value['last_name'] ?? '';
                        $name      = $value['first_name'] . ' ' . $last_name;
                    } else {
                        $name = $value['name'] ?? '';
                    }
                    if ($key == 'first_name') {
                        $type = 'Contact';
                    } else {
                        $type = 'Organisation';
                    }
                    $data['body_table'][$key]['id']    = $value['id'];
                    $data['body_table'][$key]['type']  = $type           ?? '';
                    $data['body_table'][$key]['name']  = $name           ?? '';
                    $data['body_table'][$key]['email'] = $value['email'] ?? '';
                }

                $view                    = View::make('inbox.table_assing', $data);
                $view_contact            = View::make('inbox.table_contact_detail', ['email' => $email]);
                $html['error']           = false;
                $html['content']         = $view->render();
                $html['content_contact'] = $view_contact->render();

                return json_encode($html);
            } else {
                $html['error']           = true;
                $html['content']         = '';
                $html['content_contact'] = '';

                return json_encode($html);
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function getAllEmailAccount(Request $request)
    {
        if (!empty($request->account)) {
            $email_tokens = EmailToken::where('user_id', Auth::id())->first();
            if (empty($email_tokens['schedule_mail'])) {
                $email_tokens['schedule_mail'] = $request->account;
                $email_tokens->save();

                return response()->json(['error' => false, 'message' => 'All emails from your account have been scheduled to be retrieved. The email save will end in a few minutes']);
            } else {
                return response()->json(['error' => true, 'message' => 'Wait a few seconds for it to finish with the account ' . $request->account . ' to be able to run it again']);
            }
        } else {
            return response()->json(['error' => false, 'message' => 'Select an account to get emails']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SendEmailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(SendEmailRequest $request)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $email_to_array = [];
        if ($request->email_to != null)
            $email_to_array = array_map('trim', explode(',', $request->email_to));

        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }

        $email_bcc_array = [];
        if ($request->email_bcc != null) {
            $email_bcc_array = array_map('trim', explode(',', $request->email_bcc));
        }

        $email_attachment = [];
        if ($request->attachments_upload != null) {
            $attachments_upload = array_map('trim', explode(',', $request->attachments_upload));
        }

        if (!empty($attachments_upload)) {
            foreach ($attachments_upload as $key => $row) {
                $attachment = Attachment::find($row);
                if (!empty($attachment)) {
                    $email_attachment[$key]['name']    = $attachment->name;
                    $email_attachment[$key]['type']    = $attachment->type;
                    $email_attachment[$key]['content'] = file_get_contents(Storage::disk('')->path($attachment->path));
                    $attachment->delete();
                    Storage::disk('')->delete($attachment->path);
                }
            }
        }

        $userToken = $GraphService->getAllUserToken();
        if(!empty($userToken)){
            foreach ($userToken as $row){
                $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                $user_id = $GraphService->getUserByEmail($token, $request["email_from"]);
                if(!empty($token)){
                    $result = $GraphService->sendEmail($token,  $user_id->getId(), $request["email_subject"], $request["email_body_html"], $email_to_array, $email_cc_array, $email_bcc_array, $email_attachment);
                    if($result){
                        return response()->json(['error' =>false, 'message' => "The email was send successfully"]);
                    }else{
                        return response()->json(['error' =>true, 'message' => "The email was not send successfully"]);
                    }
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SendEmailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function draftEmail(Request $request)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $email_to_array = [];
        if ($request->email_to != null)
            $email_to_array = array_map('trim', explode(',', $request->email_to));

        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }

        $email_bcc_array = [];
        if ($request->email_bcc != null) {
            $email_bcc_array = array_map('trim', explode(',', $request->email_bcc));
        }

        $email_attachment = [];
        if ($request->attachments_upload != null) {
            $attachments_upload = array_map('trim', explode(',', $request->attachments_upload));
        }

        if (!empty($attachments_upload)) {
            foreach ($attachments_upload as $key => $row) {
                $attachment = Attachment::find($row);
                if (!empty($attachment)) {
                    $email_attachment[$key]['name']    = $attachment->name;
                    $email_attachment[$key]['type']    = $attachment->type;
                    $email_attachment[$key]['content'] = file_get_contents(Storage::disk('')->path($attachment->path));
                }
            }
        }

        $userToken = $GraphService->getAllUserToken();
        if(!empty($userToken)){
            foreach ($userToken as $row){
                $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                $user_id = $GraphService->getUserByEmail($token, $request["email_from"]);
                if(!empty($token)){
                        if(!empty($request->items_email_send)){
                            $email_forwad = Email::find($request->items_email_send);
                            $result = $GraphService->saveDraftForward($token,  $user_id->getId(), $email_forwad["guid"]);
                            if(empty($result)){
                                $result = $GraphService->saveDraft($token,  $user_id->getId(), $request["email_subject"], $request["email_body_html"], $email_to_array, $email_cc_array, $email_bcc_array, $email_attachment);
                            }
                        }else{
                            $result = $GraphService->saveDraft($token,  $user_id->getId(), $request["email_subject"], $request["email_body_html"], $email_to_array, $email_cc_array, $email_bcc_array, $email_attachment);
                        }

                    if (!empty($result)) {
                        if (!empty($attachments_upload)) {
                            foreach ($attachments_upload as $key => $row) {
                                $attachment = Attachment::find($row);
                                if (!empty($attachment)) {
                                    $attachment['guid'] = $result['id'];
                                    $attachment->save();
                                }
                            }
                        }

                        return response()->json(['error' => false, 'message' => 'The draft was saved successfully']);
                    } else {
                        return response()->json(['error' => true, 'message' => 'An error occurred when saving the draft']);
                    }
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SendEmailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function forwardEmail(SendEmailRequest $request)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $email_to_array = [];
        if ($request->email_to != null)
            $email_to_array = array_map('trim', explode(',', $request->email_to));

        $email_cc_array = [];
        if ($request->email_cc != null)
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));

        $email_bcc_array = [];
        if ($request->email_bcc != null) {
            $email_bcc_array = array_map('trim', explode(',', $request->email_bcc));
        }

        $email_attachment = [];
        if ($request->attachments_upload != null)
            $attachments_upload = array_map('trim', explode(',', $request->attachments_upload));

        $userToken = $GraphService->getAllUserToken();
        if(!empty($userToken)){
            foreach ($userToken as $row){
                $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                $user_id = $GraphService->getUserByEmail($token, $request["email_from"]);
                if(!empty($token)){
                    $email = Email::where("id", $request["items_email_send"])->orWhere("guid", $request["items_email_send"])->first();
                    if(!empty($email)){
                        $result = $GraphService->forwardEmail($token,  $user_id->getId() , $email["guid"], $request["email_subject"], $request["email_body_html"], $email_to_array, $request["email_cc"], $email_bcc_array);
                        if($result){
                            return response()->json(['error' =>false, 'message' => "The email was send successfully"]);
                        }else{
                            $result = $GraphService->sendEmail($token,  $user_id->getId(), $request["email_subject"], $request["email_body_html"], $email_to_array, $email_cc_array, $email_bcc_array, $email_attachment);
                            if($result){
                                return response()->json(['error' =>false, 'message' => "The email was send successfully"]);
                            }else{
                                return response()->json(['error' =>true, 'message' => "The email was not send successfully"]);
                            }
                            return response()->json(['error' =>true, 'message' => "The email was not send successfully"]);
                        }
                    } else {
                        return response()->json(['error' => true, 'message' => 'The email was not send successfully']);
                    }
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SendEmailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function forwardEmailBulk(SendEmailBulkRequest $request)
    {

        if (!empty($request["items_email_send"])) {
            $items = explode(",", (string)$request["items_email_send"]);

            $GraphService = new GraphService();
            $GraphService->initializeGraphForUserAuth();

            $email_to_array = [];
            if ($request->email_to != null)
                $email_to_array = array_map('trim', explode(',', $request->email_to));

            $email_cc_array = [];
            if ($request->email_cc != null)
                $email_cc_array = array_map('trim', explode(',', $request->email_cc));

            $email_bcc_array = [];
            if ($request->email_bcc != null)
                $email_bcc_array = array_map('trim', explode(',', $request->email_bcc));

            $userToken = $GraphService->getAllUserToken();
            if(!empty($userToken)){
                foreach ($userToken as $row){
                    $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                    $user_id = $GraphService->getUserByEmail($token, $request["email_from"]);
                    if(!empty($token)){
                        foreach ($items as $id) {
                            $email = Email::find($id);
                            if(!empty($email)){
                                $result = $GraphService->forwardEmail($token,  $user_id->getId() , $email["guid"], $request["email_subject"], $request["email_body_html"], $email_to_array, $email_cc_array, $email_bcc_array);
                            }
                        }
                        return response()->json(['error' =>false, 'message' => "The email was send successfully"]);
                    }else{
                        return response()->json(['error' =>true, 'message' => "The email was not send successfully"]);
                    }
                }
            }else{
                return response()->json(['error' =>true, 'message' => "The email was not send successfully"]);
            }
        }else{
            return response()->json(['error' =>true, 'message' => "The email was not send successfully"]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SendEmailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function replyEmail(SendEmailRequest $request)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();

        $email_bcc_array = [];
        if ($request->email_bcc != null) {
            $email_bcc_array = array_map('trim', explode(',', $request->email_bcc));
        }

        $userToken = $GraphService->getAllUserToken();
        if (!empty($userToken)) {
            foreach ($userToken as $row) {
                $token   = $GraphService->getUserToken($row['id'], json_decode($row['token']));
                $user_id = $GraphService->getUserByEmail($token, $request['email_from']);
                if (!empty($token)) {
                    $email  = Email::find($request['items_email_send']);
                    $result = $GraphService->replyEmail($token, $user_id->getId(), $email['guid'], $request['email_subject'], $request['email_body_html'], $request['email_to'], $request['email_cc'], $email_bcc_array);
                    if ($result) {
                        return response()->json(['error' => false, 'message' => 'The email was send successfully']);
                    } else {
                        return response()->json(['error' => true, 'message' => 'The email was not send successfully']);
                    }
                }
            }
        }
    }

    /**
     * Remove the selected actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createSentEmail($subject, $from, $email, $body, $guid = null)
    {
        $contact   = Contact::where('email', $email)->first();
        $new_email = new Email();
        if (!empty($contact) && $contact->count() > 0) {
            $first_name              = $contact['first_name'] ?? '';
            $last_name               = $contact['last_name']  ?? '';
            $name                    = $first_name . ' ' . $last_name;
            $new_email['contact_id'] = $contact['id'] ?? null;
        } else {
            $organisation                 = Organisation::where('email', $email)->first();
            $new_email['organisation_id'] = $organisation['id']   ?? null;
            $name                         = $organisation['name'] ?? '';
        }
        $new_email['from_email'] = $from;
        $new_email['to_email']   = $email;
        $new_email['body']       = $body;
        if (empty($guid)) {
            $new_email['guid'] = rand(1, 100);
        } else {
            $new_email['guid'] = $guid;
        }
        $new_email['subject'] = $subject;
        $new_email['name']    = $name;
        $new_email['is_send'] = 1;
        $new_email->save();

        return response()->json();
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('inbox.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['inbox.filter' => $query]);

        return redirect()->back();
    }

    //Export excel document with orders info.
    public function export(Request $request)
    {
        $file_name = 'Emails list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $inboxsByYear = Email::select('*', DB::raw('YEAR(created_at) as year, MONTH(created_at) as month'))->whereIn('id', explode(',', $request->items))->get()->groupBy(['year', 'month']);

        $export = new InboxExport($inboxsByYear);

        return Excel::download($export, $file_name);
    }

    /**
     * Filter contacts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterInbox(Request $request)
    {
         // Set session organization filter
         $data = session('inbox.filter');
         foreach ($request->query() as $key => $row){
             if(!empty($row)){
                 $data[$key] = $row;
             }
         }
         session(['inbox.filter' => $data]);

         return redirect()->back();
    }

    /**
     * Remove from contact session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromInboxSession($key)
    {
        $query = session('inbox.filter');
        Arr::forget($query, $key);
        session(['inbox.filter' => $query]);

        return redirect()->back();
    }

    public function getDraftEmail(Request $request)
    {
        if (!empty($request->id)) {
            $email = Email::find($request->id);
            if (!empty($email)) {
                $attachments    = Attachment::select('id')->where('guid', $email['guid'])->get();
                $attachments_id = '';
                if (!empty($attachments)) {
                    foreach ($attachments as $row) {
                        $attachments_id = $row['id'] . ',' . $attachments_id;
                    }
                }

                return response()->json(['error' => false, 'email' => $email->toArray(), 'attachments_id' => $attachments_id]);
            } else {
                return response()->json(['error' => true, 'message' => 'The email not exist']);
            }
        } else {
            return response()->json(['error' => true, 'message' => 'The email not exist']);
        }
    }

    public function addDashboard(Request $request)
    {
        if (!empty($request->email_ids)) {
            $items = explode(',', (string) $request->email_ids);
            if (!empty($request->parent_id)) {
                foreach ($items as $id) {
                    $new_item_dashboard                  = new ItemDashboard();
                    $new_item_dashboard['itemable_id']   = $id;
                    $new_item_dashboard['itemable_type'] = 'email';
                    $new_item_dashboard['dashboard_id']  = $request->parent_id;
                    $new_item_dashboard->save();
                }

                return response()->json(['error' => false, 'message' => 'The email was added correctly to the dashboard']);
            } else {
                return response()->json(['error' => true, 'message' => 'The email was not added correctly to the dashboard']);
            }
        } else {
            $GraphService = new GraphService();
            $GraphService->initializeGraphForUserAuth();

            $userToken = $GraphService->getAllUserToken();
            if (!empty($userToken)) {
                foreach ($userToken as $row) {
                    $token   = $GraphService->getUserToken($row['id'], json_decode($row['token']));
                    $user_id = $GraphService->getUserByEmail($token, $request['to_email']);
                    if (!empty($token)) {
                        $attachment = $GraphService->getAttachmentsInfoByEmail($token, $user_id->getId(), $request['email_guid'], $request['items_attachment'])->getPage();
                        if (!empty($attachment)) {
                            $path     = public_path() . '/attachments/' . $attachment->getname();
                            $contents = base64_decode($attachment->getContentBytes());

                            $email = Email::where('guid', $request['email_guid'])->first();

                            //store file temporarily
                            file_put_contents($path, $contents);

                            $new_document         = new Attachment();
                            $new_document['path'] = '/attachments/' . $attachment->getname();
                            $new_document['name'] = $attachment->getname();
                            $new_document['guid'] = $request['items_attachment'];
                            $new_document['type'] = $this->getType($attachment->getContentType());
                            if (!empty($email)) {
                                $new_document['email_id'] = $email['id'];
                            }
                            $new_document->save();

                            if (!empty($request->parent_id)) {
                                $new_item_dashboard                  = new ItemDashboard();
                                $new_item_dashboard['itemable_id']   = $new_document->id;
                                $new_item_dashboard['itemable_type'] = 'attachment';
                                $new_item_dashboard['dashboard_id']  = $request->parent_id;
                                $new_item_dashboard->save();
                            }

                            return response()->json(['error' => false, 'message' => 'The attachment was added correctly to the dashboard']);
                        } else {
                            return response()->json(['error' => true, 'message' => 'The attachment was not added correctly to the dashboard']);
                        }
                    }
                }
            }
        }
    }

    public function getType($type)
    {
        if ($type == 'application/pdf') {
            return 'pdf';
        } elseif ($type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            return 'doc';
        } elseif ($type == 'application/octet-stream') {
            return 'xls';
        } elseif ($type == 'image/png') {
            return 'png';
        } elseif ($type == 'image/jpg') {
            return 'jpg';
        } else {
            return $type;
        }
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
     * Upload file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadAttachment(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            //File Name
            $file_name = $file->getClientOriginalName();

            $type = $file->getMimeType();

            $path = Storage::putFileAs(
                'public/general_docs', $file, $file_name
            );

            $size = FileSizeHelper::bytesToHuman(Storage::size('public/general_docs/' . $file_name));

            $new_document         = new Attachment();
            $new_document['path'] = $path;
            $new_document['name'] = $file_name;
            $new_document['guid'] = '';
            $new_document['type'] = $file->getMimeType();
            $new_document->save();

            if (!empty($new_document)) {
                $data['data_attachments'] = $new_document;
                $view                     = View::make('inbox.item_attachments_new', $data);
                $html                     = $view->render();
            } else {
                $html = '';
            }

            return response()->json(['error' => false, 'attachment_id' => $new_document->id, 'attachments' => $html]);
        } else {
            return response()->json(['error' => true]);
        }
    }

    public function deleteAttachment(Request $request)
    {
        if (!empty($request->id)) {
            $attachment = Attachment::find($request->id);
            if (!empty($attachment)) {
                if (Storage::disk('')->exists($attachment->path)) {
                    Storage::disk('')->delete($attachment->path);
                }
                $attachment->delete();

                return response()->json(['error' => false]);
            } else {
                return response()->json(['error' => true, 'message' => 'Could not delete attachment']);
            }
        } else {
            return response()->json(['error' => true, 'message' => 'Could not delete attachment']);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SendEmailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function attachmentEmailMime(Request $request)
    {
        try{
            if (!empty($request["items"]) && count($request->items) > 0) {
                $GraphService = new GraphService();
                $GraphService->initializeGraphForUserAuth();
                $html = "";

                $userToken = $GraphService->getAllUserToken();
                if(!empty($userToken)){
                    foreach ($userToken as $row){
                        $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                        $user_id = $GraphService->getUserByEmail($token, $request["account"]);
                        if(!empty($token)){
                            $attachments_id = "";
                            foreach ($request->items as $id) {
                                $email = Email::find($id);
                                if(!empty($email)){
                                    $result = $GraphService->getEmailInfoMIME($token,  $user_id->getId() , $email["guid"]);
                                    $mime = (string)$result->getRawBody();
                                    if(!empty($mime)){
                                        $file_name = str_replace(["¨", "º", "-", "~", "#", "@", "|", "!", '"', "·", "$", "%", "&", "/", "(", ")", "?", "¡", "¿", "[", "^", "<code>", "]", "+", "}", "{", "¨", "´", ">", "< ", ";", ",", ":", ".", " "], "",  $email["subject"]);
                                        $file_name = str_replace(" ", "",  $file_name) . "-" . $email["id"] . ".eml";
                                        $path       = Storage::disk('')->path("public/general_docs") . "/" .  $file_name;
                                        file_put_contents($path, $mime);

                                        $new_document = new Attachment();
                                        $new_document["path"] = "public/general_docs/" .  $file_name;
                                        $new_document["name"] = $file_name;
                                        $new_document["guid"] = "";
                                        $new_document["type"] = Storage::disk('')->getMimeType("public/general_docs/" .  $file_name);
                                        $new_document->save();

                                        $attachments_id = $new_document["id"] . "," . $attachments_id;

                                        if(!empty($new_document)){
                                            $data["data_attachments"] = $new_document;
                                            $view = View::make('inbox.item_attachments_new', $data);
                                            $html .= $view->render();
                                        }

                                    }
                                }
                            }
                            return response()->json(['error' =>false, 'message' => "The email was select successfully", "htmlAttachments" => $html, "attachments_id" => $attachments_id]);
                        }else{
                            return response()->json(['error' =>true, 'message' => "The email was not select successfully"]);
                        }
                    }
                }else{
                    return response()->json(['error' =>true, 'message' => "The email was not select successfully"]);
                }
            }else{
                return response()->json(['error' =>true, 'message' => "The email was not select successfully"]);
            }
        }catch (\Throwable $th) {
            return $th;
        }


    }

    public function addColorEmail(Request $request){
        if (count($request->ids) > 0) {
            foreach ($request->ids as $id) {
                $email = Email::find($id);
                $email["color_id"] = $request->idColor;
                $email->save();
            }

            return response()->json(['error' =>false, 'message' => "The color add to the emails successfully"]);
        }else{
            return response()->json(['error' =>false, 'message' => "The color was not add to the emails"]);
        }
    }

    public function createColor(Request $request)
    {
        if(!empty($request->color_email_ids)){
            $color = new Color();
            $color["title"] = $request->title;
            $color["name"] = strtolower(str_replace(" ", "_", $request->title));
            $color["color"] = $request->color;
            $color->save();

            $items = explode(",", (string)$request->color_email_ids);
            foreach ($items as $id) {
                $email = Email::find($id);
                $email["color_id"] = $color->id;
                $email->save();
            }
            return redirect()->back()->with('success', 'The new color was saved successfully');
        }else{
            return redirect()->back()->with('error', 'The new color was not saved');
        }
    }

    public function removeColor(Request $request){
        if (count($request->ids) > 0) {
            foreach ($request->ids as $id) {
                $email = Email::find($id);
                $email["color_id"] = null;
                $email->save();
            }

            return response()->json(['error' =>false, 'message' => "The color remove to the emails successfully"]);
        }else{
            return response()->json(['error' =>false, 'message' => "The color was not remove to the emails"]);
        }
    }

    /**
     * Ordering of emails sent and emails received by 'date' or by 'containing attachment?'
     *
     * @param object $emails_received
     * @param object $emails
     * @param string $sortselected
     * @return array
     */
    public function emailOrdening(\Illuminate\Database\Eloquent\Builder $emails_received, \Illuminate\Database\Eloquent\Builder $emails, $sortselected)
    {
      if ($sortselected === 'attachment') {

         // Emails received
         $tmp_emails_received = $emails_received->pluck('id')->all();
         $attach_emails_received = Attachment::distinct()->whereIn('email_id', $tmp_emails_received)->pluck('email_id')->all();
         if (count($attach_emails_received) <= 0) {
            $emails_received->orderBy('created_at', 'DESC');
         } else {
            $emails_received->orderByRaw('case when id in ('
               . implode(',', $attach_emails_received) . ') then 0 else 1 end')
               ->orderBy('emails.created_at', 'DESC');
         }

         // Emails sent
         $tmp_emails = $emails->pluck('id')->all();
         $attach_emails = Attachment::distinct()->whereIn('email_id', $tmp_emails)->pluck('email_id')->all();
         if (count($attach_emails) <= 0) {
            $emails->orderBy('created_at', 'DESC');
         } else {
            $emails->orderByRaw('case when id in ('
               . implode(',', $attach_emails) . ') then 0 else 1 end')
               ->orderBy('emails.created_at', 'DESC');
         }
      } else {
         $emails_received->orderBy('created_at', 'DESC');
         $emails->orderBy('created_at', 'DESC');
      }
      return [
         'emails_received' => $emails_received,
         'emails' => $emails,
      ];
   }
}
