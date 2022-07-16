<?php

namespace App\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class ImageFormExtension extends DataExtension
{
    public function updateFormFields(FieldList $fields)
    {
        $fields->insertAfter('Title', $altField = new \SilverStripe\Forms\TextField('Alt', 'Alt'));
        $altField->setDescription('Alternative text for the image');
    }
}
