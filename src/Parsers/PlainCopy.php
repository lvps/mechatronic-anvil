<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

class PlainCopy implements Parser {

	public function canParse(File $what): bool {
		return true;
	}

	public function render(File $file): string {
		$output = $file->getFilename();
		copy($file->getRenderFrom()->getFilename(), $output);
		chmod($output, $file->getMode());
		touch($output, $file->getMtime());
		return NULL;
	}

	public function parse(File &$file) {
		$file->setParser($this);
	}
}
