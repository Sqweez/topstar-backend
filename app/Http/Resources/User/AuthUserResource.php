<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->string_role,
            'club' => $this->club,
            'roles' => $this->roles,
            'photo' => $this->getFirstMediaUrl(User::MEDIA_AVATAR),
            'is_boss' => $this->is_boss,
            'can_sale_service' => $this->can_sale_service
        ];
    }
}
