<?php

namespace Pie\JsonApi\Library;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\Request;

class JsonApiFactory
{
    private static $instance;

    public static function create(
        Request $request = null,
        ResponseInterface $response = null,
        ExceptionFactoryInterface $exceptionFactory = null
    ) {
        if (self::$instance instanceof JsonApi) {
            return self::$instance;
        }

        if (is_null($request) || is_null($response) || is_null($exceptionFactory)) {
            throw new InvalidArgumentException('You must set all arguments.');
        }

        return self::$instance = new JsonApi($request, $response, $exceptionFactory);
    }

    public static function get(
        Request $request = null,
        ResponseInterface $response = null,
        ExceptionFactoryInterface $exceptionFactory = null
    ) {
        return self::create($request, $response, $exceptionFactory);
    }
}
