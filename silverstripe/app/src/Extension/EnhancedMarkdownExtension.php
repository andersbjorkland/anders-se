<?php

namespace App\Extension;

use App\Utility\EnhancedMarkdownParser;
use Axllent\Gfmarkdown\Forms\MarkdownEditor;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDataColumns;

class EnhancedMarkdownExtension extends \SilverStripe\ORM\DataExtension
{
    private static $db = [
        'Markdown' => 'Markdown',
        'EnhancedMarkdown' => 'Markdown'
    ];

    private static $many_many = [
        'MarkdownImages' => Image::class
    ];

    private static $owns = [
        'MarkdownImages'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('Markdown');
        $fields->removeByName('EnhancedMarkdown');
        $fields->removeByName('MarkdownImages');

        $editor = MarkdownEditor::create('EnhancedMarkdown', 'Page Content (Markdown)')
            ->setRows(20)                   // set number of rows in CMS
            ->setWrap(false)                // disable word wrapping
            ->setHighlightActiveLine(true)  // enable line highlighting
            ->setDescription(EnhancedMarkdownParser::$HTML_DESCRIPTION)
            ->addExtraClass('cms-description-toggle');
        $fields->addFieldToTab('Root.Markdown', $editor);

        /** @var UploadField $imageField */
        $imageField = UploadField::create('MarkdownImages', 'Images');

        $fields->addFieldToTab('Root.MarkdownImages', $imageField);

        $fields->addFieldToTab('Root.Markdown', $imagesGridField = GridField::create(
            '',
            'Image list',
            $this->owner->MarkdownImages()
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

    public function onBeforeWrite()
    {
        try {
            $this->owner->Markdown = EnhancedMarkdownParser::parse($this->owner->EnhancedMarkdown);
        } catch (\Exception $e) {
            Debug::message($e->getMessage());
        }
        parent::onBeforeWrite();
    }
}
