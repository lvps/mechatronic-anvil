<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

class HTMLWithDefaultTemplate implements Parser {
	use PHPTemplate;

	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'html') {
			return true;
		}
		return false;
	}

	public function parse(File $file) {
		return;
	}

	public function renderToString(File $file): string {
		return $this->render($this->getTemplate($file->getMetadata()), $file, $file->getRenderFrom()->getContents());
	}

	public function renderToFile(File $file) {
		file_put_contents($file->getFilename(), $this->renderToString($file));
		$file->applyMtime();
		$file->applyMode();
	}
}