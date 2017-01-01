<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

/**
 * Matches everything, copies file from source to destination.
 * You shouldn't need to add this parser explicitly, it's already the default in ParserCollection.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class PlainCopy implements Parser {

	public function canParse(File $what): bool {
		return true;
	}

	public function renderToFile(File $file) {
		$output = $file->getFilename();
		try {
			$comparison = $file->statMtimeCompare();
		} catch(\RuntimeException $ignored) {
			$comparison = 1;
		}
		if($comparison > 0) {
			copy($file->getRenderFrom()->getFilename(), $output);
			$file->applyMtime();
		}
		$file->applyMode();
	}

	public function renderToString(File $file) {
		return $file->getRenderFrom()->getContents();
	}

	public function parse(File $file) {
		return;
	}
}
