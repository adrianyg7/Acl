<?php

namespace Adrianyg7\Acl\Roles;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
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
     * The Eloquent user model name.
     *
     * @var string
     */
    protected static $userModel = 'Adrianyg7\Acl\Users\User';

    /**
     * The Eloquent permission model name.
     *
     * @var string
     */
    protected static $permissionModel = 'Adrianyg7\Acl\Permissions\Permission';

    /**
     * Returns the user model.
     *
     * @return string
     */
    public static function getUserModel()
    {
        return static::$userModel;
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
     * Sets the user model.
     *
     * @param  string  $userModel
     * @return void
     */
    public static function setUserModel($userModel)
    {
        static::$userModel = $userModel;
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
     * Relation with users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(static::$userModel);
    }

    /**
     * Relation with permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(static::$permissionModel);
    }
}
