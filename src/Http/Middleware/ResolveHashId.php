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

    public function handle(Request $request, Closure $next, ?string $parameters = ''): Response
    {
        if (static::hasIgnoredParameters($parameters)) {
            $this->decode($request, function ($parameterName) use ($parameters) {
                $parametersToIgnore = static::extractParameters($parameters);
                return in_array($parameterName, $parametersToIgnore);
            });
        } else if (static::hasOnlyParameters($parameters)) {
            $this->decode($request, function ($parameterName) use ($parameters) {
                $parametersToDecode = static::extractParameters($parameters);
                return !in_array($parameterName, $parametersToDecode);
            });
        } else {
            $this->decode($request);
        }

        return $next($request);
    }

    private function decode(Request $request, ?Closure $skipCondition = null): void
    {
        $route = $request->route();

        foreach ($route->parameters() as $key => $value) {
            if ($skipCondition && $skipCondition($key))
                continue;

            $decodedValue = $this->hashids->decode($value);

            if (!$decodedValue)
                abort(Response::HTTP_NOT_FOUND);

            $decodedValue = is_array($decodedValue) ? current($decodedValue) : $decodedValue;

            $route->setParameter($key, $decodedValue);
        }
    }

    private static function hasIgnoredParameters(string $parameters): bool
    {
        return str_starts_with($parameters, 'ignore');
    }

    private static function hasOnlyParameters(string $parameters): bool
    {
        return str_starts_with($parameters, 'only');
    }

    private static function extractParameters(string $parameters): array
    {
        if (!$parameters)
            return [];

        $actualParameters = explode('=', $parameters)[1];

        return explode('&', $actualParameters);
    }
}
