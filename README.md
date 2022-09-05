
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# Implement robust laravel authorization logic without writing a single line of code

[![Latest Version on Packagist](https://img.shields.io/packagist/v/flixtechs-labs/laravel-authorizer.svg?style=flat-square)](https://packagist.org/packages/flixtechs-labs/laravel-authorizer)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/flixtechs-labs/laravel-authorizer/run-tests?label=tests)](https://github.com/flixtechs-labs/laravel-authorizer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/flixtechs-labs/laravel-authorizer/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/flixtechs-labs/laravel-authorizer/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/flixtechs-labs/laravel-authorizer.svg?style=flat-square)](https://packagist.org/packages/flixtechs-labs/laravel-authorizer)

This package helps you to quickly create strong policy authorization logic in your Laravel application with minimal effort. In most cases the defaults will be just enough and all you'd need to do is 
authorize.

## Installation
You can install the package via composer:

```bash
composer require flixtechs-labs/laravel-authorizer
```
You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-authorizer-config"
```

This is the contents of the published config file:

```php
return [
    'permissions' => [
        'create',
        'update',
        'delete',
        'view all',
        'view',
        'force delete',
        'restore',
    ],
];
```

## Setup

This package depends on the [spatie/laravel-permission](https://github.com/spatie/laravel-permission) package. It's installed automatically when you install this package.

To setup the package all you need to is run the following command:

```bash
php artisan authorizer:setup
```

If your project is ready you can generate the permissions on setup by adding the `--permissions` option:

```bash
php artisan authorizer:setup --permissions
```
You can also generate the policies on setup by adding the `--policies` option:

```bash
php artisan authorizer:setup --policies
```
Or you can generate both on setup by adding the `--permissions` and `--policies` options:

```bash
php artisan authorizer:setup --permissions --policies
```
This will publish the migrations from the spatie/laravel-permission package, migrate the database and generate the permissions and policies.

## Usage

This package generates a batteries included policy skeleton. You just have to generate a policy and authorize in your controllers.

### Generate a policy for one model

```bash
php artisan authorizer:policies:generate Post --model=Post
```

This will generate a `PostPolicy` in the `App\Policies\` namespace. The generated Policy would look something like this:

```php
<?php

namespace App\Policies;

use App\Enums\PostState;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return $user->can('view all posts');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User|null $user
     * @param Post $post
     * @return Response|bool
     */
    public function view(?User $user, Post $post): Response|bool
    {
        return $user->can('view post')
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->can('create post');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Post $post
     * @return Response|bool
     */
    public function update(User $user, Post $post): Response|bool
    {
        return $user->can('update post');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Post $post
     * @return Response|bool
     */
    public function delete(User $user, Post $post): Response|bool
    {
        return $user->can('delete post');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Post $post
     * @return Response|bool
     */
    public function restore(User $user, Post $post): Response|bool
    {
        return $user->can('restore post');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Post $post
     * @return Response|bool
     */
    public function forceDelete(User $user, Post $post): Response|bool
    {
        return $user->can('force delete post');
    }
}
```
Now all you just need to do is authorize the user in your controllers:

```php
<?php

namespace App\Controllers;

use App\Models\Post;

public function __construct()
{
    $this->authorizeResource(Post::class, 'post');
}
```
Or authorize per action

```php
public function update(UpdatePostRequest $request, Post $post)
{
    $this->authorize('update', $post);
}
```

### Generating policies for all models

```bash
php artisan authorizer:policies:generate
```
This will generate policies for all models in your project.

### Generating permissions for one model

```php
php artisan authorizer:permissions:generate --model=Post
```
This will generate all the CRUD permissions for one specific model. You can add additional permission to be generated by adding them to the config file in `config/authorizer.php`

Or you can just generate for all the models

```php
php artisan authorizer:generate:permissions
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Given Ncube](https://github.com/slimgee)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
