<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


Interface Parser {
	public function canParse(File $what): bool;
	public function getMetadata(File $from): Metadata;
	public function getOutputFile(File $from): File;
	public function renderToString(File $from, File $to, Metadata $metadata): string;
	public function renderToFile(File $from, File $to, Metadata $metadata): void;
}
