<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * App\Models\ServiceSale
 *
 * @property int $id
 * @property int $service_id
 * @property int|null $entries_count
 * @property int|null $minutes_remaining
 * @property string|null $active_until
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Service|null $service
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereActiveUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereEntriesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereMinutesRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read string|null $active_until_formatted
 * @property-read bool $can_be_prolonged
 * @property-read bool $can_be_used
 * @property-read bool $is_activated
 * @property-read bool $is_expired
 * @property-read bool $is_unlimited
 * @property-read int|null $remaining_visits
 * @property-read int|null $visits_count
 * @property-read \App\Models\Sale|null $sale
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SessionService[] $visits
 * @property-read bool $can_be_restored
 * @property-read bool $already_written_off
 * @property-read mixed $last_trainer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientServicePenalty[] $penalties
 * @property-read int|null $penalties_count
 * @property int $is_prolongation
 * @property-read int $remaining_minutes
 * @property-read int $solarium_expired_minutes
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceSale whereIsProlongation($value)
 */
class ServiceSale extends Model
{
    use HasFactory;

    protected $table = 'service_sale';

    protected $guarded = ['id'];

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class);
    }

    public function sale(): MorphOne {
        return $this->morphOne(Sale::class, 'salable');
    }

    public function visits(): HasMany {
        return $this->hasMany(SessionService::class);
    }

    public function penalties(): HasMany {
        return $this->hasMany(ClientServicePenalty::class)
            ->where('is_accepted', true)
            ->where('is_declined', false);
    }

    public function getLastTrainerAttribute() {
        return $this->visits()->latest()->first()->trainer ?? ['id' => null, 'name' => 'Не установлен'];
    }

    public function getActiveUntilFormattedAttribute(): ?string {
        return
            $this->active_until ?
                Carbon::parse($this->active_until)->format('d.m.Y') :
                null;
    }

    public function getIsActivatedAttribute(): bool {
        return !!$this->active_until;
    }

    public function getCanBeProlongedAttribute(): bool {
        return $this->getCanBeUsedAttribute() && $this->service->prolongation_price > 0;
    }

    public function getVisitsCountAttribute(): int {
        return $this->visits->count();
    }

    public function getPenaltiesCountAttribute(): int {
        return $this->penalties->count();
    }

    public function getIsUnlimitedAttribute(): bool {
        return is_null($this->entries_count);
    }

    public function getRemainingVisitsAttribute() {
        if ($this->getIsUnlimitedAttribute()) {
            return true;
        }
        return $this->entries_count - $this->getVisitsCountAttribute() - $this->getPenaltiesCountAttribute();
    }

    public function getSolariumExpiredMinutesAttribute(): int {
        return $this->visits->sum('minutes');
    }

    public function getRemainingMinutesAttribute(): int {
        return $this->minutes_remaining - $this->getSolariumExpiredMinutesAttribute();
    }

    public function getIsExpiredAttribute(): bool {
        if (!$this->getIsActivatedAttribute()) {
            return false;
        }
        return !Carbon::parse($this->active_until)->greaterThanOrEqualTo(now());
    }

    public function getCanBeUsedAttribute(): bool {
        return $this->getIsActivatedAttribute()
            && $this->getRemainingVisitsAttribute()
            && !$this->getIsExpiredAttribute();
    }

    public function getCanBeRestoredAttribute(): bool {
        return !$this->getCanBeUsedAttribute()
            && $this->getIsActivatedAttribute()
            && $this->getRemainingVisitsAttribute() > 0;
    }

    public function getAlreadyWrittenOffAttribute(): bool {
        $activeSession = $this->sale->client->active_session;
        if (!$activeSession) {
            return false;
        }
        return !!$this->visits->where('session_id', $activeSession->id)->count();
    }
}
