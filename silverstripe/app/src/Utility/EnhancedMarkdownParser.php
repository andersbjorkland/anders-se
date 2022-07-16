<?php

namespace App\Utility;

use SilverStripe\Assets\Image;

class EnhancedMarkdownParser
{
    public static $IMAGE_TAG = '/({{image:\d+}})/'; // matches on the format {{image:123}}
    public static $SUPPORTED_TAG_TYPES = [
        'image'
    ];

    public static function parse($markdown)
    {
        $markdown = self::replaceImageTags($markdown);
        return $markdown;
    }

    /**
     * @param string $markdown
     * @return string
     * @throws \Exception
     */
    public static function replaceImageTags(string $markdown):string
    {
        $markdown = preg_replace_callback(self::$IMAGE_TAG, function ($matches) {
            $tagType = 'image';
            $tagID = self::getIDFromTag($matches[0], $tagType);
            $image = Image::get()->byID($tagID);
            if ($image) {
                /** @var Image $image */
                $imageURL = $image->getAbsoluteURL();
                $imageAlt = $image->Alt;

                $imageMarkdown = sprintf('![%s](%s)', $imageAlt, $imageURL);

                return $imageMarkdown;
            }
            return $matches[0];
        }, $markdown);
        return $markdown;
    }

    /**
     * @param string $tag
     * @param string $tagType
     * @return string|null
     * @throws \Exception
     */
    public static function getIDFromTag(string $tag, string $tagType = 'image'): ?string
    {
        if (!in_array($tagType, self::$SUPPORTED_TAG_TYPES)) {
            throw new \Exception("Tag type '$tagType' is not supported");
        }

        $tag = str_replace('{{' . $tagType . ':', '', $tag);
        $tag = str_replace('}}', '', $tag);

        return $tag;
    }

}
