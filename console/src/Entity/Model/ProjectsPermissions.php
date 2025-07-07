<?php

namespace App\Entity\Model;

class ProjectsPermissions
{
    public function __construct(
        private readonly bool $isAdmin,
        private readonly array $permissions,
        private readonly ?array $categoriesPermissions,
    ) {
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

    public function hasCategoryPermission(string $projectId, string $type, array $entityCategoriesUuids): bool
    {
        if ($this->isAdmin) {
            return true;
        }

        if (!$this->categoriesPermissions || empty($this->categoriesPermissions[$projectId][$type])) {
            return true;
        }

        return count(array_intersect($this->categoriesPermissions[$projectId][$type], $entityCategoriesUuids)) >= 1;
    }

    public function getCategoryPermissions(string $projectId, string $type): ?array
    {
        return $this->categoriesPermissions[$projectId][$type] ?? null;
    }
}
