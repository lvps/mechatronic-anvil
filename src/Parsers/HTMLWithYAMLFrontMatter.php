<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;


use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

/**
 * HTML content with YAML front matter (for metadata). Fed into the PHPTemplate class.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class HTMLWithYAMLFrontMatter extends AbstractYAMLFrontMatter implements Parser {
	use YamlParserWrapper;

	public function canParse(File $what): bool {
		if(strtolower($what->getRenderFrom()->getExtension()) === 'html') {
			$content = $what->getRenderFrom()->getContents();
			if($this->separatorType($this->removeStartingSeparator($content)) !== NULL) {
				return true;
			}
		}
		return false;
	}

	public function renderContentToString(string $what): string {
		return $what;
	}
}