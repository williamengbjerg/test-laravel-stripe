<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

/**
 * Class User
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
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
     * The tax rates that should apply to the customer's subscriptions.
     *
     * @return array
     */
    public function taxRates(): array
    {
        return ['txr_1IHsoeAt19ot019UMuO3nKc5'];
    }

}
