<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientLog extends Model
{
    protected $fillable = [
        'client_id', 'user_id', 'field_name', 'old_value', 'new_value', 'note', 'log_type'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
