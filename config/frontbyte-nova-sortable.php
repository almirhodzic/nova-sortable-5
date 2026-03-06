<?php

/**
 * Nova-Sortable 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-sortable-5
 * Copyright (c) 2026 Almir Hodzic
 * MIT License
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Order Column
    |--------------------------------------------------------------------------
    |
    | The default database column used for sorting. This applies to all models
    | using the HasSortableRows trait, unless overridden per model via the
    | $sortableColumn property.
    |
    */
    'default_column' => 'sort_order',

    /*
    |--------------------------------------------------------------------------
    | Default Order Direction
    |--------------------------------------------------------------------------
    |
    | The default sort direction for the sortable column. Supported: 'asc', 'desc'.
    |
    */
    'order_direction' => 'asc',

    /*
    |--------------------------------------------------------------------------
    | Show Drag Handle
    |--------------------------------------------------------------------------
    |
    | Whether to show the drag handle on the index view by default.
    |
    */
    'show_drag_handle' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Order Arrows
    |--------------------------------------------------------------------------
    |
    | Whether to show the up/down arrow buttons on the index view by default.
    |
    */
    'show_order_arrows' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Order Number
    |--------------------------------------------------------------------------
    |
    | Whether to show the order number on the index view by default.
    |
    */
    'show_order_number' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Toast Message
    |--------------------------------------------------------------------------
    |
    | Whether to show a success toast message after reordering.
    |
    */
    'show_toast' => true,

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | The authentication guards used for the sortable API routes.
    |
    */
    'guards' => ['web'],

];
