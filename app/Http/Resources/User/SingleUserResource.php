<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin User
*/
class SingleUserResource extends JsonResource
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
            'roles' => $this->roles->pluck('id'),
            'club' => $this->club,
            'position' => $this->string_role,
            'login' => $this->login,
            'photo' => $this->getFirstMediaUrl(),
            'phone' => $this->phone,
            'birth_date' => $this->birth_date_formatted,
            'pass' => $this->pass->code ?? '',
            'description' => $this->description
        ];
    }
}
