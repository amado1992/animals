<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entities = [
            //'user',
            //'role',
            'contacts',
            'institutions',
            //'bank_account',
            'animals',
            'classifications',
            'airfreights',
            'crates',
            //'regions',
            //'countries',
            //'airports',
            //'currencies',
            //'cites',
            'surplus-suppliers',
            'standard-surplus',
            'wanted-clients',
            'standard-wanted',
            //'price-request',
            'offers',
            'orders',
            'invoices',
            'codes',
            'zoo-associations',
            'standard-texts',
            'interesting-websites',
            'guidelines',
            'general-documents',
        ];

        foreach ($entities as $entity) {
            Permission::create(['name' => $entity . '.create', 'display_name' => 'Can create ' . $entity]);
            Permission::create(['name' => $entity . '.read', 'display_name' => 'Can read ' . $entity]);
            Permission::create(['name' => $entity . '.update', 'display_name' => 'Can update ' . $entity]);
            Permission::create(['name' => $entity . '.delete', 'display_name' => 'Can delete ' . $entity]);
        }

        Permission::create(['name' => 'animals.export-survey', 'display_name' => 'Can export animals survey']);
        Permission::create(['name' => 'animals.upload-files', 'display_name' => 'Can upload files in animals']);
        Permission::create(['name' => 'animals.delete-files', 'display_name' => 'Can delete files in animals']);

        Permission::create(['name' => 'crates.export-survey', 'display_name' => 'Can export crates survey']);
        Permission::create(['name' => 'crates.see-cost-prices', 'display_name' => 'Can see crates cost prices']);
        Permission::create(['name' => 'crates.see-sale-prices', 'display_name' => 'Can see crates sale prices']);
        Permission::create(['name' => 'crates.upload-files', 'display_name' => 'Can upload files in crates']);
        Permission::create(['name' => 'crates.delete-files', 'display_name' => 'Can delete files in crates']);

        Permission::create(['name' => 'airfreights.upload-files', 'display_name' => 'Can upload files in airfreights']);
        Permission::create(['name' => 'airfreights.delete-files', 'display_name' => 'Can delete files in airfreights']);

        Permission::create(['name' => 'zoo-associations.export-survey', 'display_name' => 'Can export zoo-associations survey']);
        Permission::create(['name' => 'codes.export-survey', 'display_name' => 'Can export codes survey']);

        Permission::create(['name' => 'institutions.export-contacts', 'display_name' => 'Can export institutions survey']);

        Permission::create(['name' => 'contacts.see-all-contacts', 'display_name' => 'Can see all contacts']);
        Permission::create(['name' => 'contacts.approve-members', 'display_name' => 'Can approve new members']);
        Permission::create(['name' => 'contacts.export-contacts', 'display_name' => 'Can export contacts survey']);
        Permission::create(['name' => 'contacts.contacts-address-list', 'display_name' => 'Can create a contact address list']);

        Permission::create(['name' => 'standard-surplus.see-cost-prices', 'display_name' => 'Can see std surplus cost prices']);
        Permission::create(['name' => 'standard-surplus.see-sale-prices', 'display_name' => 'Can see std surplus sale prices']);
        Permission::create(['name' => 'standard-surplus.export-survey', 'display_name' => 'Can export std surplus survey']);
        Permission::create(['name' => 'standard-surplus.upload-files', 'display_name' => 'Can upload files in std surplus']);
        Permission::create(['name' => 'standard-surplus.delete-files', 'display_name' => 'Can delete files in std surplus']);

        Permission::create(['name' => 'surplus-suppliers.see-cost-prices', 'display_name' => 'Can see surplus cost prices']);
        Permission::create(['name' => 'surplus-suppliers.see-sale-prices', 'display_name' => 'Can see surplus sale prices']);
        Permission::create(['name' => 'surplus-suppliers.see-all-surplus', 'display_name' => 'Can see all surplus']);
        Permission::create(['name' => 'surplus-suppliers.surplus-mailing', 'display_name' => 'Can send surplus mailing']);
        Permission::create(['name' => 'surplus-suppliers.export-survey', 'display_name' => 'Can export surplus survey']);
        Permission::create(['name' => 'surplus-suppliers.upload-files', 'display_name' => 'Can upload files in surplus']);
        Permission::create(['name' => 'surplus-suppliers.delete-files', 'display_name' => 'Can delete files in surplus']);

        Permission::create(['name' => 'standard-wanted.export-survey', 'display_name' => 'Can export std wanted survey']);

        Permission::create(['name' => 'wanted-clients.see-all-wanted', 'display_name' => 'Can see all wanted']);
        Permission::create(['name' => 'wanted-clients.wanted-mailing', 'display_name' => 'Can send wanted mailing']);
        Permission::create(['name' => 'wanted-clients.export-survey', 'display_name' => 'Can export wanted survey']);

        Permission::create(['name' => 'standard-surplus.read-lists', 'display_name' => 'Can read standard surplus lists']);
        Permission::create(['name' => 'standard-surplus.create-lists', 'display_name' => 'Can create standard surplus list']);

        Permission::create(['name' => 'surplus-suppliers.read-lists', 'display_name' => 'Can read surplus lists']);
        Permission::create(['name' => 'surplus-suppliers.create-lists', 'display_name' => 'Can create surplus list']);

        Permission::create(['name' => 'standard-wanted.read-lists', 'display_name' => 'Can read standard wanted lists']);
        Permission::create(['name' => 'standard-wanted.create-lists', 'display_name' => 'Can create standard wanted list']);

        Permission::create(['name' => 'offers.see-all-offers', 'display_name' => 'Can see all offers']);
        Permission::create(['name' => 'offers.see-cost-prices', 'display_name' => 'Can see offer-order cost prices']);
        Permission::create(['name' => 'offers.see-sale-prices', 'display_name' => 'Can see offer-order sale prices']);
        Permission::create(['name' => 'offers.see-profit-value', 'display_name' => 'Can see offer-order profit value']);
        Permission::create(['name' => 'offers.export-survey', 'display_name' => 'Can export offers survey']);
        Permission::create(['name' => 'offers.upload-files', 'display_name' => 'Can upload files in offers']);
        Permission::create(['name' => 'offers.delete-files', 'display_name' => 'Can delete files in offers']);
        Permission::create(['name' => 'offers.offer-document', 'display_name' => 'Can generate offer document']);
        Permission::create(['name' => 'offers.calculation-document', 'display_name' => 'Can generate calculation details document']);
        Permission::create(['name' => 'offers.send-offer', 'display_name' => 'Can send offer']);
        Permission::create(['name' => 'offers.send-reminders', 'display_name' => 'Can send reminders']);
        Permission::create(['name' => 'offers.other-email-options', 'display_name' => 'Can send other email options']);

        Permission::create(['name' => 'orders.see-all-orders', 'display_name' => 'Can see all orders']);
        Permission::create(['name' => 'orders.order-invoices', 'display_name' => 'Can generate order invoices']);
        Permission::create(['name' => 'orders.export-survey', 'display_name' => 'Can export orders survey']);
        Permission::create(['name' => 'orders.upload-files', 'display_name' => 'Can upload files in orders']);
        Permission::create(['name' => 'orders.delete-files', 'display_name' => 'Can delete files in orders']);
        Permission::create(['name' => 'orders.order-documents', 'display_name' => 'Can create order documents']);

        Permission::create(['name' => 'invoices.export-survey', 'display_name' => 'Can export invoices survey']);
    }
}
