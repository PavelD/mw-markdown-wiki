# MarkdownWiki extension for MediaWiki

## About

This extension allows usage of [Markdown](https://daringfireball.net/projects/markdown/) syntax on wiki pages. It's based on extension of the [Cebe](https://github.com/cebe)'s [Markdown](https://github.com/cebe/markdown) library for parsing Mrkdown into WikiText syntax.

### Why new extension

I have many markdown files that used to be source for our personal web in the past. But the provider is gone and I had many files on my disk with some not
well known dialect of Markdown inside.

There is [Blake](https://github.com/bharley)'s [Markdown](https://github.com/bharley/mw-markdown). It's not working ion the version of MediaWiki I have
and fixing it turns into this project. To be more flexible in parsing I swhitched backend library to [Cebe](https://github.com/cebe/markdown)'s
[Markdown](https://github.com/cebe/markdown) parser. As well I inject some wiki syntax into the files and change the logic of the parsing.

Now it's possilbe to combine the Markkdown language with wiki syntax.

To simplify the extension I extracted [MarkdownWiki](https://github.com/PavelD/markdown-wiki) parser to separated project.

## Instalation

- In `$mw` run `COMPOSER=composer.local.json composer require --no-update paveld/mw-markdown-wiki`, where `$mw` is a path to your MediaWiki installation
- In `$mw` run `composer update paveld/mw-markdown-wiki --no-dev -o`
- Add the following to `$mw/LocalSettings.php`:

```php
wfLoadExtension( 'Markdown' );
```

Set MarkdwonWiki as default paser add to the file as well following code:

```php
$wgMarkdownDefaultOn = true;
```

## Usage

To alow markdown syntax in the article put `{{MARKDOWN}}` tp the begging of the page.

To use Markdown on every page use `$wgMarkdownDefaultOn` in `$mw/LocalSettings.php`

### Custom markdown elements

See [README.md](https://github.com/PavelD/markdown-wiki/blob/main/README.md) on the MarkdownWiki project page.

