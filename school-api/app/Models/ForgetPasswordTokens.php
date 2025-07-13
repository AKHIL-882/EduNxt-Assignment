<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordTokens extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'token',
        'expiration',
    ];

    protected $casts = [
        'expiration' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatepassword(string $password)
    {

        //check if the token has expired
        if (Carbon::parse($this->expiration)->isPast()) {
            return response()->json(['error' => 'Password reset token has expired'], 422);
        }

        //Fetch the associated user and update the password
        $user = User::findOrFail($this->user_id);
        $user->password = Hash::make($password);
        $user->save();

        //delete the token after successful update
        $this->delete();

        return ['status' => true,
            'message' => 'Password updated Succesfully'];

    }
}