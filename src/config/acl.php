<?php

return [

    /**
     * The User model used in Acl.
     */
    'user' => 'Adrianyg7\Acl\Users\User',

    /**
     * The Role model used in Acl.
     */
    'role' => 'Adrianyg7\Acl\Roles\Role',

    /**
     * The Permission model used in Acl.
     */
    'permission' => 'Adrianyg7\Acl\Permissions\Permission',

    /**
     * The excepted routes in Acl.
     */
    'except' => [
        'admin.dashboard'
    ],
    
];