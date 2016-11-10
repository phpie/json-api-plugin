<?php

namespace Pie\JsonApi\Middleware;

use Pie\JsonApi\Library\JsonApiFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactory;
use WoohooLabs\Yin\JsonApi\Request\Request;

class JsonApiMiddleware
{
    /**
     * @param ServerRequestInterface $request  The request.
     * @param ResponseInterface      $response The response.
     * @param callable               $next     The next middleware to call.
     *
     * @return ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $params = $request->getQueryParams();
        // Remove page
        unset($params['page']);

        $request = $request->withQueryParams($params)
            ->withAttribute(
                'params',
                $request->getAttribute('params') +
                [
                    '_jsonApi' => JsonApiFactory::get()
                ]
            );

        return $next($request, $response);
    }
}
