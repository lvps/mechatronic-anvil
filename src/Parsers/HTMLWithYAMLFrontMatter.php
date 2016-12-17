<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;


use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;
use Michelf\MarkdownExtra;

class HTMLWithYAMLFrontMatter extends AbstractYAMLFrontMatter implements Parser {
	use YamlParserWrapper, PHPTemplate;

	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'html') {
			$content = $what->getRenderFrom()->getContents();
			if($this->separatorType($this->removeStartingSeparator($content)) !== NULL) {
				return true;
			}
		}
		return false;
	}

	public function renderInputString(string $what): string {
		return $what;
	}
}