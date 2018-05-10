<?php
namespace MyCMS\App\Utils\Users;

use MyCMS\App\Utils\Models\Container;

class MyCMSRole extends Container
{
    public $name;

    public $permissions;

    /**
     * MyCMSRole constructor.
     * @param $role
     * @param $permissions
     * @param $container
     */
    function __construct($role, $permissions, $container)
    {
        $this->name = $role;
        $this->permissions = $permissions;

        parent::__construct($container);
    }

    public function addPermission($permission, $grant = true)
    {
        $this->permissions[$permission] = $grant;
    }

    public function removePermission($permission)
    {
        unset($this->permissions[$permission]);
    }

    public function hasPermission($permission)
    {
        $permissions = $this->container['plugins']->applyEvent('role_has_permission', $this->permissions, $permission, $this->name);
        if(!empty($permissions[$permission]))
            return $permissions[$permission];
        else
            return false;
    }
}