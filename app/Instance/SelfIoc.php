<?php
    namespace App\Instance;

    use App\Models\User;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Auth;
    use App\Repositories;
    use App\Services;

    class SelfIoc {
        public static function getInstance($className)
        {
            $paramArr = self::getMethodParams($className);

            return (new \ReflectionClass($className))->newInstanceArgs($paramArr);
        }

        public static function make($className, $methodName,array $args = [])
        {
            $class = self::getInstance($className);

            $params = self::getMethodParams($className, $methodName);

            return $class->{$methodName}(array_merge($params, $args));
        }

        public static function makeModel($className, $methodName, array $args = [])
        {
            $class = self::getInstance($className);

            $params = self::getMethodParams($className, $methodName);
            foreach ($params as $key => $param) {
                if ($param instanceof Model) {
                    if (is_int($args[$key])) {
                        if ($newParam = $param->find($args[$key])) {
                            $params[$key] = $newParam;
                            unset($args[$key]);
                        } else {
                            throw new \Exception('没有找到相应的记录');
                        }
                    } else if ($args[$key] instanceof Model){
                        $params[$key] = $args[$key];
                        unset($args[$key]);
                    }
                }
            }
            array_values($args);

            return $class->{$methodName}($params, $args);
        }

        public static function makeAuth($className, $methodName, array $args = [])
        {
            $class = self::getInstance($className);

            $params = self::getMethodParams($className, $methodName);
            foreach ($params as $key => $param) {
                if ($param instanceof Model) {
                    if ($param instanceof User) {
                        if (Auth::user()) {
                            $params[$key] = Auth::user();
                            $count = count($args);
                            for($i = $count; $i > $key; $i--) {
                                $args[$i] = $args[$i-1];
                            }
                        } else {
                            throw new \Exception('当前没有登录的用户');
                        }
                    } else {
                        if (is_int($args[$key])) {
                            if ($newParam = $param->find($args[$key])) {
                                $params[$key] = $newParam;
                                unset($args[$key]);
                            } else {
                                throw new \Exception('没有找到相应的记录');
                            }
                        } else if ($args[$key] instanceof Model){
                            $params[$key] = $args[$key];
                            unset($args[$key]);

                        }
                    }
                }
            }
            array_values($args);

            return $class->{$methodName}(array_merge($params, $args));
        }


        protected static function getMethodParams($className, $methodName = '_construct')
        {
            $class = new \ReflectionClass($className);
            $paramArr = [];

            if ($class->hasMethod($methodName)) {
                $method = $class->getMethod($methodName);
                $params = $method->getParameters();

                if (count($params) > 0) {
                    foreach ($params as $param) {
                        if ($paramClass = $param->getClass()) {
                            $paramClassName = $paramClass->getName();

                            $args = self::getMethodParams($paramClassName);

                            $paramArr[] = (new \ReflectionClass($paramClassName))->newInstanceArgs($args);
                        }
                    }
                }
            }

            return $paramArr;
        }
    }