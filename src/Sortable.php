<?php

/**
 * Nova-Sortable 5 by Almir Hodzic
 * Original: https://github.com/almirhodzic/nova-sortable-5
 * Copyright (c) 2026 Almir Hodzic
 * MIT License
 */

namespace AlmirHodzic\NovaSortable5;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class Sortable extends Field
{
    public $component = 'nova-sortable';

    protected string $orderColumn;

    protected bool $showDragHandle;

    protected bool $showSortArrows;

    protected bool $showOrderNumber;

    protected bool $showToast;

    protected bool $autoAssignOnCreate = true;

    public function __construct($name = 'Order', $attribute = null, ?callable $resolveCallback = null)
    {
        $defaultColumn = config('frontbyte-nova-sortable.default_column', 'order');

        parent::__construct($name, $attribute ?? $defaultColumn, $resolveCallback);

        $this->hideWhenCreating();
        $this->hideWhenUpdating();

        $this->orderColumn = $attribute ?? $defaultColumn;
        $this->showDragHandle = config('frontbyte-nova-sortable.show_drag_handle', true);
        $this->showSortArrows = config('frontbyte-nova-sortable.show_order_arrows', true);
        $this->showOrderNumber = config('frontbyte-nova-sortable.show_order_number', true);
        $this->showToast = config('frontbyte-nova-sortable.show_toast', true);
    }

    public function orderColumn(string $column): self
    {
        $this->orderColumn = $column;
        $this->attribute = $column;

        return $this;
    }

    public function showDragHandle(bool $show = true): self
    {
        $this->showDragHandle = $show;

        return $this;
    }

    public function hideDragHandle(): self
    {
        return $this->showDragHandle(false);
    }

    public function showSortArrows(bool $show = true): self
    {
        $this->showSortArrows = $show;

        return $this;
    }

    public function hideSortArrows(): self
    {
        return $this->showSortArrows(false);
    }

    public function showOrderNumber(bool $show = true): self
    {
        $this->showOrderNumber = $show;

        return $this;
    }

    public function hideOrderNumber(): self
    {
        return $this->showOrderNumber(false);
    }

    public function showToast(bool $show = true): self
    {
        $this->showToast = $show;

        return $this;
    }

    public function hideToast(): self
    {
        return $this->showToast(false);
    }

    public function autoAssignOnCreate(bool $auto = true): self
    {
        $this->autoAssignOnCreate = $auto;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'orderColumn' => $this->orderColumn,
            'showDragHandle' => $this->showDragHandle,
            'showSortArrows' => $this->showSortArrows,
            'showOrderNumber' => $this->showOrderNumber,
            'showToast' => $this->showToast,
            'autoAssignOnCreate' => $this->autoAssignOnCreate,
        ]);
    }
}
