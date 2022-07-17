<?php

namespace App\Model;

use App\Admin\ArticleAdmin;
use App\Model\Page\ArticleHolder;
use App\Utility\EnhancedMarkdownParser;
use App\Utility\LinkUtility;
use Axllent\Gfmarkdown\Forms\MarkdownEditor;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\TagField\TagField;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;
use SilverStripe\Versioned\Versioned;

class Article extends \SilverStripe\ORM\DataObject implements CMSPreviewable
{
    private static $table_name = 'Article';

    private static $db = array(
        'Title' => 'Varchar(255)',
        'EnhancedMarkdown' => 'Markdown',
        'Markdown' => 'Markdown',
        'URLSegment' => 'Varchar(255)',
    );

    private static $has_one = [
        'Category' => TaxonomyTerm::class
    ];

    private static $many_many = [
        'Tags' => TaxonomyTerm::class,
        'Images' => Image::class
    ];

    private static $owns = [
        'Images'
    ];

    private static $indexes = [
        "URLSegment" => true,
    ];

    // Enable CMS preview for versioned objects
    private static $show_stage_link = true;

    private static $extensions = [
        Versioned::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('URLSegment');
        $urlField = SiteTreeURLSegmentField::create(
            'URLSegment',
            'URL Segment'
        );

        /** @var ArticleHolder $articleHolder */
        $articleHolder = ArticleHolder::get()->first();
        $baseLink = $articleHolder ? $articleHolder->Link() : Director::absoluteBaseURL();
        $urlField
            ->setURLPrefix($baseLink)
            ->setURLSuffix('?stage=Stage')
            ->setDefaultURL('New-Article');
        $fields->addFieldToTab('Root.Main', $urlField);

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

        $fields->removeByName('Markdown');
        $fields->removeByName('EnhancedMarkdown');
        $editor = MarkdownEditor::create('EnhancedMarkdown', 'Page Content (Markdown)')
            ->setRows(20)                   // set number of rows in CMS
            ->setWrap(false)                // disable word wrapping
            ->setHighlightActiveLine(true)  // enable line highlighting
            ->setDescription(
                '<p>Markdown help: </p>'
                . '<ul>'
                    . '<li>'
                        . '<a href="https://guides.github.com/features/mastering-markdown/" target="_blank">'
                            . 'GitHub Markdown Guide'
                        . '</a>'
                    . '</li>'
                    . '<li>Image Relations: <i>{{image:123}}</i><br>This assumes you have uploaded or imported images.</li>'
                . '</ul>'
            )
            ->addExtraClass('cms-description-toggle');


        $fields->addFieldToTab('Root.Main', $editor);

        $fields->removeByName('Images');
        /** @var UploadField $imageField */
        $imageField = UploadField::create('Images', 'Images');

        $fields->addFieldToTab('Root.Images', $imageField);

        $fields->addFieldToTab('Root.Main', $imagesGridField = GridField::create(
            '',
            'Image list',
            $this->Images()
        ));
        $imagesConfig = $imagesGridField->getConfig();
        $imagesColumns = $imagesConfig->getComponentByType(GridFieldDataColumns::class);
        $imagesColumns->setDisplayFields([
            'ID' => '#',
            'Title' => 'Title',
            'SmallIcon' => 'Thumbnail'
        ]);

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
        // Tags
        $typeID = TaxonomyType::get()->filter('Name', 'Tag')->first()->ID ?? 0;
        $tags = $this->Tags()->filter('TypeID', 0);
        foreach ($tags as $tag) {
            $tag->TypeID = $typeID;
            $tag->write();
        }

        // URLSegment
        $articleHolder = ArticleHolder::get()->first();
        $baseLink = $articleHolder ? $articleHolder->Link() : Director::absoluteBaseURL();
        $defaultURLSegment = LinkUtility::generateURLSegment($this->Title, $this);
        if ($this->URLSegment == '' || $this->URLSegment == $baseLink) {
            $this->URLSegment = $defaultURLSegment;
        } else {
            $this->URLSegment = $defaultURLSegment;
        }

        // Markdown
        try {
            $this->Markdown = EnhancedMarkdownParser::parse($this->EnhancedMarkdown);
        } catch (\Exception $e) {
            Debug::message($e->getMessage());
        }

        parent::onBeforeWrite();
    }

    public function PreviewLink($action = null)
    {
        /*
        $articleHolder = ArticleHolder::get()->first();
        $baseLink = $articleHolder ? $articleHolder->Link() : Director::absoluteBaseURL();
        return $baseLink . $this->URLSegment . '?stage=Stage';
        */

        $admin = ArticleAdmin::singleton();
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
        $admin = ArticleAdmin::singleton();
        $sanitisedClassname = str_replace('\\', '-', $this->ClassName);
        return Controller::join_links(
            $admin->Link($sanitisedClassname),
            'EditForm/field/',
            $sanitisedClassname,
            'item',
            $this->ID
        );
        /*
        $link = Controller::join_links(
            CMSPageEditController::singleton()->Link('show'),
            $this->ID
        );
        return Director::absoluteURL($link);
        */
    }

    public function forTemplate()
    {
        // If the template for this DataObject is not an "Include" template, use the appropriate type here e.g. "Layout".
        return $this->renderWith(['type' => 'Includes', self::class]);
    }
}
