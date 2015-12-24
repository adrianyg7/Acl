<?php

namespace Adrianyg7\Acl\Policies;

use Illuminate\Routing\Route;
use Adrianyg7\Acl\Permissions\Permission;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Adrianyg7\Acl\Permissions\PermissionRepositoryInterface;

class AclPolicy
{
    /**
     * The Gate implementation.
     *
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;

    /**
     * The current route.
     *
     * @var \Illuminate\Routing\Route
     */
    protected $route;

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Routes to be excepted from authorization.
     *
     * @var array
     */
    protected static $except;

    /**
     * Create a new policy instance.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate                    $gate
     * @param  \Illuminate\Routing\Route                                 $route
     * @param  \Illuminate\Contracts\Cache\Repository                    $cache
     * @param  \Adrianyg7\Acl\Permissions\PermissionRepositoryInterface  $permissionRepository
     * @return void
     */
    public function __construct(GateContract $gate, Route $route, CacheContract $cache, PermissionRepositoryInterface $permissionRepository)
    {
        $this->gate = $gate;
        $this->route = $route;
        $this->cache = $cache;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Returns the excepted routes.
     *
     * @return array
     */
    public static function getExcept()
    {
        return static::$except;
    }

    /**
     * Sets the excepted routes.
     *
     * @return void
     */
    public static function setExcept($except)
    {
        static::$except = $except;
    }

    /**
     * Defines the Abilities for the application.
     *
     * @return void
     */
    public function defineAbilities()
    {
        $this->gate->before(function ($user, $ability) {
            if ($user->isSuperuser()) return true;
        });

        foreach ($this->getPermissions() as $permission) {
            $this->gate->define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission->name);
            });
        }
    }

    /**
     * Retreive Permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getPermissions()
    {
        return $this->cache->rememberForever('permissions', function () {
            return $this->permissionRepository->all();
        });
    }

    /**
     * Authorizes a given route.
     *
     * @param  Route  $route
     * @return bool
     */
    public function passesAuthorization()
    {
        if ( ! $this->hasToBeAuthorized() ) return true;

        return $this->gate->allows($this->route->getName());
    }

    /**
     * Determines if routes has to be authorized.
     *
     * @return bool
     */
    protected function hasToBeAuthorized()
    {
        if ( in_array($this->route->getName(), self::$except) ) return false;

        return (bool) $this->route->getName();
    }
}
