<?php

namespace Adrianyg7\Acl\Users;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'superuser' => 'bool',
    ];

    /**
     * The User Permissions.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $permissions;

    /**
     * The Eloquent role model name.
     *
     * @var string
     */
    protected static $roleModel = 'Adrianyg7\Acl\Roles\Role';

    /**
     * The Eloquent permission model name.
     *
     * @var string
     */
    protected static $permissionModel = 'Adrianyg7\Acl\Permissions\Permission';

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
     * Returns the permission model.
     *
     * @return string
     */
    public static function getPermissionModel()
    {
        return static::$permissionModel;
    }

    /**
     * Sets the role model.
     *
     * @param  string  $roleModel
     * @return void
     */
    public static function setRoleModel($roleModel)
    {
        static::$roleModel = $roleModel;
    }

    /**
     * Sets the permission model.
     *
     * @param  string  $permissionModel
     * @return void
     */
    public static function setPermissionModel($permissionModel)
    {
        static::$permissionModel = $permissionModel;
    }

    /**
     * Encrypt user password
     *
     * @param  string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Scope a query to not include SuperUsers.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotSuperuser($query)
    {
        $query->where('superuser', false);
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
     * Determines if user is a Superuser.
     *
     * @return bool
     */
    public function isSuperuser()
    {
        return $this->superuser;
    }

    /**
     * Get the user permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissions()
    {
        if ( ! isset($this->permissions) ) {
            $permissionModel = $this::$permissionModel;

            $this->permissions = $permissionModel::whereHas('roles', function ($query) {
                $query->whereHas('users', function ($query) {
                    $query->where('id', $this->id);
                });
            })->get();
        }

        return $this->permissions;
    }

    /**
     * Determines if user has ALL given permissions.
     *
     * @param  string|array $permissions
     * @return boolean
     */
    public function hasPermission($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : func_get_args();

        foreach ($permissions as $permission) {
            if ( ! $this->permissions()->contains('name', $permission) ) return false;
        }

        return true;
    }

    /**
     * Determines if user has ANY given permissions.
     *
     * @param  string|array $permissions
     * @return boolean
     */
    public function hasAnyPermission($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : func_get_args();

        foreach ($permissions as $permission) {
            if ( $this->permissions()->contains('name', $permission) ) return true;
        }

        return false;
    }
}
