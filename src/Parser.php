<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


Interface Parser {
	public function canParse(File $what): bool;
	public function parse(File &$from);
	public function renderToString(File $file): string;
	public function renderToFile(File $file);
}
