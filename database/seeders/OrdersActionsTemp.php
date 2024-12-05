<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Order;
use App\Models\OrderAction;
use App\Services\OfferService;
use Illuminate\Database\Seeder;

class OrdersActionsTemp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions      = Action::get();
        $actionRecord = Action::where('action_code', 'statement_izs')->first();

        $offerService = new OfferService;

        $orders = Order::get();

        foreach ($orders as $order) {
            /*foreach($actions as $action) {
                $orderAction = new OrderAction();
                $orderAction->order_id = $order->id;
                $orderAction->action_id = $action->id;
                $orderAction->toBeDoneBy = $action->toBeDoneBy;
                $orderAction->save();
            }*/
            /*$orderAction = new OrderAction();
            $orderAction->order_id = $order->id;
            $orderAction->action_id = $actionRecord->id;
            $orderAction->toBeDoneBy = $actionRecord->toBeDoneBy;
            $orderAction->save();*/

            $offer = $offerService->calculate_offer_totals($order->offer->id);
        }
    }
}
