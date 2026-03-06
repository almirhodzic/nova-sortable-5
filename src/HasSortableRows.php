<?php

/**
 * Nova-Sortable 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-sortable-5
 * Copyright (c) 2026 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaSortable5;

use Illuminate\Database\Eloquent\Builder;

trait HasSortableRows
{
    public static function bootHasSortableRows(): void
    {
        static::addGlobalScope('sortable-order', function (Builder $query) {
            if (empty($query->getQuery()->orders)) {
                $direction = config('frontbyte-nova-sortable.order_direction', 'asc');
                $query->orderBy((new static)->getSortableColumn(), $direction);
            }
        });

        static::creating(function ($model) {
            if (is_null($model->{$model->getSortableColumn()})) {
                $model->{$model->getSortableColumn()} = static::query()->max($model->getSortableColumn()) + 1;
            }
        });
    }

    public function getSortableColumn(): string
    {
        return $this->sortableColumn ?? config('frontbyte-nova-sortable.default_column', 'order');
    }

    public function scopeOrderBySortable($query, string $direction = 'asc')
    {
        return $query->orderBy($this->getSortableColumn(), $direction);
    }

    public function moveToPosition(int $newPosition): void
    {
        $column = $this->getSortableColumn();
        $currentPosition = $this->{$column};

        if ($currentPosition === $newPosition) {
            return;
        }

        if ($newPosition < $currentPosition) {
            static::query()
                ->where($column, '>=', $newPosition)
                ->where($column, '<', $currentPosition)
                ->increment($column);
        } else {
            static::query()
                ->where($column, '>', $currentPosition)
                ->where($column, '<=', $newPosition)
                ->decrement($column);
        }

        $this->{$column} = $newPosition;
        $this->save();
    }
}
