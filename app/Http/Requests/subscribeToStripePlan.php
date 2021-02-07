<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class subscribeToStripePlan extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required',
            'planName'       => 'required',
            'pricing_plan'   => 'required',
        ];
    }
}
