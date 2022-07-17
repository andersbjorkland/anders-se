<?php

namespace App\Utility;

use SilverStripe\Assets\Image;

class EnhancedMarkdownParser
{
    public static $IMAGE_TAG = '/({{image:\d+)(,\s?width:\d+)?(,\s?height:\d+)?(,\s?width:\d+)?}}/'; // matches on the format {{image:123}}
    public static $SUPPORTED_TAG_TYPES = [
        'image'
    ];
    public static $SUPPORTED_TAG_ATTRIBUTES = [
        'width',
        'height'
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

                $width = self::getParameterFromTag($matches[0], 'width');
                $height = self::getParameterFromTag($matches[0], 'height');

                if ($width && $height) {
                    $imageURL = $image->Fill($width, $height)->getAbsoluteURL();
                } elseif ($width) {
                    $imageURL = $image->ScaleWidth($width)->getAbsoluteURL();
                } elseif ($height) {
                    $imageURL = $image->ScaleHeight($height)->getAbsoluteURL();
                }

                $imageMarkdown = sprintf('![%s](%s)', $imageAlt, $imageURL);

                return $imageMarkdown;
            }
            return $matches[0];
        }, $markdown);
        return $markdown;
    }

    public static function getParameterFromTag(string $tag, string $parameter):?int
    {
        if (!in_array($parameter, self::$SUPPORTED_TAG_ATTRIBUTES)) {
            throw new \Exception("$parameter is not a supported tag attribute");
        }

        $startPos = strpos($tag, $parameter . ':');
        if ($startPos === false) {
            return null;
        }
        $startPos += strlen($parameter) + 1;
        $endPos = strpos($tag, '}}', $startPos);
        $alternativeEndPos = strpos($tag, ',', $startPos);
        if ($alternativeEndPos !== false && $alternativeEndPos < $endPos) {
            $endPos = $alternativeEndPos;
        }
        if ($endPos === false) {
            return null;
        }

        $parameterValue = substr($tag, $startPos, $endPos - $startPos);

        return intval($parameterValue);
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
