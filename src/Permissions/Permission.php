<?php

namespace Adrianyg7\Acl\Permissions;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The Eloquent roles model name.
     *
     * @var string
     */
    protected static $roleModel = 'Adrianyg7\Acl\Roles\Role';

    /**
     * Returns the role model.
     *
     * @return string
     */
    public static function getRoleModel()
    {
        return static::$roleModel;
    }

    /**
     * Sets the roles model.
     *
     * @param  string  $roleModel
     * @return void
     */
    public static function setRolesModel($roleModel)
    {
        static::$roleModel = $roleModel;
    }

    /**
     * Relation with roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(static::$roleModel);
    }

    /**
     * Returns the first permission matching the attributes, else, it is registered with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @param  array  $options
     * @return static
     */
    public static function firstOrRegister(array $attributes, array $values = [], array $options = [])
    {
        $permission = static::firstOrNew($attributes);

        if ( ! $permission->exists ) {
            $permission->fill($values)->save($options);
        }

        return $permission;
    }
}
