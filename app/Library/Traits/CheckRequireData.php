<?php
namespace App\Library\Traits;

trait CheckRequireData
{
    /**
     * 检查要去的require项
     *
     * @param array $data
     * @throws \Exception
     */
    protected function checkRequire(array $data)
    {
        if (!empty($this->require)) {
            foreach ($this->require as $require) {
                if (empty($data[$require])) {
                    throw new \Exception('参数' . $require . '，不可为空');
                }
            }
        } else {
            throw new \Exception('还未定义require属性');
        }
    }
}