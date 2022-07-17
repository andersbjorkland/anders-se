<?php

namespace App\Model\Element;

use App\Model\Article;
use App\Model\Page\ArticlePage;

class ArticleElement extends \DNADesign\Elemental\Models\BaseElement
{
    private static $icon = 'font-icon-image';

    private static $db = [
        'Summary' => 'HTMLText'
    ];

    private static $has_one = [
        'Article' => Article::class
    ];

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Article');
    }
}
