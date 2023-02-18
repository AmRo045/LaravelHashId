# Laravel Hash Id

This package uses [Hashids](https://hashids.org/php/) under the hood.

## Installation

You can add this package to your project by running the following command in your project root directory:

```
composer require amro045/laravelhashid
```

## Configuration

Once you added the package, you can configure the package by publishing the package default configuration using this command:

```
php artisan laravelhashid:install
```

## Usage

First add the `HasHashId` trait to your model:

```php
<?php

namespace App\Models;

use AmRo045\LaravelHashId\Traits\HasHashId;

class User extends Model
{
    use HasHashId;
}

```

this trait will add the `hash_id` property to your model, so you can use this property to create a URL with hash-id. Example:
```php
route('users.show', $user->hash_id);
// output: https://example.com/users/q1Ba7DXlJ9lKg6V2
```

then add the `ResolveHashId` middleware in `app\Http\Kernel.php` file:

```php
/**
 * The application's middleware aliases.
 *
 * Aliases may be used to conveniently assign middleware to routes and groups.
 *
 * @var array<string, class-string|string>
 */
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'signed' => \App\Http\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    'hashid' => \AmRo045\LaravelHashId\Http\Middleware\ResolveHashId::class, // 🖐️
];

/**
 * The priority-sorted list of middleware.
 *
 * Forces non-global middleware to always be in the given order.
 *
 * @var string[]
 */
protected $middlewarePriority = [
    \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class,
    \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
    \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
    \AmRo045\LaravelHashId\Http\Middleware\ResolveHashId::class, // Add before the SubstituteBindings middleware
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \Illuminate\Auth\Middleware\Authorize::class,
];
```

**NOTE**: For more information about the `$middlewarePriority` array, checkout the [documentations](https://laravel.com/docs/10.x/middleware#sorting-middleware).

and finally you can use `ResolveHashId` middleware like this:

```php
Route::get('/users/{user}', function(User $user) {
    return $user;
})->middleware('hashid');
```

## Middleware Parameters

You may want to ignore some parameters from decoding. To do so, you can pass `ignore` parameter to `hashid` middleware like this:

```php
Route::get('/users/{user}/posts/{post}/comments/{comment}', function(User $user, Post $post, Comment $comment) {
    return func_get_args();
})->middleware('hashid:ignore=post&comment');
```
if you want to decode only specific parameters, you can pass the `only` parameter to `hashid` middleware like this:

```php
Route::get('/users/{user}/posts/{post}/comments/{comment}', function(User $user, Post $post, Comment $comment) {
    return func_get_args();
})->middleware('hashid:only=user');
```