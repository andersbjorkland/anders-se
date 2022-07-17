<?php

namespace App\Extension;

use App\Model\Page\ArticleHolder;
use App\Model\Page\ProjectHolder;
use App\Utility\LinkUtility;
use Page;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\Control\Director;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

class URLSegmentExtension extends \SilverStripe\ORM\DataExtension
{
    private static $db = [
        'URLSegment' => 'Varchar(255)',
    ];

    private static $indexes = [
        "URLSegment" => true,
    ];

    private static $holder_map = [
        'Article' => ArticleHolder::class,
        'Project' => ProjectHolder::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('URLSegment');
        $urlField = SiteTreeURLSegmentField::create(
            'URLSegment',
            'URL Segment'
        );

        $ownerClass = $this->getOwnerClassName();

        /** @var Page $holder */
        $holder = $this->getHolder();

        $baseLink = $holder ? $holder->Link() : Director::absoluteBaseURL();

        $urlField
            ->setURLPrefix($baseLink)
            ->setURLSuffix('?stage=Stage')
            ->setDefaultURL('New-' . $ownerClass);
        $fields->addFieldToTab('Root.Main', $urlField);

    }

    public function onBeforeWrite()
    {
        $holder = $this->getHolder();
        $baseLink = $holder ? $holder->Link() : Director::absoluteBaseURL();
        $defaultURLSegment = LinkUtility::generateURLSegment($this->owner->Title, $this->owner);
        if ($this->owner->URLSegment == '' || $this->owner->URLSegment == $baseLink) {
            $this->owner->URLSegment = $defaultURLSegment;
        } else {
            $this->owner->URLSegment = $defaultURLSegment;
        }
        parent::onBeforeWrite();
    }

    public function getHolder(): ?DataObject
    {
        switch ($this->getOwnerClassName()) {
            case 'Article':
                return ArticleHolder::get()->first();
            case 'Project':
                return ProjectHolder::get()->first();
            default:
                return null;
        }
    }

    public function getOwnerClassName():string
    {
        $ownerClass = $this->owner->getClassName();
        $ownerClass = explode('\\', $ownerClass);
        $ownerClass = array_pop($ownerClass);
        return $ownerClass;
    }
}
