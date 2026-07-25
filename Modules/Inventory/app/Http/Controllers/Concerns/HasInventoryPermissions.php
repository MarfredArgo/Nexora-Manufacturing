<?php

namespace Modules\Inventory\Http\Controllers\Concerns;

trait HasInventoryPermissions
{
    private function getPositionLevel(): int
    {
        $position = strtolower((string) session('employee_position', ''));

        if (str_contains($position, 'manager')) return 3;
        if (str_contains($position, 'controller')) return 2;
        if (str_contains($position, 'staff') || str_contains($position, 'warehouse') || str_contains($position, 'worker')) return 1;

        return 0;
    }

    private function isInventoryManager(): bool
    {
        return $this->getPositionLevel() >= 3;
    }

    private function currentUserId(): int
    {
        return (int) session('employee_id');
    }

    private function canCancelRequest(int $ownerId): bool
    {
        return $this->getPositionLevel() >= 2 || $this->currentUserId() === $ownerId;
    }
}
