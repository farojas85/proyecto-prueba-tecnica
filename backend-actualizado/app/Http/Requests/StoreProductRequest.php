<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->has('name') ? strip_tags($this->name) : null,
            'description' => $this->has('description') ? strip_tags($this->description) : null,
        ]);
    }
}
