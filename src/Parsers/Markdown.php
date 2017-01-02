<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;
use Michelf\MarkdownExtra;

/**
 * Markdown file with no metadata.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class Markdown extends PHPTemplate implements Parser {

	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'md') {
			return true;
		}
		return false;
	}

	public function parse(File $file) {
		$file->setBasename($file->getBasenameWithoutExtension() . '.html');
		return;
	}

	public function getContent(File $file): string {
		return MarkdownExtra::defaultTransform($file->getRenderFrom()->getContents());
	}
}