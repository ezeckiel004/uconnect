<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CagnoteLike extends Model
{
    protected $fillable = ['user_id', 'cagnote_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cagnote()
    {
        return $this->belongsTo(Cagnote::class);
    }
}
