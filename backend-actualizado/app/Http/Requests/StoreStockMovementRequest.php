<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string|in:entrada,salida',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'auth_user_id' => 'nullable|integer'
        ];
    }
}
