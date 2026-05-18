<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge(['name' => strip_tags($this->name)]);
        }
        if ($this->has('description')) {
            $this->merge(['description' => strip_tags($this->description)]);
        }
    }
}
