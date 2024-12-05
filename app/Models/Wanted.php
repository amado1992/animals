<?php

namespace App\Models;

use App\Enums\AgeGroup;
use App\Enums\Enumerable;
use App\Enums\LookingFor;
use App\Models\Origin;
use Illuminate\Database\Eloquent\Model;

class Wanted extends Model
{
    //use Enumerable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wanted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organisation_id',
        'client_id',
        'animal_id',
        'origin',
        'age_group',
        'looking_for',
        'remarks',
        'intern_remarks',
        'inserted_by',
        'created_at',
        'updated_at',
        'new_wanted',
    ];

    /**
     * Define attributes that are enum.
     *
     * @var array
     */
    protected static $enums = [
        'age_group'   => \App\Enums\AgeGroup::class,
        'looking_for' => \App\Enums\LookingFor::class,
    ];

    /**
     * Get looking_for field.
     *
     * @return string
     */
    public function getLookingFieldAttribute()
    {
        return ($this->looking_for != null) ? LookingFor::getValue($this->looking_for) : '';
    }

    /**
     * Get origin field.
     *
     * @return string
     */
    public function getOriginFieldAttribute()
    {
        $origin = Origin::where('short_cut', $this->origin)->first();

        return (!empty($origin)) ? $origin['name'] : '';
    }

    /**
     * Get age field.
     *
     * @return string
     */
    public function getAgeFieldAttribute()
    {
        return ($this->age_group != null) ? AgeGroup::getValue($this->age_group) : '';
    }

    /**
     * The animal that is wanted
     */
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * The wanted institution
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * The contact that is related with the wanted.
     */
    public function client()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * The admin user who inserted the record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }

    /**
     * Get search mailing related with the wanted record.
     */
    public function search_mailings()
    {
        return $this->morphMany(SearchMailing::class, 'searchable')->orderByDesc('created_at');
    }

    /**
     * A standard surplus can be on different lists.
     */
    public function wanted_lists()
    {
        return $this->belongsToMany(WantedList::class, 'wantedlists_wanted');
    }
}
