<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO Check if wallet is connected
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
            'name' => 'required|string|max:30',
            'cid' => 'nullable|string',
            'description' => 'nullable|string|max:500',
            'transaction' => 'nullable|string',
            'collectible' => 'required|file|mimes:png,gif,jpg|max:10000',
        ];
    }
}
