<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


trait Stat {
	/** @var int */
	private $mtime = NULL;
	/** @var int */
	private $mode = NULL;

	private function doStat(string $filename) {
		if(!file_exists($filename)) {
			throw new \Exception('Trying to stat non-existing file: '.$filename);
		}

		$this->mode = fileperms($filename);
		$this->mtime = filemtime($filename);
	}

	public function getMtime(): int {
		return $this->mtime;
	}

	public function getMode(): int {
		return $this->mode;
	}

}