<?php

namespace App\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class ImageExtension extends DataExtension
{
    private static $db = [
        'Alt' => 'Varchar(255)',
    ];

    private static $extra_fields = [
        'summary_fields' => [
            'ID' => '#',
            'Title' => 'Title'
        ],
    ];

    public function SmallIcon()
    {
        return $this->owner->Thumbnail(40, 40);
    }

}
