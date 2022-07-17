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

    public function testGetWidthFromTag()
    {
        $tag = '{{image:123, width:100}}';
        $tagType = 'image';
        $expected = '100';
        $actual = EnhancedMarkdownParser::getParameterFromTag($tag, 'width');
        $this->assertEquals($expected, $actual);
    }

    public function testGetHeightFromTag()
    {
        $tag = '{{image:123, height:100}}';
        $tagType = 'image';
        $expected = '100';
        $actual = EnhancedMarkdownParser::getParameterFromTag($tag, 'height');
        $this->assertEquals($expected, $actual);
    }

    public function testGetWidthAndHeightFromTag()
    {
        $tag = '{{image:123, width:100, height:20}}';
        $tagType = 'image';
        $expected = '100';
        $actual = EnhancedMarkdownParser::getParameterFromTag($tag, 'width');
        $this->assertEquals($expected, $actual);

        $height = EnhancedMarkdownParser::getParameterFromTag($tag, 'height');
        $this->assertEquals('20', $height);
    }

    public function testGetHeightAndWidthFromTag()
    {
        $tag = '{{image:123, height:100, width:20}}';
        $tagType = 'image';
        $expected = '100';
        $actual = EnhancedMarkdownParser::getParameterFromTag($tag, 'height');
        $this->assertEquals($expected, $actual);

        $width = EnhancedMarkdownParser::getParameterFromTag($tag, 'width');
        $this->assertEquals('20', $width);
    }
}
