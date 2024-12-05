<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reservationActions = [
            ['action_code' => 'reservation_client', 'action_description' => 'Send reservation to client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'checklist_client', 'action_description' => 'Send document-checklist to client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'deposit_invoice_client', 'action_description' => 'Send deposit invoice to client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'deposit_payment_client', 'action_description' => 'Deposit payment client received', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'proforma_invoice', 'action_description' => 'Send proforma invoice to client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'reservation_supplier', 'action_description' => 'Send reservation to supplier', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'checklist_supplier', 'action_description' => 'Send document-checklist to supplier', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'deposit_invoice_supplier', 'action_description' => 'Receipt deposit invoice supplier', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'deposit_payment_supplier', 'action_description' => 'Deposit payment supplier paid', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'statement_izs', 'action_description' => 'Send statement IZS and John Rens is same entity', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
        ];

        $permitActions = [
            ['action_code' => 'cites_export_permit', 'action_description' => 'Apply Cites export permit', 'toBeDoneBy' => 'Supplier', 'belongs_to' => 'Order'],
            ['action_code' => 'cites_import_permit', 'action_description' => 'Apply Cites import permit + copy exp Cites', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
        ];

        $veterinaryActions = [
            ['action_code' => 'apply_veterinary_client', 'action_description' => 'Apply Veterinary import requirements client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Offer_Order'],
            ['action_code' => 'send_veterinary_supplier', 'action_description' => 'Send Veterinary import requirements supplier', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'costs_bloodtests', 'action_description' => 'Costs bloodtests', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'costs_quarantine', 'action_description' => 'Costs quanrantine', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'costs_healthcertificate', 'action_description' => 'Costs Healthcertificate', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'costs_veterinary_preparations', 'action_description' => 'Inform client total costs veterinary preparations', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'apply_health_certificate', 'action_description' => 'Apply Health certificate', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'send_healthcertificate_client', 'action_description' => 'Send text healthcertificate to client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'hc_approved_client', 'action_description' => 'HC approved by client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
        ];

        $crateActions = [
            ['action_code' => 'apply_exterior_dimensions_crates', 'action_description' => 'Apply exterior dimensions crates by supplier', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Offer_Order'],
            ['action_code' => 'send_dimensions_crates', 'action_description' => 'Send dimensions crates to constructor/transport agent', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
        ];

        $transportActions = [
            ['action_code' => 'transport_quotation', 'action_description' => 'Apply transport quotation', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Offer_Order'],
            ['action_code' => 'book_transport_shipper', 'action_description' => 'Book transport shipper', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'airwaybill_shipper', 'action_description' => 'Apply Airwaybill from shipper', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
            ['action_code' => 'shipp_date_awb_client', 'action_description' => 'Send shipp date + AWB to Client', 'toBeDoneBy' => 'IZS', 'belongs_to' => 'Order'],
        ];

        $offerActions = [
            ['action_code' => 'apply_pictures_enclosures', 'action_description' => 'Apply pictures of enclosures', 'toBeDoneBy' => 'Client', 'belongs_to' => 'Offer'],
            ['action_code' => 'apply_pictures_species', 'action_description' => 'Apply pictures of species', 'toBeDoneBy' => 'Supplier', 'belongs_to' => 'Offer'],
        ];

        foreach ($reservationActions as $reservationAction) {
            Action::create(['category' => 'reservation', 'action_description' => $reservationAction['action_description'], 'action_code' => $reservationAction['action_code'], 'toBeDoneBy' => $reservationAction['toBeDoneBy'], 'belongs_to' => $reservationAction['belongs_to']]);
        }

        foreach ($permitActions as $permitAction) {
            Action::create(['category' => 'permit', 'action_description' => $permitAction['action_description'], 'action_code' => $permitAction['action_code'], 'toBeDoneBy' => $permitAction['toBeDoneBy'], 'belongs_to' => $permitAction['belongs_to']]);
        }

        foreach ($veterinaryActions as $veterinaryAction) {
            Action::create(['category' => 'veterinary', 'action_description' => $veterinaryAction['action_description'], 'action_code' => $veterinaryAction['action_code'], 'toBeDoneBy' => $veterinaryAction['toBeDoneBy'], 'belongs_to' => $veterinaryAction['belongs_to']]);
        }

        foreach ($crateActions as $crateAction) {
            Action::create(['category' => 'crate', 'action_description' => $crateAction['action_description'], 'action_code' => $crateAction['action_code'], 'toBeDoneBy' => $crateAction['toBeDoneBy'], 'belongs_to' => $crateAction['belongs_to']]);
        }

        foreach ($transportActions as $transportAction) {
            Action::create(['category' => 'transport', 'action_description' => $transportAction['action_description'], 'action_code' => $transportAction['action_code'], 'toBeDoneBy' => $transportAction['toBeDoneBy'], 'belongs_to' => $transportAction['belongs_to']]);
        }

        foreach ($offerActions as $offerAction) {
            Action::create(['category' => 'offer', 'action_description' => $offerAction['action_description'], 'action_code' => $offerAction['action_code'], 'toBeDoneBy' => $offerAction['toBeDoneBy'], 'belongs_to' => $offerAction['belongs_to']]);
        }
    }
}
