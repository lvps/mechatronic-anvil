<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */
namespace lvps\MechatronicAnvil\Parsers;

use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Metadata;
use lvps\MechatronicAnvil\Parser;

class UnderscoreDotYaml implements Parser {
    use YamlParserWrapper;

	public function canParse(File $what): bool {
		if(strtolower($what->getBasename()) === '_.yaml') {
			return true;
		}
		return false;
	}

	public function parse(File &$file) {
		// move metadata to parent directory, don't render file
		$file->getParent()->setMetadata(new Metadata($this->yamlParse($file->getRenderFrom()->getContents()), true));
		$file->doNotRender();
	}

	public function renderToString(File $file): string {
		return '';
	}

	public function renderToFile(File $file) {
		return;
	}
}