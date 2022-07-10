<?php

namespace App\Model\Element;

use DNADesign\Elemental\Models\BaseElement;
use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\Assets\Image;

class HeroElement extends BaseElement
{
    private static $tableName = 'HeroElement';

    private static $icon = 'font-icon-image';

    private static $db = [
        'Caption' => 'Varchar',
        'Content' => 'HTMLText'
    ];

    private static $has_one = [
        'Button' => Link::class,
        'Image' => Image::class
    ];

    private static $singular_name = 'hero block';

    private static $plural_name = 'hero blocks';

    private static $description = 'A prominent block used for call to action';

    private static $inline_editable = false;

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Hero');
    }

    public function getCMSFields()
    {

        $fields =  parent::getCMSFields();

        $fields->removeByName('ButtonID');

        $linkField = LinkField::create('Button', 'Button', $this);

        $fields->addFieldToTab('Root.Main', $linkField);

        return $fields;
    }
}
