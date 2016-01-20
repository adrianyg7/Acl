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
     * Unauthorized message.
     */
    'unauthorized_message' => 'Unauthorized',

    /**
     * The excepted routes in Acl.
     */
    'except' => [
        'admin.dashboard',
    ],

    /**
     * The additional permissions to register in Acl.
     */
    'additional' => [
        'admin.users.show-all' => 'Show any user profile.',
    ],

    /**
     * Description of permissions in Acl.
     */
    'permission_placeholders' => [
        'admin.users.index' => 'Users list.'
    ],
    
];