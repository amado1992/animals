<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Dashboard;
use App\Models\Mailing;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Organisation;
use App\Models\SearchMailing;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $widget = [
            'organisations'    => Organisation::count(),
            'contacts'         => Contact::count(),
            'offers'           => Offer::count(),
            'orders'           => Order::count(),
            'mailings'         => Mailing::count(),
            'search_mailings'  => SearchMailing::count(),
            'row_color'        => ["yellow" => "#f7b84bb5", "blue" => "#3bafdab5", "green" => "#1abc9cb5", "red" => "#f1556cb5"],
            'dashboard_yellow' => Dashboard::where("main", 1)->where("row_color", "yellow")->orderBy("order", "ASC")->get(),
            'dashboard_blue' => Dashboard::where("main", 1)->where("row_color", "blue")->orderBy("order", "ASC")->get(),
            'dashboard_green' => Dashboard::where("main", 1)->where("row_color", "green")->orderBy("order", "ASC")->get(),
            'dashboard_red' => Dashboard::where("main", 1)->where("row_color", "red")->orderBy("order", "ASC")->get(),
            'email_from' => "info@zoo-services.com",
            'email_body' => "<br><br>" . view('emails.email-signature')->render(),
        ];

        $email_from = "info@zoo-services.com";
        $email_body = "<br><br>" . view('emails.email-signature')->render();
        
        /**
         *  Calculate totals & subtotals of actions
         *  Totals are displayed on the dashboard items
         */
        $subitems = new DashboardsController();
        $total = [];
        $totals = [];
        foreach ($widget['dashboard_yellow'] as $dashboard) {
           if ($dashboard->title !== 'EMAILS') {
             $totals[$dashboard->title] = $subitems->getFilterDataTotal($dashboard);
           }
        }
        foreach ($totals as $key => $subtotal) {
           $total[$key] = $totals[$key]['total'];
           $total[$key]['total'] = array_sum($totals[$key]['total']);
        }

        return view('home', compact('widget', 'email_from', 'email_body', 'total'));
    }
}
