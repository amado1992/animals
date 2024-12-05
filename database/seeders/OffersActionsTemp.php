<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Offer;
use App\Models\OfferAction;
use Illuminate\Database\Seeder;

class OffersActionsTemp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = Action::where(function ($query) {
            $query->where('belongs_to', 'Offer')
                ->orWhere('belongs_to', 'Offer_Order');
        })->get();

        $offers = Offer::get();

        foreach ($offers as $offer) {
            foreach ($actions as $action) {
                $offerAction             = new OfferAction();
                $offerAction->offer_id   = $offer->id;
                $offerAction->action_id  = $action->id;
                $offerAction->toBeDoneBy = $action->toBeDoneBy;
                $offerAction->save();
            }
        }
    }
}
