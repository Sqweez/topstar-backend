<?php

namespace App\Http\Requests\User;

use App\Http\Services\PassService;
use App\Models\Pass;
use App\Models\User;
use App\Rules\NotBusyPass;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Mockery\Matcher\Not;

class UpdateUserRequest extends FormRequest
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
            'phone' => 'required|string|unique:users,phone,' . $this->id,
            'birth_date' => 'required|date|before:today|after:' . now()->subYears(100),
            'roles' => 'required|array',
            'pass' => ['sometimes', new NotBusyPass($this->id, User::class)],
            'description' => 'sometimes'
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'birth_date' => Carbon::parse($this->birth_date)->format('y-m-d'),
        ]);
    }
}
