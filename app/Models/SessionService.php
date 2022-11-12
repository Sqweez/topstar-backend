<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SessionService
 *
 * @property int $id
 * @property int $service_sale_id
 * @property int $user_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereServiceSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereUserId($value)
 * @mixin \Eloquent
 * @property int $trainer_id
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereTrainerId($value)
 * @property-read \App\Models\User|null $trainer
 * @property int|null $minutes
 * @method static \Illuminate\Database\Eloquent\Builder|SessionService whereMinutes($value)
 * @property-read \App\Models\User|null $user
 */
class SessionService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainer(): BelongsTo {
        return $this->belongsTo(User::class, 'trainer_id')
            ->select(['id', 'name'])
            ->withDefault([
                'id' => null,
                'name' => 'Не установлен'
            ]);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id')
            ->select(['id', 'name'])
            ->withDefault([
                'id' => null,
                'name' => 'Неизвестно'
            ]);
    }
}
