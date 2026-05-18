<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Prevención XSS: Eliminar tags HTML de campos de texto
        if ($this->has('name')) {
            $this->merge(['name' => strip_tags($this->name)]);
        }
        if ($this->has('description')) {
            $this->merge(['description' => strip_tags($this->description)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
        ];
    }
}
