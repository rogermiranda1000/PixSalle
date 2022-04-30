<?php
declare(strict_types=1);

namespace Salle\PixSalle\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response as SlimResponse;

final class RequireLoginMiddleware
{
    public function __invoke(Request $request, RequestHandler $next): Response
    {
        if (empty($_SESSION['user_id'])) {
            $response = new SlimResponse();
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response->withHeader('Location', $routeParser->urlFor("signIn"))
                ->withStatus(307);
        }

        return $next->handle($request);
    }
}