<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Metadata;
use lvps\MechatronicAnvil\Parser;
use Michelf\MarkdownExtra;


class YamlForMarkdown implements Parser{
	use YamlParserWrapper;

	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'yaml') {
			return true;
		}
		return false;
	}

	public function parse(File &$file) {
		$metadata = new Metadata($this->yamlParse($file->getRenderFrom()->getContents()));

		$found = NULL;
		$search = $file->getBasenameWithoutExtension();

		$file->getParent()->walkCallback(function(File $file) use ($search, $found) {
			if($file->getBasenameWithoutExtension() === $search && $file->getExtension() === 'md') {
				// TODO: test this
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