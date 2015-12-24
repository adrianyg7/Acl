<?php

namespace Adrianyg7\Acl\Permissions;

interface PermissionRepositoryInterface
{
    /**
     * Returns the first permission matching the attributes, else, it is registered with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @param  array  $options
     * @return static
     */
    public function firstOrRegister(array $attributes, array $values = [], array $options = []);

    /**
     * Returns all Permissions.
     *
     * @return \IteratorAggregate
     */
    public function all();
}