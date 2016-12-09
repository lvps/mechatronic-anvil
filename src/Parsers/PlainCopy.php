<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil\Parsers;
use lvps\MechatronicAnvil\File;
use lvps\MechatronicAnvil\Metadata;
use lvps\MechatronicAnvil\Parser;

class PlainCopy implements Parser {

	public function canParse(File $what): bool {
		return true;
	}

	public function getMetadata(File $from): Metadata {
		return NULL;
	}

	public function getOutputFile(File $from): File {
		return $from->cloneToInputOutput();
	}

	public function renderToString(File $from, File $to, Metadata $metadata): string {
		return $from->getContents();
	}

	public function renderToFile(File $from, File $to, Metadata $metadata): void {
		if($to->older()) {
			copy($from->getFileName(), $to->getFileName());
			touch($to->getFileName(), $to->getMtime());
			chmod($to->getFileName(), $to->getMode());
		}
	}
}
