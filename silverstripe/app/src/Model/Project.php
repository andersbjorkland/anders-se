<?php

namespace App\Model;

use App\Admin\ProjectAdmin;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;
use SilverStripe\Versioned\Versioned;

class Project extends \SilverStripe\ORM\DataObject implements CMSPreviewable
{
    private static $table_name = 'Project';
    private static $db = array(
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
        'URLSegment' => 'Varchar(255)',
    );

    private static $has_one = array(
        'Category' => TaxonomyTerm::class,
        'Image' => Image::class,
    );

    // Enable CMS preview for versioned objects
    private static $show_stage_link = true;

    private static $extensions = [
        Versioned::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('CategoryID');
        $categoryField = DropdownField::create(
            'CategoryID',
            'Category',
            $this->getCategories()
        );

        $fields->insertAfter('Description', $categoryField);

        return $fields;
    }

    public function getCategories()
    {
        $taxonomyTypeID = TaxonomyType::get()->filter('Name', 'Category')->first()->ID ?? 0;
        return TaxonomyTerm::get()->filter('TypeID', $taxonomyTypeID);
    }

    public function PreviewLink($action = null)
    {
        $admin = ProjectAdmin::singleton();
        return Controller::join_links(
            $admin->Link(str_replace('\\', '-', $this->ClassName)),
            'cmsPreview',
            $this->ID
        );
    }

    public function getMimeType()
    {
        return 'text/html';
    }

    /**
     * Generates a link to edit this page in the CMS.
     *
     * @return string
     */
    public function CMSEditLink()
    {
        $admin = ProjectAdmin::singleton();
        $sanitisedClassname = str_replace('\\', '-', $this->ClassName);
        return Controller::join_links(
            $admin->Link($sanitisedClassname),
            'EditForm/field/',
            $sanitisedClassname,
            'item',
            $this->ID
        );
    }

    public function forTemplate()
    {
        // If the template for this DataObject is not an "Include" template, use the appropriate type here e.g. "Layout".
        return $this->renderWith(['type' => 'Includes', self::class]);
    }
}
