<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;

use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Parser;

abstract class AbstractYAMLFrontMatter extends PHPTemplate implements Parser {
	use YamlParserWrapper;

	public function parse(File $file) {
		$pieces = $this->split($file->getRenderFrom());
		$file->setBasename($file->getBasenameWithoutExtension() . '.html');
		$file->addMetadataOnTop($this->yamlParse($pieces[0]));
	}

	protected static function removeStartingSeparator(string $content): string {
		return ltrim($content, "-\r\n");
	}

	protected static function split(File $file): array {
		$content = self::removeStartingSeparator(($file->getContents()));
		$completeSeparator = self::separatorType($content);
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
	protected static function separatorType(string $content) {
		if(strpos($content, "\r\n---\r\n") !== false) {
			return "\r\n---\r\n";
		}
		if(strpos($content, "\n---\n") !== false) {
			return "\n---\n";
		}
		return NULL;
	}

	protected function getContent(File $file): string {
		$pieces = $this->split($file->getRenderFrom());
		return $this->renderContentToString($pieces[1]);
	}

	abstract protected function renderContentToString(string $what): string;

}