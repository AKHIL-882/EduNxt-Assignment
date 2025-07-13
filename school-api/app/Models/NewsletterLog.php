<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'status',
        'error_message',
    ];

    /**
     * Get the subscriber associated with the newsletter log.
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
