<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StopCard extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class)->select(['id', 'name'])->withTrashed();
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class)->select(['id', 'name']);
    }

    public function service(): BelongsTo {
        return $this->belongsTo(ServiceSale::class);
    }
}
