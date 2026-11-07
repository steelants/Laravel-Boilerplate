<?php

namespace SteelAnts\LaravelBoilerplate\Attributes;

use Attribute;

/**
 * Sada akcí (potomek ActionSet) pro model, DTO nebo DataTable - obdoba laravelího #[UsePolicy]:
 *
 *     #[UseActions(InvoiceActions::class)]
 *     class Invoice extends Model
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class UseActions extends ActionsAttribute
{
}
