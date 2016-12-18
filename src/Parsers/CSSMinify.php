<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

/**
 * Removes comments and whitespace from CSS files.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class CSSMinify implements Parser {
	private static $remove = ["\r\n", "\r", "\n", "\t", '      ', '     ', '    ', '   ', '  '];

	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'css') {
			return true;
		}
		return false;
	}

	public function parse(File $file) {
		return;
	}

	public function renderToString(File $file): string {
		return str_replace(self::$remove, '', preg_replace('#\s*/\*.+?\*/#sm', '', $file->getRenderFrom()->getContents()));
	}

	public function renderToFile(File $file) {
		file_put_contents($file->getFilename(), $this->renderToString($file));
		$file->applyMtime();
		$file->applyMode();
	}
}