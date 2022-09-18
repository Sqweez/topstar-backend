<?php

namespace App\Http\Resources\Economy;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
* @mixin Transaction
 */

class AccountTopUp extends JsonResource
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
            'description' => $this->description,
            'user' => $this->user,
            'amount' => $this->amount,
            'client' => $this->client,
            'club' => $this->club,
            'date' => format_datetime($this->created_at)
        ];
    }
}
