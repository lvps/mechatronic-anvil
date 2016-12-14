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

	/**
	 * @return int|NULL
	 */
	public function getMtime() {
		return $this->mtime;
	}

	/**
	 * @return int|NULL
	 */
	public function getMode() {
		return $this->mode;
	}

	private function getTargetFilename() {
		if($this instanceof File) {
			return $this->getFilename();
		} else { // Directory
			return $this->getPath();
		}
	}

	public function applyMtime() {
		if(($mtime = $this->getMtime()) === NULL) {
			return;
		}
		touch($this->getTargetFilename(), $mtime);
	}

	public function applyMode() {
		if(($mode = $this->getMode()) === NULL) {
			return;
		}
		chmod($this->getTargetFilename(), $mode);
	}
}