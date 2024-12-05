<?php

namespace App\Importing;

use App\Enums\AgeGroup;
use App\Enums\LookingFor;
use App\Enums\Origin;
use App\Models\Animal;
use App\Models\Contact;
use App\Models\Role;
use App\Models\User;
use App\Models\Wanted;
use DateTimeHelper;
use DB;
use Illuminate\Support\Arr;

class ImportWanteds
{
    /**
     * Create a new import instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $wanteds = DB::connection('mysql_old')->select('select * from wanted where deletedwanted = 0 order by idwanted');

        $oldAdmins = DB::connection('mysql_old')->select('select * from user where deleteduser = 0 and rol = "admin" order by id');

        $roles  = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins = User::whereRoleIs(Arr::pluck($roles, 'name'))->get();

        $contacts = Contact::pluck('id', 'old_id');

        $animals = Animal::pluck('id', 'old_id');

        $origin     = Origin::get();
        $ageGroup   = AgeGroup::get();
        $lookingFor = LookingFor::get();

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($wanteds as $wanted) {
            if ($wanted->idanimal != null && $wanted->idanimal != 0 && isset($animals[$wanted->idanimal])) {
                $oldAdmin = null;
                $admin    = null;
                if ($wanted->whoInsertedW != null && $wanted->whoInsertedW != 0) {
                    $oldAdmin = collect($oldAdmins)->where('id', $wanted->whoInsertedW)->first();

                    if (!is_null($oldAdmin)) {
                        $admin = $admins->where('email', $oldAdmin->email)->first();
                    }
                }

                array_push($mappedArray, [
                    'client_id'      => (isset($contacts[$wanted->iduser])) ? $contacts[$wanted->iduser] : null,
                    'animal_id'      => $animals[$wanted->idanimal],
                    'origin'         => (collect($origin)->contains($wanted->bred)) ? Origin::getIndex($wanted->bred) : null,
                    'age_group'      => (collect($ageGroup)->contains($wanted->age)) ? AgeGroup::getIndex($wanted->age) : null,
                    'looking_for'    => (collect($lookingFor)->contains($wanted->sex)) ? LookingFor::getIndex($wanted->sex) : null,
                    'remarks'        => (trim($wanted->remarks)       != '') ? $wanted->remarks : null,
                    'intern_remarks' => (trim($wanted->hiddenremarks) != '') ? $wanted->hiddenremarks : null,
                    'inserted_by'    => (!is_null($admin)) ? $admin->id : null,
                    'created_at'     => ($wanted->datecreated_wanted  != null && DateTimeHelper::validateDate($wanted->datecreated_wanted)) ? date('Y-m-d H:i:s', strtotime($wanted->datecreated_wanted)) : null,
                    'updated_at'     => ($wanted->datemodified_wanted != null && DateTimeHelper::validateDate($wanted->datemodified_wanted)) ? date('Y-m-d H:i:s', strtotime($wanted->datemodified_wanted)) : null,
                ]);

                $count_imported_records++;
            }
        }

        Wanted::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
