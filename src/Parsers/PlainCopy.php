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

	public function renderToString(File $file): string {
		return $file->getContents();
	}

	public function renderToFile(File $file) {
		// TODO: implement this
	}

	public function parse(File &$file) {
		$file->setParser($this);
	}
}
