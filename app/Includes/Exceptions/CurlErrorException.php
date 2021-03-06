<?php namespace App\Includes\Exceptions;


use \Exception;

/**
 * Class ClientErrorException
 * designated for error curl error requests
 *
 * @package App\Includes\Exceptions
 */
class CurlErrorException extends Exception
{
    public function __construct($message){
        parent::__construct($message);
    }
}