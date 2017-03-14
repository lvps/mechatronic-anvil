<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


abstract class FilesystemObject {
	/** @var int */
	private $mtime = NULL;
	/** @var int */
	private $mode = NULL;
	/** @var array|NULL */
	private $metadata = NULL;

	/**
	 * @param array|NULL $other
	 */
	public function addMetadataOnTop($other) {
		if($other === NULL || !is_array($other) || empty($other)) {
			return;
		}

		$this->metadata = array_merge($this->getMetadata(), $other);
	}

	/**
	 * @param array|NULL $other
	 */
	public function addMetadataOnBottom($other) {
		if($other === NULL || !is_array($other) || empty($other)) {
			return;
		}

		$this->metadata = array_merge($other, $this->getMetadata());
	}

	public function setMetadata(array $metadata) {
		$this->metadata = $metadata;
	}

	public function getMetadata(): array {
		return $this->metadata === NULL ? [] : $this->metadata ;
	}

	public function stat() {
		$filename = $this->getFilename();
		if(!file_exists($filename)) {
			throw new \Exception('Trying to stat non-existing file: '.$filename);
		}

		$this->mode = $this->readpermsFromDisk($filename);
		$this->mtime = $this->readMtimeFromDisk($filename);
	}

	/**
	 * Compare mtime between this File object and the actual file on disk.
	 *
	 * @throws \RuntimeException if mtime is missing or cannot be read from disk (e.g. file doesn't exist)
	 * @return int $this->mtime - $currentMtime, &gt;0 if this is newer, &lt;0 if this is older, =0 if identical
	 */
	public function statMtimeCompare(): int {
		$currentMtime = $this->readMtimeFromDisk($this->getFilename());
		if($this->mtime === NULL) {
			throw new \RuntimeException('No mtime set for ' . $this->getFilename());
		}
		return $this->mtime - $currentMtime;
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

	/**
	 * Return relative path to file/directory (including input/ or output/)
	 */
	public abstract function getFilename(): string;

	/**
	 * Return even MORE relative path to file/directory (WITHOUT input/ or output/)
	 */
	public abstract function getRelativeFilename(): string;

	public function applyMtime() {
		if(($mtime = $this->getMtime()) === NULL) {
			return;
		}
		touch($this->getFilename(), $mtime);
	}

	public function applyMode() {
		if(($mode = $this->getMode()) === NULL) {
			return;
		}
		chmod($this->getFilename(), $mode);
	}
}