<?php

use AmRo045\LaravelHashId\Http\Middleware\ResolveHashId;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('can decode the hash id', function() {
    $hashids = new Hashids(salt: str()->password(), minHashLength: 16);
    $userId = fake()->numberBetween(1, 1000);
    $encodedId = $hashids->encode($userId);

    $request = Request::create("/users/$encodedId", 'GET');  
    $request->setRouteResolver(function() use ($encodedId, $userId) {
        $routeStub = $this->createStub(Route::class);
        $routeStub->expects($this->exactly(1))->method('parameters')->willReturn(['user' => $encodedId]);
        $routeStub->expects($this->exactly(1))->method('setParameter')->with('user', $userId);

        return $routeStub;
    });

    $middleware = new ResolveHashId($hashids);
    $middleware->handle($request, fn() => response(null));
});

it('can have multiple encoded parameters', function() {
    $hashids = new Hashids(salt: str()->password(), minHashLength: 16);
    $userId = fake()->numberBetween(1, 1000);
    $postId = fake()->numberBetween(1, 1000);
    $encodedUserId = $hashids->encode($userId);
    $encodedPostId = $hashids->encode($postId);

    $request = Request::create("/users/$encodedUserId/posts/$encodedPostId", 'GET');  
    $request->setRouteResolver(function() use ($encodedUserId, $encodedPostId, $userId, $postId) {
        $routeStub = $this->createStub(Route::class);
        $routeStub->expects($this->exactly(1))->method('parameters')->willReturn([
            'user' => $encodedUserId,
            'post' => $encodedPostId,
        ]);
        $routeStub->expects($this->exactly(2))->method('setParameter')->withConsecutive(
            ['user', $userId],
            ['post', $postId]
        );

        return $routeStub;
    });

    $middleware = new ResolveHashId($hashids);
    $middleware->handle($request, fn() => response(null));
});

it('throws not found exception when the hash id is invalid', function() {
    $hashids = new Hashids(salt: str()->password(), minHashLength: 16);

    $request = Request::create("/users/123", 'GET');  
    $request->setRouteResolver(function() use ($request) {
        return (new Route('GET', 'users/{user}', []))->bind($request);
    });

    $middleware = new ResolveHashId($hashids);
    $middleware->handle($request, fn() => response(null));
})->throws(NotFoundHttpException::class);