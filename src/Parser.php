<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


Interface Parser {
	public function canParse(File $what): bool;

	/**
	 * Edit metadata, set attributes and so on.
	 * File is passed by reference.
	 *
	 * @param File $file
	 * @return void
	 */
	public function parse(File &$file);

	/**
	 * Render File to a string. This is called only after everything has been parse()d.
	 * If NULL is returned, content has been placed directly in the output file!
	 *
	 * @param File $file
	 * @return string
	 */
	public function render(File $file): string;
}
