<?php

use paveld\markdownwiki\MarkdownWiki;

/**
 * MarkdownWiki
 * MarkdownWiki MediaWiki parser extension.
 *
 * This is a MediaWiki extension that parse markdown syntax to wiktext using extension of cebe's marsedown parser.
 *
 * @author    Pavel Dobes <konference@lnx.cz>
 * @version   0.3.0
 * @copyright Copyright (C) 2022 Pavel Dobes
 * @license   MIT
 * @link      https://github.com/PavelD/mw-markdown-wiki
 */

class MarkdownWikiExtension
{

    const PARSE_MARKDOWN = 'MARKDOWN';
    const PARSE_WIKI = 'WIKI';

    /**
     * Register any render callbacks with the parser
     *
     * @param Parser $parser
     */
    public static function onParserFirstCallInit(Parser $parser)
    {
        // When the parser sees the <markdown> tag, it executes renderTagSample (see below)
        $parser->setHook('markdown', [self::class, 'renderTagMarkdown']);
    }

    /**
     * If everything checks out, this hook will parse the given text for Markdown.
     *
     * @param Parser $parser MediaWiki's parser
     * @param string &$text The text to parse
     * @return bool
     */
    public static function onParserBeforeInternalParse($parser, &$text)
    {
        if (static::shouldParseText($text)) {
            $text = static::stripMagicWorlds($text);
            $text = static::parseMarkdown($text);
        } else {
            $text = static::stripMagicWorlds($text);
        }
        return true;
    }

    /**
     * @param string $text The text to check over for our tags if necessary
     * @return bool Whether to parse the given text
     */
    protected static function shouldParseText($text)
    {
        global $wgMarkdownWikiDefaultOn;

        $search = static::getSearchString();

        return (
            ($wgMarkdownWikiDefaultOn && substr($text, 0, strlen($search)) !== $search)
            || (!$wgMarkdownWikiDefaultOn && substr($text, 0, strlen($search)) === $search)
        );
    }

    /**
     * @return string The search string
     */
    protected static function getSearchString()
    {
        global $wgMarkdownWikiDefaultOn, $wgMarkdownWikiToggleFormat;

        return sprintf($wgMarkdownWikiToggleFormat, $wgMarkdownWikiDefaultOn ? self::PARSE_WIKI : self::PARSE_MARKDOWN);
    }

    /**
     * @param string $text The text remove our tags if necessary
     * @return string stripped text
     **/
    protected static function stripMagicWorlds($text)
    {
        global $wgMarkdownWikiToggleFormat;

        $markdownTag = sprintf($wgMarkdownWikiToggleFormat, self::PARSE_MARKDOWN);
        $wikiTag = sprintf($wgMarkdownWikiToggleFormat, self::PARSE_WIKI);

        if (substr($text, 0, strlen($markdownTag)) === $markdownTag) {
            return substr($text, strlen($markdownTag));
        }
        if (substr($text, 0, strlen($wikiTag)) === $wikiTag) {
            return substr($text, strlen($wikiTag));
        }
        return $text;
    }

    /**
     * Converts the given text into markdown.
     *
     * @param string $text The text to parse
     * @return string The parsed text
     */
    protected static function parseMarkdown($text)
    {
        return static::getParser()->parse($text);
    }

    /**
     * @return MarkdownWiki
     */
    protected static function getParser()
    {
        static $parser;

        if (!$parser) {
            $parser = new MarkdownWiki();
        }

        return $parser;
    }

    /**
     * Render tag <markdown>
     * @param $input
     * @param array $args
     * @param Parser $parser
     * @param PPFrame $frame
     * @return string
     */
    public static function renderTagMarkdown($input, array $args, Parser $parser, PPFrame $frame)
    {
        return self::parseMarkdown($input);
    }
}
