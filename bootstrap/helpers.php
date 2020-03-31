<?php

    if (! function_exists('array_filter_empty')) {
        function array_filter_empty($array = []) {
            foreach ($array as $key => $item) {
                if (empty($item)) {
                    unset($array[$key]);
                }
            }

            if (empty($array)) {
                $array = [];
            }

            return $array;
        }

    }

    if (! function_exists('array_except_values')) {
        function array_except_values($array = [], $values = []) {
            foreach ($array as $key => $item) {
                if (in_array($item, $values)) {
                    unset($array[$key]);
                }
            }

            return $array;
        }
    }
    
    if (! function_exists('carbon_week_zh')) {
        function carbon_week_zh($week) {
            switch ($week) {
                case 0:
                    $week = '周日';
                    break;
                case 1:
                    $week = '周一';
                    break;
                case 2:
                    $week = '周二';
                    break;
                case 3:
                    $week = '周三';
                    break;
                case 4:
                    $week = '周四';
                    break;
                case 5:
                    $week = '周五';
                    break;
                case 6:
                    $week = '周六';
                    break;
            }

            return $week;
        }
    }

    if (! function_exists('check_arg')) {
        function check_arg(array $data, $checks)
        {
            if (!is_array($checks)) {
                $arr = func_get_args();
                unset($arr[0]);
                $checks = array_values($arr);
            }

            foreach ($checks as $check) {
                if (empty($data[$check])) {
                    throw new \Exception('缺少'.$check.'参数');
                }
            }
        }
    }

    if (! function_exists('arraySearch')) {
        function arraySearch($needle, $array) {
            $result = [];
            foreach ($array as $item) {
                if (str_contains($item->name, $needle)) {
                    $result []= $item->name;
                }
            }

            return $result;
        }
    }