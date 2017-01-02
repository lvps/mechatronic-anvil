<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;


use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Metadata;
use lvps\MechatronicAnvil\Parser;
use lvps\MechatronicAnvil\Rebase;

abstract class PHPTemplate implements Parser {

	abstract protected function getContent(File $file): string;

	public function renderToString(File $file) {
		return $this->renderWithStandardProcedure($file, $this->getContent($file));
	}

	public function renderToFile(File $file) {
		file_put_contents($file->getFilename(), $this->renderToString($file));
		$file->applyMtime();
		$file->applyMode();
	}

	/**
	 * Get template name from metadata (or use base.php), render file, rebase URLs.
	 *
	 * @param File $file to be rendered
	 * @param string $content
	 * @return string rendered file
	 */
	private function renderWithStandardProcedure(File $file, string $content): string {
		return Rebase::rebase($this->renderWithTemplate($this->getTemplate($file->getMetadata()), $file, $content));
	}

	/**
	 * Renders a file using the specified template. Doesn't rebase URLs.
	 *
	 * @param string $templatePath path to template
	 * @param File $file to be rendered
	 * @param string $content
	 * @return string rendered file
	 */
	private function renderWithTemplate(string $templatePath, File $file, string $content): string {
		$metadata = $file->getMetadata();
		$file_name = $file->getBasename();
		$file_path = $file->getRelativeFilename();
		ob_start();
		include $templatePath;
		return ob_get_clean();
	}

	/**
	 * Searches for a template in metadata. If none is found, base.php will be used.
	 * Returns path to template, based on the TEMPLATES constant.
	 *
	 * @param Metadata $md hopefully containing a "template" item
	 * @return string template path
	 */
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