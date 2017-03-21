<?php

namespace Pie\JsonApi\Middleware;

use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Network\Exception\HttpException;
use Cake\Network\Response;
use Cake\Routing\Router;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Schema\Error;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Serializer\DefaultSerializer;

class JsonApiErrorHandlerMiddleware
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
        try {
            return $next($request, $response);
        } catch (Exception $exception) {
            $additionalMeta = Configure::read('debug') === true ? $this->getExceptionMeta($exception) : [];

            if ($exception instanceof JsonApiExceptionInterface) {
                return $exception->getErrorDocument()
                    ->getResponse(new DefaultSerializer(), $response, null, $additionalMeta);
            }

            return $this->toErrorDocument($exception)
                ->getResponse(new DefaultSerializer(), $response, null, $additionalMeta);
        }
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    protected function getExceptionMeta($exception)
    {
        return [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => Debugger::formatTrace($exception->getTrace(), ['format' => 'points'])
        ];
    }

    protected function toErrorDocument(Exception $exception)
    {
        $title = 'Internal Server Error';
        $statusCode = 500;

        if ($exception instanceof HttpException) {
            $title = $exception->getMessage();
            $statusCode = $exception->getCode();
        } elseif ($exception->getCode() >= 400 && $exception->getCode() < 506) {
            $cakeResponse = new Response();
            $httpCodes = $cakeResponse->httpCodes($exception->getCode());

            $title = isset($httpCodes[$exception->getCode()]) ? $httpCodes[$exception->getCode()] : '';
            $statusCode = $exception->getCode();
        }

        /** @var ErrorDocument $errorDocument */
        $errorDocument = new ErrorDocument();
        $errorDocument->setLinks(
            Links::createWithoutBaseUri()->setSelf(
                new Link(Router::url(null, false))
            )
        );

        $errorDocument->addError(
            Error::create()
                ->setStatus($statusCode)
                ->setTitle($title)
        );

        return $errorDocument;
    }
}
