<?php

/**
 * Nova-Sortable 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-sortable-5
 * Copyright (c) 2026 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaSortable5;

use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Add this trait to your Nova Resource to automatically sort by the sortable column.
 *
 * Usage: use SortableResource; in your Nova Resource class.
 */
trait SortableResource
{
    public static function indexQuery(NovaRequest $request, $query)
    {
        $model = $query->getModel();

        if (method_exists($model, 'getSortableColumn')) {
            // Only apply default sort when user hasn't explicitly sorted
            if (empty($request->get('orderBy'))) {
                $query->getQuery()->orders = [];
                $direction = config('frontbyte-nova-sortable.order_direction', 'asc');
                $query->orderBy($model->getSortableColumn(), $direction);
            }
        }

        return $query;
    }
}
