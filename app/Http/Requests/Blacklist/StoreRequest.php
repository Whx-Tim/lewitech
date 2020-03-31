<?php

namespace App\Http\Requests\Blacklist;

class StoreRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type'        => 'required',
            'description' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'type.required'        => '请输入加入黑名单的类型',
            'description.required' => '请输入拉黑的原因'
        ];
    }
}
