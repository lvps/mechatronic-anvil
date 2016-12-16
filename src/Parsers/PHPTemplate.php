<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;


use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Metadata;

trait PHPTemplate {
	private function render(string $templatePath, File $file, string $content): string {
			$metadata = (array) $file->getMetadata(); // this works, surprisingly.
			$file_name = $file->getBasename();
			$file_path = $file->getFilename();
			unset($md); // avoid chaos
			ob_start();
			include $templatePath;
			return ob_get_clean();
	}

	private function getTemplate(Metadata $md): string {
		if(!defined('TEMPLATES')) {
			throw new \LogicException('TEMPLATES constant not defined!');
		}

		if(isset($md['template'])) {
			return TEMPLATES . DIRECTORY_SEPARATOR . $md['template'];
		} else {
			return TEMPLATES . DIRECTORY_SEPARATOR . 'base.php';
		}
	}
}