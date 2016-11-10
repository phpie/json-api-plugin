<?php

namespace Pie\JsonApi\Library;

use Cake\Network\Response as CakeResponse;
use Zend\Diactoros\Response as PsrResponse;

class ResponseTransformer extends \Cake\Http\ResponseTransformer
{
    public static function fromPsrToCake(PsrResponse $psrResponse, CakeResponse $cakeResponse)
    {
        $body = static::getBody($psrResponse);

        $cakeResponse->body($body['body']);
        $cakeResponse->statusCode($psrResponse->getStatusCode());

        if ($body['file']) {
            $cakeResponse->file($body['file']);
        }
        $cookies = static::parseCookies($psrResponse->getHeader('Set-Cookie'));
        foreach ($cookies as $cookie) {
            $cakeResponse->cookie($cookie);
        }
        $headers = static::collapseHeaders($psrResponse);
        $cakeResponse->header($headers);

        if (!empty($headers['Content-Type'])) {
            $cakeResponse->type($headers['Content-Type']);
        }

        return $cakeResponse;
    }
}
