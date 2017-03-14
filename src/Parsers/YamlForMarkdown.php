<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

/**
 * YAML file containing metadata associated with a markdown file.
 * E.g. example.yaml contains metadata for example.md.
 * If its "sibling" file isn't found, parse() raises a LogicException.
 * If its sibling has YAML front matter, that will take priority and overwrite any elements with the same name;
 * i.e. this metadata is added on bottom of YAML front matter.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class YamlForMarkdown implements Parser{
	use YamlParserWrapper;

	public function canParse(File $what): bool {
		if(strtolower($what->getRenderFrom()->getExtension()) === 'yaml') {
			return true;
		}
		return false;
	}

	public function parse(File $file) {
		$metadata = $this->yamlParse($file->getRenderFrom()->getContents());

		$found = NULL;
		$search = $file->getBasenameWithoutExtension();

		$file->getParent()->walkCallback(function(File $file) use ($search, &$found) {
			if($file->getBasenameWithoutExtension() === $search && $file->getExtension() === 'md') {
				$found = $file;
			}
		});
		/** @var File $found */
		if(!$found) {
			throw new \LogicException($file . ' has no associated markdown file!');
		}
		// might have front matter or metadata from other sources, add on bottom
		$found->addMetadataOnBottom($metadata);
		$file->doNotRender();
	}

	public function renderToString(File $file): string {
		return '';
	}

	public function renderToFile(File $file) {
		return;
	}
}