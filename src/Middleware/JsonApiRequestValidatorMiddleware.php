<?php

namespace Pie\JsonApi\Middleware;

use Cake\Core\Configure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Negotiation\RequestValidator;
use WoohooLabs\Yin\JsonApi\Request\Request;

class JsonApiRequestValidatorMiddleware
{
    protected $includeOriginalMessageInResponse = true;
    protected $negotiate = true;
    protected $checkQueryParams = true;
    protected $lintBody = true;

    public function __construct(
        $includeOriginalMessageInResponse = true,
        $negotiate = true,
        $checkQueryParams = true,
        $lintBody = true
    ) {
        $this->includeOriginalMessageInResponse = $includeOriginalMessageInResponse;
        $this->negotiate = $negotiate;
        $this->checkQueryParams = $checkQueryParams;
        $this->lintBody = $lintBody;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param                        $next
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        if (1 === preg_match(Configure::read('pie.jsonApi.validatorPathMatchRegex'), $request->getUri()->getPath())) {
            $exceptionFactory = new DefaultExceptionFactory();
            $validator = new RequestValidator($exceptionFactory, $this->includeOriginalMessageInResponse);
            $jsonApiRequest = new Request($request, $exceptionFactory);

            if ($this->negotiate) {
                $validator->negotiate($jsonApiRequest);
            }

            if ($this->checkQueryParams) {
                $validator->validateQueryParams($jsonApiRequest);
            }

            if ($this->lintBody) {
                $validator->lintBody($jsonApiRequest);
            }
        }

        return $next($request, $response);
    }
}