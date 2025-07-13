<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\UserTypeEnum;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Jobs\ProcessResetPasswordMailJob;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => UserTypeEnum::class,
        ];
    }

    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(ForgetPasswordTokens::class);
    }

    public function initiatePasswordReset(): void
    {
        $expiryTimelimit = config('auth.reset_password_expiry_time_limit');
        $expiry = Carbon::now()->addMinutes($expiryTimelimit);
        $token = str()->random(60);

        $this->passwordResetTokens()->create([
            'email' => $this->email,
            'token' => $token,
            'expiration' => $expiry,
        ]);

        ProcessResetPasswordMailJob::dispatch($this->email, $token, $expiry, $this->name);
    }

    public static function createUser($data): object
    {
        info($data);
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => (int) $data['type'],
        ]);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }
}
