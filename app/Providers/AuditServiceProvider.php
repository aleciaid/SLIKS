<?php

namespace App\Providers;

use App\Models\Applicant;
use App\Models\Collectiv;
use App\Models\User;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Applicant::observe(AuditObserver::class);
        Collectiv::observe(AuditObserver::class);
        User::observe(AuditObserver::class);
    }
}
