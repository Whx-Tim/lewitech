<?php
namespace App\Services;

class Service
{
    public function ajaxReturn($code,$message,$data=[]){
        return response()->json(compact('code','message','data'));
    }
}