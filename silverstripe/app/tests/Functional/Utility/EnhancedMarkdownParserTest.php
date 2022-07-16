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
        $markdown = '{{image:2}}';
        $expected = '![](/assets/Tests/test.jpeg)';
        $actual = EnhancedMarkdownParser::replaceImageTags($markdown);
        $this->assertEquals($expected, $actual);
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
