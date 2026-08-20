<?php

namespace App\DataTables\Html;

use Yajra\DataTables\Html\Builder as BaseBuilder;
use Yajra\DataTables\Html\Column;

/**
 * Project wide DataTables HTML builder.
 *
 * Every list in the admin/customer panel shows its row actions right after the
 * serial column instead of at the far right, so the action column is inserted
 * at index 1 here once rather than being re-ordered in each DataTable class.
 */
class Builder extends BaseBuilder
{
    /**
     * True once addAction() has shifted the data columns one position right.
     */
    protected bool $actionColumnInserted = false;

    /**
     * Add the action column right after the first (serial) column.
     *
     * Pass 'action_position' => 'last' in $attributes to keep the old
     * behaviour of appending the column at the end of the table.
     */
    public function addAction(array $attributes = [], bool $prepend = false): static
    {
        $position = $attributes['action_position'] ?? 'after_first';
        unset($attributes['action_position']);

        if ($prepend || $position === 'last') {
            return parent::addAction($attributes, $prepend);
        }

        $attributes = array_merge([
            'defaultContent' => '',
            'data' => 'action',
            'name' => 'action',
            'title' => 'Action',
            'render' => null,
            'orderable' => false,
            'searchable' => false,
            'exportable' => false,
            'printable' => true,
            'footer' => '',
        ], $attributes);

        // Inline action buttons need far more room than the 55px the columns
        // were given while they were collapsed behind a 3-dots dropdown.
        unset($attributes['width']);
        $attributes['class'] = trim(($attributes['class'] ?? '') . ' ic-action-col');

        $this->collection->splice(1, 0, [new Column($attributes)]);
        $this->actionColumnInserted = true;

        return $this;
    }

    /**
     * Keep the configured default ordering pointing at the same columns.
     *
     * The DataTable classes declare `order` with the column indexes of
     * getColumns(), which knows nothing about the action column. Inserting
     * that column at index 1 pushes every following column one to the right.
     */
    public function parameters(array $attributes = []): static
    {
        if ($this->actionColumnInserted && ! empty($attributes['order'])) {
            $attributes['order'] = array_map(function ($order) {
                if (is_array($order) && isset($order[0]) && is_int($order[0]) && $order[0] >= 1) {
                    $order[0]++;
                }

                return $order;
            }, (array) $attributes['order']);
        }

        return parent::parameters($attributes);
    }
}
