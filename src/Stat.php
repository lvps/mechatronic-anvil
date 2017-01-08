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
	 * Set mtime. upDate() is probably more useful.
	 *
	 * @see upDate()
	 * @param int $mtime
	 */
	public function setMtime(int $mtime) {
		$this->mtime = $mtime;
	}

	/**
	 * @return int|NULL
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * Update mtime if supplied argument is more recent.
	 *
	 * @see setMtime()
	 * @param int $newMtime
	 */
	public function upDate(int $newMtime) {
		if(!is_int($this->getMtime())) {
			throw new \LogicException('Trying to update mtime on a file without mtime!?');
		}

		if($newMtime > $this->getMtime()) {
			$this->setMtime($newMtime);
		}
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