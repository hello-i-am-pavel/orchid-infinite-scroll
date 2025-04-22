<?php

declare(strict_types=1);

namespace Hiap\OrchidInfiniteScroll\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;

/**
 * Class InfiniteScrollTable
 * @package Hiap\OrchidInfiniteScroll\Layouts
 */
abstract class InfiniteScrollTable extends Table
{
    /**
     * @var string
     */
    protected $template = 'hiap-orchid::layouts.infinite-scroll-table';
}
