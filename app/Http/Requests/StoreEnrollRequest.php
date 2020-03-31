<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollRequest extends FormRequest
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
            'name'       => 'required',
            'phone'      => 'required',
            'occupation' => 'required',
            'gender'     => 'required',
            'age'        => 'required',
            'hobby'      => 'required',
            'wechat'     => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => '请输入称呼',
            'phone.required'      => '请输入联系电话',
            'occupation.required' => '请输入职业',
            'gender.required'     => '请输入性别',
            'age.required'        => '请输入年龄',
            'hobby.required'      => '请输入爱好',
            'wehchat.required'    => '请输入联系的微信号'
        ];
    }
}
