<?php
namespace App\Library\Traits;

trait ExceptionReport
{
    use AjaxReturn;

    function exceptionResponse(\Closure $callback)
    {
        try {
            return $callback();
        } catch (\MessageException $messageException) {
            return $this->errorReturn(2001, $messageException->getMessage());
        } catch (\SystemException $systemException) {

        } catch (\Exception $exception) {

        } finally {

        }
    }
}