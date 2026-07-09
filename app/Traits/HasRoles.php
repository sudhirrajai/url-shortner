<?php

namespace App\Traits;

trait HasRoles
{
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isSales(): bool
    {
        return $this->role === 'sales';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }
}
