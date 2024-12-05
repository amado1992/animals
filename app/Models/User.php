<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        //$this->attributes['password'] = bcrypt($value);
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Get the user's full name.
     *
     * @param  string  $value
     * @return void
     */
    public function getFullNameAttribute()
    {
        if (is_null($this->last_name)) {
            return "{$this->name}";
        }

        return "{$this->name} {$this->last_name}";
    }

    /**
     * Get the user email domain.
     *
     * @return void
     */
    public function getEmailDomainAttribute()
    {
        return substr("{$this->email}", strpos("{$this->email}", '@') + 1);
    }

    /**
     * The zoo associations related with the user.
     */
    public function zoo_associations()
    {
        return $this->hasMany(ZooAssociation::class);
    }

    /**
     * Get the contact record associated with the user.
     */
    public function contact()
    {
        return $this->hasOne(Contact::class);
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $userLanguage = strtoupper(App::getLocale());

        if ($this->hasRole('website-user')) {
            if ($this->contact->country) {
                $userLanguage = $this->contact->country->language;
            }

            $url = 'http://www-tst.zoo-services.com/reset-password?token=' . $token . '&email=' . $this->email;
        } else {
            $url = 'https://app.zoo-services.com/password/reset/' . $token . '?email=' . $this->email;
        }

        $this->notify(new ResetPasswordNotification($url, $userLanguage));
    }
}
