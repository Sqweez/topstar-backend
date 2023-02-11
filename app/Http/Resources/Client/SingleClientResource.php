<?php

namespace App\Http\Resources\Client;

use App\Models\Client;
use App\Models\ClientBookmark;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
* @mixin Client
 */

class SingleClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'gender_display' => $this->gender === 'F' ? 'Женский' : 'Мужской',
            'club' => $this->club,
            'pass' => $this->pass,
            'registrar' => $this->registrar->name,
            'phone' => mask_phone($this->phone),
            'unmasked_phone' => $this->phone,
            'balance' => $this->balance,
            'photo' => $this->getFirstMediaUrl(Client::MEDIA_AVATAR),
            'birth_date_formatted' => $this->birth_date_formatted,
            'age' => $this->age,
            'age_type' => $this->age_type,
            'birth_date' => $this->birth_date,
            'is_birthday_today' => $this->is_birthday,
            'description' => $this->description,
            'sales' => $this->sales,
            'programs' => ClientPurchasedServices::collection($this->lastPrograms),
            'solarium' => ClientPurchasedSolarium::collection($this->solarium),
            'trinket' => $this->trinket->code ?? null,
            'cabinet_number' => $this->trinket->cabinet_number ?? null,
            'active_session' => $this->active_session,
            'is_in_club' => $this->is_in_club,
            'trinket_can_given' => $this->trinket_can_given,
            'session_can_be_finished' => $this->session_can_be_finished,
            'has_unlimited_discount' => $this->has_unlimited_discount,
            'total_solarium' => $this->solarium->sum('salable.remaining_minutes'),
            'in_bookmark' => !!ClientBookmark::query()
                ->where('client_id', $this->id)
                ->where('user_id', auth()->id())
                ->first()
        ];
    }
}
