<?php

declare(strict_types=1);

namespace Hiap\OrchidInfiniteScroll;

use Illuminate\Support\ServiceProvider;

/**
 * Class HiapOrchidInfiniteScrollServiceProvider
 * @package Hiap\OrchidInfiniteScroll
 */
class HiapOrchidInfiniteScrollServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'hiap-orchid');
    }
}
