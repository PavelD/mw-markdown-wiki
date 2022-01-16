<?php
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

class MarkdownWikiExtension {

    const PARSE_MARKDOWN = 'MARKDOWN';
    const PARSE_WIKI = 'WIKI';

    /**
     * Adds custom styopes to highlight generated text
     *
     * @param OutputPage &$out
     * @return bool
     */
    public static function onBeforePageDisplay( OutputPage &$out ) {
        global $wgMarkdownWikiHighlight, $wgMarkdownWikiHighlightJs, $wgMarkdownWikiHighlightCss;

        if ( $wgMarkdownWikiHighlight ) {
            $out->addScriptFile( $wgMarkdownWikiHighlightJs );
            $out->addStyle( $wgMarkdownWikiHighlightCss );
            $out->addInlineScript( 'hljs.initHighlightingOnLoad();' );
        }

        return true;
    }

    /**
     * If everything checks out, this hook will parse the given text for Markdown.
     *
     * @param Parser $parser MediaWiki's parser
     * @param string &$text The text to parse
     * @return bool
     */
    public static function onParserBeforeInternalParse( $parser, &$text ) {
        if ( static::shouldParseText( $text ) ) {
            $text = static::stripMagicWorlds( $text );
            $text = static::parseMarkdown( $text );
        } else {
            $text = static::stripMagicWorlds( $text );
        }
        return true;
    }

    /**
     * Converts the given text into markdown.
     *
     * @param string $text The text to parse
     * @return string The parsed text
     */
    protected static function parseMarkdown( $text ) {
        return static::getParser()->parse( $text );
    }

    /**
     * @param string $text The text to check over for our tags if necessary
     * @return bool Whether to parse the given text
     */
    protected static function shouldParseText( $text ) {
        global $wgMarkdownWikiDefaultOn;

        $search = static::getSearchString();

        return (
                ( $wgMarkdownWikiDefaultOn && substr($text, 0, strlen($search)) !== $search )
                || ( !$wgMarkdownWikiDefaultOn && substr($text, 0, strlen($search)) === $search )
        );
    }

    /**
     * @return string The search string
     */
    protected static function getSearchString() {
        global $wgMarkdownWikiDefaultOn, $wgMarkdownWikiToggleFormat;

        return sprintf( $wgMarkdownWikiToggleFormat, $wgMarkdownWikiDefaultOn ? self::PARSE_WIKI : self::PARSE_MARKDOWN );
    }

    /**
     * @param string $text The text remove our tags if necessary
     * @return string stripped text
     **/
    protected static function stripMagicWorlds( $text ) {
        global $wgMarkdownWikiToggleFormat;

        $markdownTag = sprintf( $wgMarkdownWikiToggleFormat, self::PARSE_MARKDOWN );
        $wikiTag = sprintf( $wgMarkdownWikiToggleFormat, self::PARSE_WIKI );

        if(substr($text, 0, strlen( $markdownTag )) === $markdownTag) {
            return substr($text,strlen( $markdownTag ));
        }
        if(substr($text, 0, strlen( $wikiTag )) === $wikiTag) {
            return substr($text,strlen( $wikiTag ));
        }
        return $text;
    }

    /**
     * @return MarkdownWiki
     */
    protected static function getParser() {
        static $parser;

        if ( !$parser ) {
            $parser = new paveld\markdownwiki\MarkdownWiki();
        }

        return $parser;
    }
}
