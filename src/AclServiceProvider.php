<?php

namespace Adrianyg7\Acl;

use Adrianyg7\Acl\Policies\AclPolicy;
use Illuminate\Support\ServiceProvider;
use Adrianyg7\Acl\Events\PermissionsModified;
use Adrianyg7\Acl\Permissions\PermissionRepository;
use Adrianyg7\Acl\Console\Commands\RegisterPermissions;
use Adrianyg7\Acl\Permissions\PermissionRepositoryInterface;

class AclServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/acl.php', 'acl'
        );

        $config = $this->app['config']->get('acl');

        $except = array_get($config, 'except');
        $userModel = array_get($config, 'user');
        $roleModel = array_get($config, 'role');
        $permissionModel = array_get($config, 'permission');

        AclPolicy::setExcept($except);

        if (class_exists($userModel) && method_exists($userModel, 'setRoleModel')) {
            forward_static_call_array([$userModel, 'setRoleModel'], [$roleModel]);
        }

        if (class_exists($userModel) && method_exists($userModel, 'setPermissionModel')) {
            forward_static_call_array([$userModel, 'setPermissionModel'], [$permissionModel]);
        }

        if (class_exists($roleModel) && method_exists($roleModel, 'setUserModel')) {
            forward_static_call_array([$roleModel, 'setUserModel'], [$userModel]);
        }

        if (class_exists($roleModel) && method_exists($roleModel, 'setPermissionModel')) {
            forward_static_call_array([$roleModel, 'setPermissionModel'], [$permissionModel]);
        }

        if (class_exists($permissionModel) && method_exists($permissionModel, 'setRolesModel')) {
            forward_static_call_array([$permissionModel, 'setRolesModel'], [$roleModel]);
        }

        $this->app->bind(PermissionRepositoryInterface::class, function ($app) use ($permissionModel) {
            return new PermissionRepository($permissionModel);
        });

        $this->commands(RegisterPermissions::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->listen(PermissionsModified::class, function ($event) {
            $this->app['cache']->forget('permissions');
        });

        $this->publishes([
            __DIR__ . '/config/acl.php' => config_path('acl.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/migrations' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'Adrianyg7\Acl\Permissions\PermissionRepositoryInterface',
        ];
    }
}
