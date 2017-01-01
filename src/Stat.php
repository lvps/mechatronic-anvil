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

		$this->mode = $this->readpermsFromDisk($filename);
		$this->mtime = $this->readMtimeFromDisk($filename);
	}

	private function readMtimeFromDisk(string $filename): int {
		$result = false;
		if(file_exists($filename)) {
			$result = filemtime($filename);
		}
		if(is_int($result)) {
			return $result;
		} else {
			throw new \RuntimeException('Cannot read mtime from ' . $filename);
		}
	}

	private function readPermsFromDisk(string $filename): int {
		$result = false;
		if(file_exists($filename)) {
			$result = fileperms($filename);
		}
		if(is_int($result)) {
			return $result;
		} else {
			throw new \RuntimeException('Cannot read permissions from ' . $filename);
		}
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