<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

/**
 * Renders HTML files using the PHPTemplate class: files contain no metadata and only some plain HTML that will be used
 * as $content.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class HTMLWithDefaultTemplate extends PHPTemplate implements Parser {

	public function canParse(File $what): bool {
		if(strtolower($what->getRenderFrom()->getExtension()) === 'html') {
			return true;
		}
		return false;
	}

	public function parse(File $file) {
		return;
	}

	protected function getContent(File $file): string {
		return $file->getRenderFrom()->getContents();
	}
}