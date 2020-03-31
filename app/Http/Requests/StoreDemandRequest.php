<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandRequest extends FormRequest
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
            'title'      => 'required',
            'content'    => 'required',
            'name'       => 'required',
            'phone'      => 'required',
            'occupation' => 'required',
            'gender'     => 'required',
            'age'        => 'required',
            'hobby'      => 'required',
            'wechat'     => 'required',
            'student_number' => 'required_if:is_schoolmate,1',
            'college'        => 'required_if:is_schoolmate,1',
            'grade'          => 'required_if:is_schoolmate,1'
        ];
    }

    public function messages()
    {
        return [
            'title.required'      => '请输入需求标题',
            'content.required'    => '请输入需求内容',
            'name.required'       => '请输入您的姓名或者称呼',
            'phone.required'      => '请输入您的联系方式',
            'occupation.required' => '请输入职业',
            'gender.required'     => '请输入性别',
            'age.required'        => '请输入年龄',
            'hobby.required'      => '请输入爱好',
            'wehchat.required'    => '请输入联系的微信号',
            'student_number.required_if' => '请输入学号',
            'college.required_if'        => '请输入学院全称',
            'grade.required_if'          => '请输入入学年份'
        ];
    }
}
