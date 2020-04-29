<?php
declare(strict_types=1);

namespace Authorization\Service;

class PermissionsManager
{
    /**
     * This method should check if connection is authorized (user is logged in)
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        // TODO: implement authorization check

        return true;
    }

    /**
     * This method should check if user have enough permissions to do make some action
     *
     * @param array $routePermissions
     * @return bool
     */
    public function isAllowed(array $routePermissions): bool
    {
        // TODO: implement current user permissions resolver access

        // TODO: implement user permissions and route permissions check

        return true;
    }
}
