<?php

namespace App\Resolver;


class ElementalResolver
{
    public static function resolveElements($obj, $args, $context, $info)
    {
        if (method_exists($obj, 'Elements')) {
            return $obj->getElements();
        }

        return null;
    }
}
