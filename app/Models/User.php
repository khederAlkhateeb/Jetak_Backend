<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Builders\UserBuilder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;

#[Fillable(['name', 'email', 'password', 'phone', 'status', 'building_number', 'floor', 'room_number'])]
#[Hidden(['password', 'remember_token'])]
#[UseEloquentBuilder(UserBuilder::class)]


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    protected string $guard_name = 'api';


    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function deliveredOrders()
    {
        return $this->hasMany(Order::class, 'delivery_id');
    }

    public function breadSubscriptions()
    {
        return $this->hasMany(BreadSubscription::class, 'student_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    public function providedServices()
    {
        return $this->hasMany(StudentService::class, 'provider_id');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'requester_id');
    }

    public function writtenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'provider_id');
    }
    public function getRole(): ?string
    {
        return $this->getRoleNames()->first();
    }
}
