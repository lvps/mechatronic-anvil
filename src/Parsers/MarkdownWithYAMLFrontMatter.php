<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;


use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;
use Michelf\MarkdownExtra;

/**
 * Markdown file with YAML front matter.
 * Use `---` to separate content from metadata; `---` at the beginning of file is optional.
 *
 * @package lvps\MechatronicAnvil\Parsers
 */
class MarkdownWithYAMLFrontMatter extends AbstractYAMLFrontMatter implements Parser {
	use YamlParserWrapper, PHPTemplate;

	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'md') {
			$content = $what->getRenderFrom()->getContents();
			if($this->separatorType($this->removeStartingSeparator($content)) !== NULL) {
				return true;
			}
		}
		return false;
	}

	public function renderInputString(string $what): string {
		return MarkdownExtra::defaultTransform($what);
	}
}