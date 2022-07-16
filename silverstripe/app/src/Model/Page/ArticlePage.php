<?php

namespace App\Model\Page;

use SilverStripe\Forms\DropdownField;
use SilverStripe\TagField\TagField;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

class ArticlePage extends \Page
{
    private static $table_name = 'ArticlePage';

    private static $db = array(
        'Markdown' => 'Markdown',
    );

    private static $has_one = [
        'Category' => TaxonomyTerm::class
    ];

    private static $many_many = [
        'Tags' => TaxonomyTerm::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Tags');
        $fields->addFieldToTab('Root.Main', TagField::create(
            'Tags',
            'Tags',
            $this->getTags()
        )->setTitleField('Name')
        );

        $fields->removeByName('CategoryID');
        $fields->addFieldToTab('Root.Main', DropdownField::create(
            'CategoryID',
            'Category',
            $this->getCategories()
        ));

        return $fields;
    }

    public function getTags()
    {
        $taxonomyTypeID = TaxonomyType::get()->filter('Name', 'Tag')->first()->ID ?? 0;
        return TaxonomyTerm::get()->filter('TypeID', $taxonomyTypeID);
    }

    public function getCategories()
    {
        $taxonomyTypeID = TaxonomyType::get()->filter('Name', 'Category')->first()->ID ?? 0;
        return TaxonomyTerm::get()->filter('TypeID', $taxonomyTypeID);
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
