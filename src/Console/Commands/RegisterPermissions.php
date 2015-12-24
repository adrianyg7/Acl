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
        foreach ($this->routesToRegister() as $route) {
            $this->permissionRepository->firstOrRegister([
                'name' => $route->getName(),
            ], [
                'description' => $route->getName(),
            ]);
        }

        $this->events->fire(new PermissionsModified);
    }

    /**
     * Retreives the routes to be registered.
     *
     * @return \Illuminate\Support\Collection
     */
    public function routesToRegister()
    {
        return array_filter($this->router->getRoutes()->getRoutes(), function ($route) {
            return $route->getName() and ! in_array($route->getName(), AclPolicy::getExcept());
        });
    }
}
