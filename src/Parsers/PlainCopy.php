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

	public function renderToFile(File $file) {
		$output = $file->getFilename();
		copy($file->getRenderFrom()->getFilename(), $output);
		$file->applyMtime();
		$file->applyMode();
	}

	public function renderToString(File $file) {
		return $file->getRenderFrom()->getContents();
	}

	public function parse(File &$file) {
		return;
	}
}
