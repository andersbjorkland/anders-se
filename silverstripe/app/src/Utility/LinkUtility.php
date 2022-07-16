<?php

namespace App\Utility;

use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;

class LinkUtility
{
    public static function generateURLSegment(
        string $title,
        DataObject $object = null,
        string $classSegmentField = 'URLSegment'
    ): string
    {
        $generatedUrlSegment = URLSegmentFilter::create()->filter($title);
        if ($object !== null) {
            $class = get_class($object);
            $unique = $class::get()->filter([$classSegmentField => $generatedUrlSegment])->first();
            if ($unique !== null && $unique->ID !== $object->ID) {
                $generatedUrlSegment .= '-' . $object->ID;
            }
        }
        return $generatedUrlSegment;
    }
}
