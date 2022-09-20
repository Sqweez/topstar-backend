<?php

namespace App\Http\Requests\User;

use App\Rules\NotBusyPass;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
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
            'phone' => [
                'required',
                'string',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'birth_date' => 'required|date|before:today|after:' . now()->subYears(100),
            'club_id' => 'required',
            'roles' => 'required|array',
            'password' => 'sometimes',
            'pass' => ['sometimes', new NotBusyPass],
            'description' => 'sometimes',
            'photo' => 'sometimes|file',
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'birth_date' => Carbon::parse($this->birth_date)->format('y-m-d'),
            'password' => $this->password ? \Hash::make($this->password) : Hash::make(Str::random(10)),
            'phone' => unmask_phone($this->phone),
        ]);
    }
}
