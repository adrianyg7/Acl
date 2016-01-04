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

        $this->registerAclExceptions($config);
        $this->registerModels($config);

        $this->app->bind(PermissionRepositoryInterface::class, function ($app) use ($config) {
            $permissionModel = array_get($config, 'permission');

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
            __DIR__ . '/database/migrations' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Registers the Acl exception routes.
     *
     * @param  array $config
     * @return void
     */
    public function registerAclExceptions($config)
    {
        $except = array_get($config, 'except');

        AclPolicy::setExcept($except);
    }

    /**
     * Registers the models from config.
     *
     * @param  array $config
     * @return void
     */
    public function registerModels($config)
    {
        $userModel = array_get($config, 'user');
        $roleModel = array_get($config, 'role');
        $permissionModel = array_get($config, 'permission');

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
