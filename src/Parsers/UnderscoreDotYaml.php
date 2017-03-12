<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */
namespace lvps\MechatronicAnvil\Parsers;

use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Metadata;
use lvps\MechatronicAnvil\Parser;

/**
 * Parses a YAML file called _.yaml. Its content will be applied to every file in the current directory and every subdirectory in the "merging" phase.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class UnderscoreDotYaml implements Parser {
    use YamlParserWrapper;

	public function canParse(File $what): bool {
		if(strtolower($what->getRenderFrom()->getBasename()) === '_.yaml') {
			return true;
		}
		return false;
	}

	public function parse(File $file) {
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