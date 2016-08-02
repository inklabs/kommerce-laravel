<?php
namespace App\Lib;

use inklabs\KommerceTemplates\Lib\CSRFTokenGeneratorInterface;

class CSRFTokenGenerator implements CSRFTokenGeneratorInterface
{
    /**
     * @return string
     */
    public function getToken()
    {
        return csrf_token();
    }
}
