<?php

namespace Adrianyg7\Acl\Permissions;

use Adrianyg7\Acl\Permissions\Permission;
use Adrianyg7\Acl\Permissions\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * The Eloquent permission model name.
     *
     * @var string
     */
    protected $permissionModel;

    /**
     * Constructor
     */
    public function __construct($permissionModel)
    {
        $this->permissionModel = $permissionModel;
    }

    /**
     * Returns the first permission matching the attributes, else, it is registered with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @param  array  $options
     * @return static
     */
    public function firstOrRegister(array $attributes, array $values = [], array $options = [])
    {
        $permissionModel = $this->permissionModel;

        return $permissionModel::firstOrRegister($attributes, $values, $options);
    }

    /**
     * Returns all Permissions.
     *
     * @return \IteratorAggregate
     */
    public function all()
    {
        $permissionModel = $this->permissionModel;

        return $permissionModel::all();
    }
}