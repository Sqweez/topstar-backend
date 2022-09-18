<?php

namespace App\Models;

use App\Http\Services\SaleService;
use App\Models\Traits\HasPass;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $gender
 * @property string|null $description
 * @property int $balance
 * @property string $birth_date
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUserId($value)
 * @mixin \Eloquent
 * @property-read string|null $birth_date_formatted
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Pass|null $pass
 * @method static \Illuminate\Database\Eloquent\Builder|Client pass($pass)
 * @property-read \App\Models\User|null $registrar
 * @property int $club_id
 * @property-read \App\Models\Club|null $club
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClubId($value)
 * @property-read mixed $age
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sale[] $programs
 * @property-read int|null $programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sale[] $sales
 * @property-read int|null $sales_count
 * @property-read mixed $trinket
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Session[] $sessions
 * @property-read int|null $sessions_count
 * @property-read \App\Models\Session|null $active_session
 * @property-read bool $is_in_club
 * @property-read bool $trinket_can_given
 * @property-read bool $session_can_be_finished
 * @property-read bool $has_unlimited_discount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sale[] $solarium
 * @property-read int|null $solarium_count
 * @property string|null $password
 * @property string|null $comment
 * @property int $is_client
 * @property int $is_employee
 * @property int|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read bool $is_trainer
 * @property-read string $string_role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Query\Builder|Client onlyTrashed()
 * @method static Builder|Client whereComment($value)
 * @method static Builder|Client whereCreatedById($value)
 * @method static Builder|Client whereDeletedAt($value)
 * @method static Builder|Client whereIsClient($value)
 * @method static Builder|Client whereIsEmployee($value)
 * @method static Builder|Client wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|Client withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Client withoutTrashed()
 */
class Client extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const MEDIA_AVATAR = 'client_avatar';

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'club_id' => 'integer'
    ];

    public function pass(): MorphOne {
        return $this->morphOne(Pass::class, 'passable');
    }

    public function registrar(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'id' => null,
            'name' => 'Система'
        ]);
    }

    public function club(): BelongsTo {
        return $this->belongsTo(Club::class)->withDefault([
            'id' => -1,
            'name' => 'Неизвестно'
        ]);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class)
            ->orderByDesc('id');
    }

    public function sales(): HasMany {
        return $this->hasMany(Sale::class)
            ->latest();
    }

    public function programs(): HasMany {
        return $this
            ->sales()
            ->whereHasMorph('salable', [ServiceSale::class], function ($query) {
                return $query->whereHas('service', function ($query) {
                    return $query->whereIn('service_type_id', [Service::TYPE_PROGRAM, Service::TYPE_UNLIMITED]);
                });
            })
            ->latest();
    }

    public function solarium(): HasMany {
        return $this
            ->sales()
            ->whereHasMorph('salable', [ServiceSale::class], function ($query) {
                return $query->whereHas('service', function ($query) {
                    return $query->whereIn('service_type_id', [Service::TYPE_SOLARIUM]);
                });
            })
            ->latest();
    }

    public function sessions(): HasMany {
        return $this->hasMany(Session::class)->orderByDesc('id');
    }

    public function active_session(): HasOne {
        return $this
            ->hasOne(Session::class)
            ->where('finished_at', null);
    }

    public function getBirthDateFormattedAttribute(): ?string {
        return $this->birth_date
            ? Carbon::parse($this->birth_date)->format('d.m.Y')
            : 'Неизвестно';
    }

    public function getTrinketAttribute() {
        return $this->active_session ? $this->active_session->trinket : null;
    }

    public function getIsInClubAttribute(): bool {
        return !!$this->active_session;
    }

    public function getAgeAttribute() {
        if (!$this->birth_date) {
            return 'Неизвестно';
        }
        return now()->diffInYears($this->birth_date);
    }

    public function getTrinketCanGivenAttribute(): bool {
        return $this->active_session && $this->active_session->trinket_id === null;
    }

    public function getSessionCanBeFinishedAttribute(): bool {
        return !!$this->active_session /*&& $this->active_session->trinket_id !== null */;
    }

    public function getHasUnlimitedDiscountAttribute(): bool {
        return $this->programs
            ->where('salable.service.service_type_id', 1)
            ->filter(function ($sale) {
                return $sale['salable']['can_be_used'] === true &&
                    Carbon::parse($sale['salable']['active_until'])->diffInDays(now()) >= Club::UNLIMITED_DAYS_BEFORE_DISCOUNT;
            })
            ->count() > 0;
    }
}
