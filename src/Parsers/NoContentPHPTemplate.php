<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

/**
 * Never matches anything, doesn't parse anything, renders files with PHPTemplate trait.
 * Content is always an empty string.
 * Useful for pages generated in onRead/onParsed/onMerged.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class NoContentPHPTemplate implements Parser {
	use PHPTemplate;

	public function canParse(File $what): bool {
		return false;
	}

	public function parse(File $file) {
		return;
	}

	public function renderToString(File $file): string {
		return $this->render($this->getTemplate($file->getMetadata()), $file, '');
	}

	public function renderToFile(File $file) {
		file_put_contents($file->getFilename(), $this->renderToString($file));
		$file->applyMtime();
		$file->applyMode();
	}
}