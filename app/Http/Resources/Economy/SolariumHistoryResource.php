<?php

namespace App\Http\Resources\Economy;

use App\Models\Session;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Session
 */

class SolariumHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client' => $this->client,
            'user' => $this->session_service->first()->user,
            'club' => $this->club,
            'minutes' => $this->session_service->reduce(function ($a, $c) {
                return $a + $c['minutes'];
            }, 0),
            'date' => format_datetime($this->session_service->first()->created_at),
            'created_at' => $this->session_service->first()->created_at,
        ];
    }
}
