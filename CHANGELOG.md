CHANGELOG
=========

Version 0.3.0 on 16 Jan. 2022
-----------------------------
This version breaks configuration in `$mw/LocalSettings.php`!

* change configuration variable names:
```php
$wgMarkdownDefaultOn => $wgMarkdownWikiDefaultOn,
$wgMarkdownToggleFormat => $wgMarkdownWikiToggleFormat,
$wgMarkdownHighlight => $wgMarkdownWikiHighlight,
$wgMarkdownHighlightJs => $wgMarkdownWikiHighlightJs,
$wgMarkdownHighlightCss => $wgMarkdownWikiHighlightCss
```
* Fix bug [#7](https://github.com/PavelD/mw-markdown-wiki/issues/7)



Version 0.2.2 on 14 Jan. 2022
-----------------------------
Initial release.

* Composer installation
