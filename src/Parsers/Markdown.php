<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;
use Michelf\MarkdownExtra;

class Markdown implements Parser {
	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'md') {
			return true;
		}
		return false;
	}

	public function parse(File &$file) {
		return;
	}

	public function renderToString(File $file): string {
		return MarkdownExtra::defaultTransform($file->getRenderFrom()->getContents());
	}

	public function renderToFile(File $file) {
		file_put_contents($file->getFilename(), $this->renderToString($file));
		$file->applyMtime();
		$file->applyMode();
	}
}