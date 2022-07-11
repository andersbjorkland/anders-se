<?php

namespace App\Model\Element;

class ArticleElement extends \DNADesign\Elemental\Models\BaseElement
{
    private static $icon = 'font-icon-image';

    private static $db = [
        'Summary' => 'HTMLText'
    ];

    private static $has_one = [
        'Article' => \App\Model\Page\ArticlePage::class
    ];

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Article');
    }
}
