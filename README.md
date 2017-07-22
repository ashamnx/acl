# Acl

Laravel Route based ACL. 

## Requirements
* Laravel 5.4 with Passport
## Getting Started
1.Install via 
```composer require ashamnx/acl```

2.Add the package to your application service providers in `config/app.php`.
```
'providers' => [
   ...
   Ashamnx\Acl\AclServiceProvider::class,

],
```
3.Migrate using ``php artisan migrate``

4.Add the middleware to your `app/Http/Kernel.php`.

```php
protected $routeMiddleware = [
    ....
    'acl' => \Ashamnx\Acl\Middleware\AclMiddleware::class,

];
```
5.Add the middleware to your `app/Http/Kernel.php`.

```php
protected $routeMiddleware = [
    ....
    'acl' => 'Ashamnx\Acl\Middleware\AclMiddleware',

];
```
6.Make sure all routes that use the middleware are named and the name has the format [prefix].[resource].[action]
````
Route::middleware(['acl'])->get('test/win', function() {
    return 'Testing win';
})->name('testing.win');
````
7.Run ``php artisan acl:init`` to create an `Administrator` group and give all access.
