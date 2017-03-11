# Mechatronic Anvil
Another static site generator. WIP.

Requires PHP 7 and Composer.

## Features
* Uses PHP as a templating language
* Pages can be rendered from markdown files for content and PHP+HTML templates
* "Parsers" can be (easily?) created to render other file formats
* Allows running user-defined callback functions before/after rendering to generate e.g. a sitemap, blog index, etc...
* Optional URL rebasing in every rendered HTML file

## How it works (short version)
Files are taken from an `input` directory, rendered (possibily using a template) and placed in an `output` directory. Templates are simply PHP files ("PHP as a templating language").

Every files contains data, metadata, or both; metadata can be used by templates and metadata-only files will not be rendered into output directory.

Input, output and templates directories can be configured via constants in `launch.php`, which should be used to run the program. An example `launch.php` is provided as `launch-example.php`.

## Installation

1. Clone/download this git repo
2. Run `composer install`, to install dependencies and generate `autoload.php`
3. Copy `launch-example.php` up one directory and rename it to `launch.php` (or anything else)
4. Customize `launch.php` as needed, create the input and output directories next to it (paths in `launch.php` are relative to that file)
5. Place some files in input directory and run `launch.php`

Note that Mechatronic Anvil should not be uploaded to a server. Instead, the idea is to avoid running PHP in production, by building the website locally and uploading only the `output` directory contents.

## How it works (long version)

(WIP)

## How to...

### Write a template

(WIP)

### Add metadata to input files

(WIP)

### Write a Parser

(WIP)

### Rebase URLs

(WIP)

## License

MIT (see LICENSE).