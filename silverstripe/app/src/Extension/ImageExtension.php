<?php

namespace App\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class ImageExtension extends DataExtension
{
    private static $db = [
        'Alt' => 'Varchar(255)',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab('Root.Main', $altField = new \SilverStripe\Forms\TextField('Alt', 'Alt'));
        $altField->setDescription('Alternative text for the image');
    }
}
