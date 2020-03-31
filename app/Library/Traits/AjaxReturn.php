<?php
namespace App\Library\Traits;

trait AjaxReturn
{
    /**
     * 响应一个ajax的成功请求
     *
     * @param array $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    private function successReturn($message = 'successful', $data = [])
    {
        return $this->ajaxReturn(0, $message, $data);
    }

    /**
     * 响应一个ajax的错误请求
     *
     * @param array $data
     * @param int $code
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    private function errorReturn($code = 1, $message = 'error', $data = [])
    {
        return $this->ajaxReturn($code, $message, $data, 422);
    }

    /**
     * 响应ajax的请求
     *
     * @param $code
     * @param $message
     * @param array $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    private function ajaxReturn($code, $message, $data = [], $status = 200)
    {
        $this->is_ajax();

        return response()->json(compact('code', 'message', 'data'), $status);
    }

    /**
     * 判断是否为ajax请求
     *
     * @throws \Exception
     */
    private function is_ajax()
    {
        if (!request()->ajax()) {
            throw new \SystemException('不是ajax请求，请使用别的方式响应');
        }
    }
}