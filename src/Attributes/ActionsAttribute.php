<?php

namespace SteelAnts\LaravelBoilerplate\Attributes;

use ReflectionClass;

/** Společný základ atributů #[UseActions] a #[UseBulkActions] - odkaz na třídu sady akcí. */
abstract class ActionsAttribute
{
    /** @param class-string $class */
    public function __construct(public string $class)
    {
    }

    /**
     * Sada akcí deklarovaná na třídě nebo jejím rodiči (atributy PHP se nedědí).
     *
     * @return class-string|null
     */
    public static function on(object|string $target): ?string
    {
        for ($class = new ReflectionClass($target); $class !== false; $class = $class->getParentClass()) {
            $attribute = $class->getAttributes(static::class)[0] ?? null;

            if ($attribute !== null) {
                return $attribute->newInstance()->class;
            }
        }

        return null;
    }
}
