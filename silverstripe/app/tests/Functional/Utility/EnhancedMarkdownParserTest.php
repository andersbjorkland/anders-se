<?php

namespace Test\Functional\Utility;

use App\Utility\EnhancedMarkdownParser;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\DB;

class EnhancedMarkdownParserTest extends FunctionalTest
{
    protected static $fixture_file = 'EnhancedMarkdownParserTest.yml';

    /**
     * @throws \Exception
     */
    public function testReplaceImageTags()
    {
        $hostname = $_ENV['HOSTNAME'] ?? 'localhost';
        $host = "http://$hostname";
        $markdown = '{{image:2}}';
        $expected = '![A test image](' . $host . '/assets/Tests/test.jpeg)';
        $actual = EnhancedMarkdownParser::replaceImageTags($markdown);
        $this->assertEquals($expected, $actual);
    }

    public function testScalesImage()
    {
        $hostname = $_ENV['HOSTNAME'] ?? 'localhost';
        $host = "http://$hostname";
        $markdown = '{{image:2, width:100, height:20}}';
        $expectedStartsWith = '![A test image](' . $host . '/assets/Tests/test__Fill';
        $actual = EnhancedMarkdownParser::replaceImageTags($markdown);
        $this->assertNotFalse(strpos($actual, $expectedStartsWith), 'Image should be scaled');
    }

    public function testScalesWidthOnly()
    {
        $hostname = $_ENV['HOSTNAME'] ?? 'localhost';
        $host = "http://$hostname";
        $markdown = '{{image:2,width:100}}';
        $expectedStartsWith = '![A test image](' . $host . '/assets/Tests/test__ScaleWidth';
        $actual = EnhancedMarkdownParser::replaceImageTags($markdown);
        $this->assertNotFalse(strpos($actual, $expectedStartsWith), 'Image should be scaled');
    }

    public static function tearDownAfterClass(): void
    {
        $tables = array_values(DB::table_list());
        foreach($tables as $table) {
            $sql = "DROP TABLE `$table`";
            DB::query($sql);
        }
    }
}
