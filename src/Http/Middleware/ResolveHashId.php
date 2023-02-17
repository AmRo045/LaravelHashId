<?php

namespace AmRo045\LaravelHashId\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveHashId
{
    private Hashids $hashids;

    public function __construct(Hashids $hashids)
    {
        $this->hashids = $hashids;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        foreach($route->parameters() as $key => $value) {
            $decodedValue = $this->hashids->decode($value);
            
            if (!$decodedValue)
                abort(Response::HTTP_NOT_FOUND);

            $route->setParameter($key, $decodedValue);
        }

        return $next($request);
    }
}
