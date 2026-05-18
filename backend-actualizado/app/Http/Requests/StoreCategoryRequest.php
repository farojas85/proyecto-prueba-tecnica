<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Prevención XSS: Eliminar tags HTML de campos de texto
        $this->merge([
            'name' => $this->has('name') ? strip_tags($this->name) : null,
            'description' => $this->has('description') ? strip_tags($this->description) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
        ];
    }
}
