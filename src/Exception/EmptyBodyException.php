<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-16
 * Time: 18:46
 */

namespace App\Exception;

use Throwable;

class EmptyBodyException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct('The body of the POST/PUT/PATCH should not be empty', $code, $previous);
    }
}