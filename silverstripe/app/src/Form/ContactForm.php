<?php

namespace App\Form;

use SilverStripe\ORM\DataObject;

class ContactForm extends DataObject
{
    private static $db = [
        'Name' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'Message' => 'HTMLText',
    ];

    public function canCreate($member = null, $context = [])
    {
        return true;
    }
}
