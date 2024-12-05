<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Animal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_id',
        'code_number',
        'common_name',
        'common_name_alt',
        'scientific_name',
        'scientific_name_slug',
        'scientific_name_alt',
        'spanish_name',
        'cites_global_key',
        'cites_europe_key',
        //'class_id',
        //'order_id',
        //'family_id',
        'genus_id',
        'iata_code',
        'iata_code_letter',
        'body_weight',
        'catalog_pic',
        'ztl_class',
        'ztl_order',
        'ztl_family',
        'ztl_article',
        'chinese_name',
    ];

    /**
     * Set the slug value.
     *
     * @param  string  $value
     * @return void
     */
    public function setScientificNameSlugValue($value)
    {
        $this->attributes['scientific_name_slug'] = Str::slug($value, '_');
    }

    /**
     * Set the animal common name.
     *
     * @param  string  $value
     * @return void
     */
    public function setCommonNameAttribute($value)
    {
        $this->attributes['common_name'] = Str::ucfirst($value);
    }

    /**
     * Get the surplus female quantity.
     *
     * @return string
     */
    public function getImagenFirstAttribute()
    {
        $files = Storage::allFiles('public/animals_pictures/' . $this->id);

        $files_processed = [];
        $result          = [];
        foreach ($files as $file) {
            $file = pathinfo($file);

            $result['name'] = $file['basename'];

            $size = getimagesize(public_path() . '/storage/animals_pictures/' . $this->id . '/' . $file['basename']);
            if ($size !== false) {
                $result['dimension'] = $size[0] . ' x ' . $size[1] . ' px';
                array_push($files_processed, $result);
            }
        }

        return $files_processed[0] ?? [];
    }

    /**
     * Set the animal scientific name.
     *
     * @param  string  $value
     * @return void
     */
    public function setScientificNameAttribute($value)
    {
        $this->attributes['scientific_name'] = Str::ucfirst($value);
    }

    /**
     * Set the animal scientific name 2.
     *
     * @param  string  $value
     * @return void
     */
    public function setScientificNameAltAttribute($value)
    {
        $this->attributes['scientific_name_alt'] = Str::ucfirst($value);
    }

    /**
     * Set the animal spanish name.
     *
     * @param  string  $value
     * @return void
     */
    public function setSpanishNameAttribute($value)
    {
        $this->attributes['spanish_name'] = Str::ucfirst($value);
    }

    /**
     * The genus classification for this animal
     */
    public function classification()
    {
        return $this->belongsTo(Classification::class, 'genus_id');
    }

    /**
     * The global cites for this animal
     */
    public function cites_global()
    {
        return $this->belongsTo(Cites::class, 'cites_global_key', 'key');
    }

    /**
     * The european cites for this animal
     */
    public function cites_europe()
    {
        return $this->belongsTo(Cites::class, 'cites_europe_key', 'key');
    }

    /**
     * The standard surplus records that are related with an animal.
     */
    public function our_surpluses()
    {
        return $this->hasMany(OurSurplus::class);
    }

    /**
     * The surplus records that are related with an animal.
     */
    public function surpluses($orderByField = 'created_at', $orderByDirection = 'desc')
    {
        return $this->hasMany(Surplus::class)->orderBy($orderByField, $orderByDirection);
    }

    /**
     * The surplus records that are related with an animal.
     */
    public function surpluses_not_order()
    {
        return $this->hasMany(Surplus::class);
    }

    /**
     * The standard wanted records that are related with an animal.
     */
    public function our_wanteds()
    {
        return $this->hasMany(OurWanted::class);
    }

    /**
     * The wanted records that are related with an animal.
     */
    public function wanteds()
    {
        return $this->hasMany(Wanted::class);
    }

    /**
     * The offers where an animal belong.
     */
    public function offers()
    {
        return $this->hasManyThrough(
            'App\Models\OfferSpecies',
            'App\Models\OurSurplus',
            'animal_id', // Foreign key on our_surplus table...
            'oursurplus_id', // Foreign key on offers_species table...
            'id', // Local key on animals table...
            'id' // Local key on our_surplus table...
        );
        //return $this->hasManyThrough('App\Models\OfferSpecies', 'App\Models\OurSurplus');
    }

    /**
     * Animal crates.
     */
    public function crates()
    {
        return $this->belongsToMany(Crate::class, 'animal_crate');
    }
}
