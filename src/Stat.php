<?php


namespace lvps\MechatronicAnvil;


trait Stat {
	/** @var int */
	private $mtime;
	/** @var int */
	private $mode;

	private function doStat(string $filename) {
		if(!file_exists($filename)) {
			throw new \Exception('Trying to stat non-existing file: '.$filename);
		}

		$this->mode = fileperms($filename);
		$this->mtime = filemtime($filename);
	}
}