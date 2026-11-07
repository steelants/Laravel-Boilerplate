<?php

namespace SteelAnts\LaravelBoilerplate\Attributes;

use Attribute;

/**
 * Sada hromadných akcí (potomek BulkActionSet) pro DataTable s traitem WithBulkActions:
 *
 *     #[UseBulkActions(InvoiceBulkActions::class)]
 *     class DataTable extends DataTableComponent
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class UseBulkActions extends ActionsAttribute
{
}
