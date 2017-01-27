<?php

use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Middleware\RoutingMiddleware;
use Pie\JsonApi\Middleware\JsonApiErrorHandlerMiddleware;
use Pie\JsonApi\Middleware\JsonApiMiddleware;
use Pie\JsonApi\Middleware\JsonApiRequestValidatorMiddleware;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Request\Request;
use Zend\Diactoros\Response;

Configure::load('Pie/JsonApi.config', 'default', false);

$request = ServerRequestFactory::fromGlobals();

if (1 === preg_match(Configure::read('pie.jsonApi.pathMatchRegex'), $request->getUri()->getPath())) {
    EventManager::instance()
        ->on(
            'Server.buildMiddleware',
            function (Event $event, MiddlewareQueue $middleware) {
                $middleware
                    //->insertBefore(ErrorHandlerMiddleware::class, new JsonApiErrorHandlerMiddleware())
                    ->add(new JsonApiErrorHandlerMiddleware())
                    ->insertAfter(RoutingMiddleware::class, new JsonApiMiddleware())
                    ->insertAfter(JsonApiMiddleware::class, new JsonApiRequestValidatorMiddleware());
            }
        );
}

$exceptionFactory = new DefaultExceptionFactory();
$jsonApiRequest = new Request($request, $exceptionFactory);
\Pie\JsonApi\Library\JsonApiFactory::create($jsonApiRequest, new Response(), $exceptionFactory);
