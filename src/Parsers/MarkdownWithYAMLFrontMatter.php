<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;


use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Metadata;
use lvps\MechatronicAnvil\Parser;
use Michelf\MarkdownExtra;

class MarkdownWithYAMLFrontMatter implements Parser {
	use YamlParserWrapper;

	public function canParse(File $what): bool {
		if(strtolower($what->getExtension()) === 'md') {
			$content = $what->getRenderFrom()->getContents();
			if($this->separatorType($this->removeStartingSeparator($content)) !== NULL) {
				return true;
			}
		}
		return false;
	}

	public function parse(File &$file) {
		$pieces = $this->split($file->getRenderFrom());
		$file->addMetadataOnTop(new Metadata($this->yamlParse($pieces[0])));
	}

	private function removeStartingSeparator(string $content): string {
		return ltrim($content, "-\r\n");
	}

	private function split(File $file): array {
		$content = $this->removeStartingSeparator(($file->getRenderFrom()->getContents()));
		$completeSeparator = $this->separatorType($content);
		if($completeSeparator === NULL) {
			throw new \LogicException('Can\'t parse '.$file.': missing --- separator!');
		}
		return explode($completeSeparator, $content, 2);
	}

	/**
	 * Find the --- separator.
	 *
	 * @param string $content
	 * @return string complete separator (with \n or \r\n), or NULL if not found
	 */
	private function separatorType(string $content): string {
		if(strpos($content, "\r\n---\r\n") !== false) {
			return "\r\n---\r\n";
		}
		if(strpos($content, "\n---\n") !== false) {
			return "\n---\n";
		}
		return NULL;
	}

	public function render(File $file): string {
		$pieces = $this->split($file->getRenderFrom());
		return MarkdownExtra::defaultTransform($pieces[1]);
	}
}