<?php

/**
 * Nova-Sortable 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-sortable-5
 * Copyright (c) 2026 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaSortable5\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Nova\Nova;

class SortController extends Controller
{
    public function move(Request $request, string $resource, string $resourceId): JsonResponse
    {
        $guards = config('frontbyte-nova-sortable.guards', ['web']);
        $hasAccess = collect($guards)->contains(fn ($guard) => auth()->guard($guard)->check());

        if (! $hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $resourceClass = Nova::resourceForKey($resource);

        if (! $resourceClass) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $direction = $request->input('direction');
        $orderColumn = $request->input('orderColumn', 'order');

        if (! in_array($direction, ['up', 'down'])) {
            return response()->json(['error' => 'Invalid direction'], 400);
        }

        $model = $resourceClass::newModel()->findOrFail($resourceId);
        $currentOrder = $model->{$orderColumn};

        if ($direction === 'up') {
            $swapModel = $resourceClass::newModel()
                ->where($orderColumn, '<', $currentOrder)
                ->orderByDesc($orderColumn)
                ->first();
        } else {
            $swapModel = $resourceClass::newModel()
                ->where($orderColumn, '>', $currentOrder)
                ->orderBy($orderColumn)
                ->first();
        }

        if (! $swapModel) {
            return response()->json(['error' => 'Already at boundary'], 422);
        }

        $swapOrder = $swapModel->{$orderColumn};
        $model->{$orderColumn} = $swapOrder;
        $swapModel->{$orderColumn} = $currentOrder;

        $model->save();
        $swapModel->save();

        Nova::actionEvent()->forResourceUpdate($request->user(), $model)->save();

        return response()->json([
            'success' => true,
            'moved' => [
                'id' => $model->getKey(),
                'order' => $model->{$orderColumn},
            ],
            'swapped' => [
                'id' => $swapModel->getKey(),
                'order' => $swapModel->{$orderColumn},
            ],
        ]);
    }

    public function reorder(Request $request, string $resource): JsonResponse
    {
        $guards = config('frontbyte-nova-sortable.guards', ['web']);
        $hasAccess = collect($guards)->contains(fn ($guard) => auth()->guard($guard)->check());

        if (! $hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $resourceClass = Nova::resourceForKey($resource);

        if (! $resourceClass) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $ids = $request->input('ids', []);
        $orderColumn = $request->input('orderColumn', 'order');
        $startOrder = $request->input('startOrder', 1);

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        $modelClass = $resourceClass::newModel();

        foreach ($ids as $index => $id) {
            $modelClass->newQuery()
                ->where($modelClass->getKeyName(), $id)
                ->update([$orderColumn => $startOrder + $index]);
        }

        if ($request->user()) {
            $firstModel = $modelClass->find($ids[0]);

            if ($firstModel) {
                Nova::actionEvent()->forResourceUpdate($request->user(), $firstModel)->save();
            }
        }

        return response()->json([
            'success' => true,
            'count' => count($ids),
        ]);
    }
}
