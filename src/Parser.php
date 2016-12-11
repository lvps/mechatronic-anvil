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

	public function renderToString(File $file);

	/**
	 * Render File to its FINAL destination™.
	 *
	 * @param File $file
	 * @return void
	 */
	public function renderToFile(File $file);
}
