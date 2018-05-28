<?php
namespace MyCMS\App\Utils\Users;

use MyCMS\App\Utils\Models\Container;

class MyCMSRoles extends Container
{
    public $roles;
    public $roleObjects;

    function __construct($container)
    {
        $container['roles'] = $this;

        parent::__construct($container);

        $this->loadRolesData();
    }

    public function loadRolesData()
    {
        $this->roles = unserialize(base64_decode($this->container['settings']->getSettingsValue("site_roles")));

        if (empty($this->roles))
            return;

        $this->roleObjects = [];

        foreach (array_keys($this->roles) as $role)
        {
            $this->roleObjects[ $role ] = new MyCMSRole($role, $this->roles[ $role ]['permissions'], $this->container);
        }

        $this->container['plugins']->applyEvent('roles_initialized');
    }

    public function addRole($role, $name, $permissions = [])
    {
        if(empty($role) || isset($this->roles[$role]))
            return;

        $this->roles[$role] = [
            'name' => $name,
            'permissions' => $permissions
        ];

        $this->container['settings']->saveSettings("site_roles", base64_encode(serialize( $this->roles)), true);

        $this->roleObjects[$role] = new MyCMSRole($role, $permissions, $this->container);
        return $this->roleObjects[$role];
    }

    public function removeRole($role)
    {
        if(!isset($this->roleObjects[$role]))
            return;

       unset($this->roleObjects[$role]);
       unset($this->roles[$role]);

       $this->container['settings']->saveSettings("site_roles", base64_encode(serialize( $this->roles)), true);
    }

    /**
     * @param $role
     * @param $permission
     * @param bool $grant
     */
    public function addPermission($role, $permission, $grant = true)
    {
        if(!isset($this->roles[$role]))
            return;

        $this->roles[$role]['permissions'][$permission] = $grant;
        $this->container['settings']->saveSettings("site_roles", base64_encode(serialize( $this->roles)), true);
    }

    /**
     * @param $role
     * @param $permission
     */
    public function removePermission($role, $permission)
    {
        if(!isset($this->roles[$role]))
            return;

        unset($this->roles[$role]['permissions'][$permission]);
        $this->container['settings']->saveSettings("site_roles", base64_encode(serialize( $this->roles)), true);
    }

    public function getRole($role)
    {
        if (isset($this->roleObjects[$role]))
            return $this->roleObjects[$role];
        else
            return null;
    }

    /**
     * @return mixed
     */
    public function getRoleObjects()
    {
        return $this->roleObjects;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }
}