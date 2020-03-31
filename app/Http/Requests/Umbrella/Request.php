<?php

namespace App\Http\Requests\Umbrella;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
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

    public function messages()
    {
        return [
            'phone.required'     => '请输入手机号码',
            'vcode.required'     => '请输入手机验证码',
            'real_name.required' => '请输入真实姓名',
            'ID_number.required' => '请输入身份证号码',
            'ID_number.id'       => '身份证号码格式错误',
            'ID_number.unique'       => '该身份证号码已经注册过了',
            'phone.phone'        => '手机号码格式错误',
            'phone.unique'        => '该手机号码已经注册过了',
            'birthday'           => '请选择生日'
        ];
    }
}
