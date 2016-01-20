<?php

namespace Adrianyg7\Acl\Console\Commands;

use Illuminate\Routing\Router;
use Illuminate\Console\Command;
use Adrianyg7\Acl\Policies\AclPolicy;
use Adrianyg7\Acl\Events\PermissionsModified;
use Adrianyg7\Acl\Permissions\PermissionRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

class RegisterPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers all named routes to permissions table.';

    /**
     * The application router.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * The application router.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The permission repository implementation.
     *
     * @var \Adrianyg7\Acl\Permissions\PermissionRepositoryInterface
     */
    protected $permissionRepository;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Routing\Router                                $router
     * @param  \Illuminate\Contracts\Events\Dispatcher                   $events
     * @param  \Adrianyg7\Acl\Permissions\PermissionRepositoryInterface  $permissionRepository
     * @return void
     */
    public function __construct(Router $router, DispatcherContract $events, PermissionRepositoryInterface $permissionRepository)
    {
        parent::__construct();

        $this->router = $router;
        $this->events = $events;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->permissionsToRegister() as $name => $description) {
            $this->permissionRepository->firstOrRegister([
                'name' => $name,
            ], [
                'description' => $description,
            ]);
        }

        $this->events->fire(new PermissionsModified);
    }

    /**
     * Retreives the permissions to be registered.
     *
     * @return array
     */
    public function permissionsToRegister()
    {
        $config = config('acl');
        $permissionPlaceholders = array_get($config, 'permission_placeholders');
        $additionalPermissions = array_get($config, 'additional');
        $permissionsToRegister = [];

        $routes = array_filter($this->router->getRoutes()->getRoutes(), function ($route) {
            return $route->getName() and ! in_array($route->getName(), AclPolicy::getExcept());
        });

        foreach ($routes as $route) {
            if (array_key_exists($route->getName(), $permissionPlaceholders)) {
                $permissionsToRegister[$route->getName()] = $permissionPlaceholders[$route->getName()];
            } else {
                $permissionsToRegister[$route->getName()] = $route->getName();
            }
        }

        return array_merge($permissionsToRegister, $additionalPermissions);
    }
}
