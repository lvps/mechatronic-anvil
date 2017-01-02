<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

/**
 * Never matches anything, doesn't parse anything, renders files with PHPTemplate class.
 * Content is always an empty string.
 * Useful for pages generated in onRead/onParsed/onMerged.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class NoContentPHPTemplate extends PHPTemplate implements Parser {

	public function canParse(File $what): bool {
		return false;
	}

	public function parse(File $file) {
		return;
	}

	protected function getContent(File $file): string {
		return '';
	}
}