<?php
declare(strict_types=1);

namespace Salle\PixSalle\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response as SlimResponse;

final class IntegerIdCheckerMiddleware
{
    public function __invoke(Request $request, RequestHandler $next): Response
    {
        // getting the argumente here it's more complicated [from https://discourse.slimframework.com/t/solved-args-of-route-in-middleware/3452/5]
        $route = \Slim\Routing\RouteContext::fromRequest($request)->getRoute();
        $routeArguments = $route->getArguments();
        $id = $routeArguments['id'];
        
        if (!is_numeric($id)) {
            $error = [];
            $error['message'] = "The post ID must be a number";

            $response = new SlimResponse();
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        //$route->setArgument('id', intval($id));
        return $next->handle($request);
    }
}