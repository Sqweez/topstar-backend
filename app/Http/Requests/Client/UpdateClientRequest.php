<?php

namespace App\Http\Requests\Client;

use App\Models\Client;
use App\Rules\NotBusyPass;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'club_id' => 'required',
            'pass' => ['sometimes', new NotBusyPass($this->id, Client::class)],
            'description' => 'sometimes',
            'photo' => 'sometimes|file',
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'phone' => unmask_phone($this->phone),
            'description' => $this->description === 'null' ? '' : $this->description,
        ]);
    }
}
