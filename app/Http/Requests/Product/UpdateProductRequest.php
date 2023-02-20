<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'price' => 'required|integer|min:0',
            'product_category_id' => 'required',
            'barcode' => [
                'sometimes',
                Rule::unique('products')->ignore($this->id),
            ],
            'product_type_id' => 'required',
            'attribute' => 'string'
        ];
    }
}
