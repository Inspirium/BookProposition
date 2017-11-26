<?php

namespace Inspirium\BookProposition;

use Illuminate\Support\ServiceProvider;
use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\BookProposition\Observers\ApprovalRequestObserver;

class BookPropositionServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        ApprovalRequest::observe(ApprovalRequestObserver::class);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
