# Mechatronic Anvil
Another static site generator. WIP. No stable release yet.

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

Looking at the code in `ma.php` may help better understanding this section.
 
There are 6 stages, after each one a user-defined callback function will be called (if provided in `launch.php`):

**Input and output tree**: a tree of `Directory` and `File` objects, representing the input directory, is built. `INPUT` is a constant defined in `launch.php`, if undefined it defaults to `input`. If the input directory doesn't exist or is inaccessibile, the program terminates (same for the `output` directory and `OUTPUT` constant).

`Directory` objects can contain other `Directory` o `File` objects. The tree, actually, is the input `Directory` with all its content placed inside.

The tree is then cloned into `$output` and modified to replace the root directory, `INPUT`, with `OUTPUT`. To each `File` is also added a reference (pointer) to its corresponding file in the input tree, as the `$renderFrom` property.

User-defined callback function `onRead($output)` is then called.

**Parsing input files**: the program tries to parse each `File` in the *output* tree by calling `$parser->canParse($file)`. This is done for all the parsers provided in the `$parsers` array defined in `launch.php`. If the array isn't defined, it defaults to `$parsers = ['UnderscoreDotYaml', 'YamlForMarkdown', 'MarkdownWithYAMLFrontMatter', 'Markdown'];`.

Note that `PlainCopy` will always be added at the end, even if `$parsers` is an empty array; all it does when rendering is to copy files from input to output directory. Its `canParse()` also always return true.

Output tree is used and modified, but parsers should decide whether to parse a file or not based on `$file->getRenderFrom()` (which returns the `$renderFrom` property) and get file contents from there.

Parsers are tried one after another until one of the `canParse()` methods returns true: when that happens, a reference to the `Parser` object is placed in `File` `$parser` property (since the parser will be later used to render the file) and `$parser->parse($file)` is called.

The `parse()` method can, for example:
1. Extract any metadata from the file and place it where needed (in the `$metadata` property of the `File` itself, or of the parent `Directory`, etc...)
2. Optionally rename the output file, e.g. changing extension from .md to .html.
3. Optionally call `$file->doNotRender()` to set that the file only contained metadata and shouldn't appear in the output directory

User-defined callback function `onParsed($output)` is then called.

**Merging metadata**: any metadata applied to a `Directory` is considered "global" and it applies to each `File` and `Directory` contained there. Metadata contained by `File`s themselves has precedence over global metadata, however.

To account for this, the program will take metadata from each `Directory` and apply it to each `File` and `Directory` contained inside recursively (i.e. even to sub-sub-sub-directories and so on).

User-defined callback function `onMerged($output)` is then called.

**Deleting non-renderable files**: `File`s marked with `$file->doNotRender()` are finally deleted from the output tree.

User-defined callback function `onPruned($output)` is then called. This is a good time to generate a sitemap or similar content using the callback function.

**Cleaning output directory**: if the input directory contents have changed since last run, output directory may still contain files that don't exist anymore and won't be rendered again: the program checks for any file in output directory that isn't in the output tree and deletes it from disk.

User-defined callback function `onCleaned($output)` is then called.

**Rendering**: for each file in the output tree, its parser is called. More specifically, `$file->render()` calls `$parser->renderToFile()`.

After the file has been rendered and placed in the output directory, its permissions and modification time are updated according to what they were in the input file or what the parser has set. Directories are created and correct permission and modification times are applied independently of files.
 
User-defined callback function `onRendered($output)` is then called.
 
Finally, some stats are displayed.

## How to...

### Write a template

Templates are PHP files that print the rendered page. That's it.
 
Output buffering is used to recover the page and save it to the output file.
 
All the "parsers" that use this kind of templates extend the PHPTemplate base class.

The rendering method in `PHPTemplates`, first of all, looks for the `template` element in file metadata. If found, it calls that file from the templates directory (which is defined via the `TEMPLATES` constant). If it ins't found, it calls the `base.php` in templates directory.  
E.g. If `TEMPLATES = "templates"` and `$metadata\['template'\] = foo.php`, then `templates/foo.php` is called. If there's no `$metadata\['template'\]`, then `templates/base.php` is called.

And "calls" means "literally `include` that file".

The `include`d template will find some variables available in its scope:
* `$content`, which is the page content coming from a parser: usually HTML, the content of the `<body></body>` tags
* `$metadata`, an array of metadata
* `$file`, the full File object that's being rendered
* `$file_name`, file basename (e.g. `index.html`)
* `$file_path`, file path (e.g. `output/foo/index.html`)

A basic template could be:
```
<!DOCTYPE html>
<html>
    <head>
        <title>
        	<?php echo isset($metadata['title']) ? $metadata['title'] : 'No title';	?>
        </title>
    </head>
    <body>
        <?= $content ?>
    </body>
</html>
```
Note that `$metadata['title']` isn't a default variable, it should be defined somewhere in the input files (e.g. by using parsers that accept YAML front matter).

### What does each parser do

(WIP)

### Add metadata to input files

(WIP)

### Write a Parser

(WIP)

### Rebase URLs

(WIP)

## License

MIT (see LICENSE).