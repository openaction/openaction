<?php

namespace App\Entity\Model;

class ProjectsPermissions
{
    private bool $isAdmin;
    private array $permissions;

    public function __construct(bool $isAdmin, array $permissions)
    {
        $this->isAdmin = $isAdmin;
        $this->permissions = $permissions;
    }

    public function getConfiguredProjectsIds(): array
    {
        $keys = [];
        foreach ($this->permissions as $projectId => $permissions) {
            foreach ($permissions as $hasPermission) {
                if ($hasPermission) {
                    $keys[] = $projectId;
                    continue 2;
                }
            }
        }

        return $keys;
    }

    public function hasPermission(string $projectId, string $permission): bool
    {
        if ($this->isAdmin) {
            return true;
        }

        return $this->permissions[$projectId][$permission] ?? false;
    }
}
