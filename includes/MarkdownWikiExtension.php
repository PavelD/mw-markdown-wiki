<?php
/**
 * MarkdownWiki
 * MarkdownWiki MediaWiki parser extension.
 *
 * This is a MediaWiki extension that parse markdown syntax to wiktext using extension of cebe's marsedown parser.
 *
 * @author    Pavel Dobes <konference@lnx.cz>
 * @version   0.1.0
 * @copyright Copyright (C) 2022 Pavel Dobes
 * @license   MIT
 * @link      https://github.com/PavelD/mw-markdown-wiki
 */

class MarkdownWikiExtension {
    /**
     * Adds custom styopes to highlight generated text
     *
     * @param OutputPage &$out
     * @return bool
     */
    public static function onBeforePageDisplay( OutputPage &$out ) {
        global $wgMarkdownHighlight, $wgMarkdownHighlightJs, $wgMarkdownHighlightCss;

        if ( $wgMarkdownHighlight ) {
            $out->addScriptFile( $wgMarkdownHighlightJs );
            $out->addStyle( $wgMarkdownHighlightCss );
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
        global $wgMarkdownDefaultOn;

        if ( static::shouldParseText( $text ) ) {
            if ( !$wgMarkdownDefaultOn ) {
                $text = substr( $text, strlen( static::getSearchString() ) );
            }

            $text = static::parseMarkdown( $parser, $text );

            return false;
        }

        if ( $wgMarkdownDefaultOn ) {
            $text = substr( $text, strlen( static::getSearchString() ) );
        }

        return true;
    }

    /**
     * Converts the given text into markdown.
     *
     * @param Parser $parser MediaWiki's parser
     * @param string $text The text to parse
     * @return string The parsed text
     */
    protected static function parseMarkdown( $parser, $text ) {
        $html = $text;

        $html = static::getParser()->parse( $html );

        $html = $parser->internalParse( $html );
        return $html;
    }

    /**
     * @param string $text The text to check over for our tags if necessary
     * @return bool Whether to parse the given text
     */
    protected static function shouldParseText( $text ) {
        global $wgMarkdownDefaultOn;

        $search = static::getSearchString();

        return (
                ( $wgMarkdownDefaultOn && strpos( $text, $search ) !== 0 )
                || ( !$wgMarkdownDefaultOn && strpos( $text, $search ) === 0 )
        );
    }

    /**
     * @return string The search string
     */
    protected static function getSearchString() {
        global $wgMarkdownDefaultOn, $wgMarkdownToggleFormat;

        return sprintf( $wgMarkdownToggleFormat, $wgMarkdownDefaultOn ? 'WIKI' : 'MARKDOWN' );
    }

    /**
     * @return Parsedown
     */
    protected static function getParser() {
        static $parser;

        if ( !$parser ) {
            $parser = new paveld\markdownwiki\MarkdownWiki();
        }

        return $parser;
    }
}
