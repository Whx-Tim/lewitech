<?php

namespace App\Http\Requests\Business;

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
            'type'    => 'required',
            'address' => 'required',
            'linkman' => 'required',
            'phone'   => 'required',
            'name'    => 'required',
            'image'   => 'required',
            'poster'  => 'required',
        ];
    }

    public function messages()
    {
        return [
            'type.required'    => '商家类型必填',
            'address.required' => '请填写商家地址!',
            'linkman.required' => '请填写商家联系人电话!',
            'phone.required'   => '请填写商家电话!',
            'name.required'    => '请填写商家名称!',
            'image.required'   => '请上传商家图片!',
            'poster.required'  => '请上传商家海报!',
            'phone.numeric'    => '商家电话必须为数字',
            'linkman.numeric'  => '商家联系人电话必须为数字',
        ];
    }
}
