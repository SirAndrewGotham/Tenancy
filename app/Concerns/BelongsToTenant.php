<?php

namespace App\Concerns;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongstoTenant()
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (auth()->check())
            {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        $this->belongsTo(Tenant::class);
    }
}
