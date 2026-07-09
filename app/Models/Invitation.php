<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    protected $fillable = ['email', 'role', 'company_id', 'new_company_name', 'invited_by_id', 'token', 'accepted_at'];

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
