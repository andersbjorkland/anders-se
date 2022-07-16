<?php

namespace Test\Unit\Utility;

use App\Utility\EnhancedMarkdownParser;

class EnhancedMarkdownParserTest extends \SilverStripe\Dev\SapphireTest
{
    /**
     * @throws \Exception
     */
    public function testGetIDFromTag()
    {
        $tag = '{{image:123}}';
        $tagType = 'image';
        $expected = '123';
        $actual = EnhancedMarkdownParser::getIDFromTag($tag, $tagType);
        $this->assertEquals($expected, $actual);
    }
}
