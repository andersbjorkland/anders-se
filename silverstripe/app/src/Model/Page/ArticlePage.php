<?php

namespace App\Model\Page;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\TagField\TagField;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

class ArticlePage extends \Page
{
    private static $table_name = 'ArticlePage';

    private static $allowed_children = [];

    private static $has_one = [
        'Category' => TaxonomyTerm::class
    ];

    private static $many_many = [
        'Tags' => TaxonomyTerm::class
    ];

    public function getCMSFields()
    {
        $taxonomyTypeID = TaxonomyType::get()->filter('Name', 'Tag')->first()->ID ?? 0;
        $fields = parent::getCMSFields();

        $fields->removeByName('Tags');

        $fields->addFieldToTab('Root.Main', TagField::create(
                'Tags',
                'Tags',
                TaxonomyTerm::get()->filter('TypeID', $taxonomyTypeID)
            )
            ->setTitleField('Name')
        );

        return $fields;
    }

    public function onBeforeWrite()
    {
        $typeID = TaxonomyType::get()->filter('Name', 'Tag')->first()->ID ?? 0;
        $tags = $this->Tags()->filter('TypeID', 0);
        foreach ($tags as $tag) {
            $tag->TypeID = $typeID;
            $tag->write();
        }
        parent::onBeforeWrite();
    }
}
