<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActiveRequest extends FormRequest
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
            'phone'      => 'required',
            'sponsor'    => 'required',
            'location'   => 'required',
//            'start_time' => 'required',
//            'end_time'   => 'required',
            'poster'     => 'required',
            'name'       => 'required',
//            'end_at'     => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => '请输入活动名称',
            'poster.required'     => '请上传活动封面图片',
            'location.required'   => '请输入活动位置',
            'sponsor.required'    => '请输入活动的主办方',
            'phone.required'      => '请输入活动主办方的联系电话',
            'start_time.required' => '请选择活动的开始时间',
            'end_time.required'   => '请选择活动的结束时间',
            'end_at.required'     => '请选择活动的截止时间'
        ];
    }
}
