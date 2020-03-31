<?php

namespace App\Http\Requests\Umbrella;

class RegisterRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'real_name' => 'required',
            'ID_number' => 'required|id|unique:users',
            'phone'     => 'required|phone|unique:users',
            'vcode'     => 'required',
            'birthday'  => 'required'
        ];
    }
}
