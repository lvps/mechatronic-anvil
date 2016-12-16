<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class Directory implements HasParent {
	use Stat, HasMetadata;

	/** @var string */
	private $name;
	/** @var Directory|NULL */
	private $parent;
	/** @var array */
	private $content = [];

	/**
	 * Directory constructor.
	 *
	 * @param string $name directory name
	 * @param Directory|NULL $parent parent directory or NULL if "root" directory (i.e. INPUT or OUTPUT constant)
	 */
	function __construct(string $name, Directory &$parent = NULL) {
		$this->setName($name);
		$this->parent = $parent;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name) {
		if($name === NULL || $name === '') {
			throw new \InvalidArgumentException('Directories cannot have an empty name!');
		}
		$this->name = $name;
	}

	public function getPath(): string {
		// TODO: apply memoization to get rid of a few function calls?
		if($this->parent instanceof Directory) {
			return $this->parent->getPath() . DIRECTORY_SEPARATOR . $this->name;
		} else {
			return $this->name;
		}
	}

	public function buildTree() {
		$currentPath = $this->getPath();

		foreach(scandir($currentPath) as $entry) {
			if($entry === '.' || $entry === '..') {
				continue;
			}

			$filename = $currentPath . DIRECTORY_SEPARATOR . $entry;

			if(is_file($filename)) {
				$thing = new File($entry, $this);
				$thing->stat();
				$this->content[] = $thing;
			} else if(is_dir($filename)) {
				$thing = new Directory($entry, $this);
				$thing->stat();
				$thing->buildTree();
				$this->content[] = $thing;
			}
		}
	}

	public function setParent(Directory $parent) {
		$this->parent = $parent;
	}

	public function getParent(): Directory {
		return $this->parent;
	}

	private function stat() {
		$this->doStat($this->getPath());
	}

	public function __clone() {
		foreach($this->content as $i => $item) {
			// clone each file and directory
			$copy = clone $item;
			// update its "parent" pointer
			if($copy instanceof Directory) {
				/** @var Directory $copy */
				$copy->setParent($this);
			} else {
				/** @var File $copy */
				$copy->setParent($this);
			}
			$this->content[$i] = $copy;
		}
	}

	/**
	 * Builds the file\directory output tree from the input one.
	 *
	 * @param Directory $newRoot new root directory (i.e. OUTPUT)
	 * @return Directory the output tree
	 */
	public function buildOutputTree(Directory $newRoot): Directory {
		$linkFilesToThemselves = function(File $file) {
			if($file->getRenderFrom() !== NULL) {
				throw new \LogicException('buildOutputTree() must be called only on the input tree (i.e. found a non-NULL renderFrom variable in a File object)!');
			}
			$file->setRenderFrom($file);
		};
		$linkFilesToNULL = function(File $file) {
			$file->setRenderFrom(NULL);
		};
		$this->recursiveWalkCallback($linkFilesToThemselves, NULL, NULL);
		$copy = clone $this;
		$this->recursiveWalkCallback($linkFilesToNULL, NULL, NULL);
		return $copy->reRoot($newRoot);
	}

	private function isCallableOrNull($what): bool {
		if(is_callable($what) || is_null($what)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Walk through the directory tree recusively.
	 * Calls $onFile for every file, and $onDirEnter & $onDirLeave for every directory encountered.
	 * Callbacks receive a File or Directory object as the only parameter.
	 * NULL instead of a Callable function means "no action".
	 *
	 * @param Callable|NULL $onFile
	 * @param Callable|NULL $onDirEnter
	 * @param Callable|NULL $onDirLeave
	 * @see Directory::walkCallback
	 */
	public function recursiveWalkCallback($onFile, $onDirEnter = NULL, $onDirLeave = NULL) {
		if(!$this->isCallableOrNull($onFile)) {
			throw new \InvalidArgumentException('$onFile must be callable or NULL!');
		}
		if(!$this->isCallableOrNull($onDirEnter)) {
			throw new \InvalidArgumentException('$onDirEnter must be callable or NULL!');
		}
		if(!$this->isCallableOrNull($onDirLeave)) {
			throw new \InvalidArgumentException('$onDirLeave must be callable or NULL!');
		}

		$onDirEnter === NULL ?: call_user_func($onDirEnter, $this);
		$this->recursiveWalkCallbackInternal($onFile, $onDirEnter, $onDirLeave);
		$onDirEnter === NULL ?: call_user_func($onDirLeave, $this);
	}

	/**
	 * Walk across the current directory.
	 * Calls $onFile for every file and $onDir for every directory encountered.
	 * Callbacks receive a File or Directory object as the only parameter.
	 * NULL instead of a Callable function means "no action".
	 *
	 * @param $onFile
	 * @param null $onDir
	 * @see Directory::recursiveWalkCallback
	 */
	public function walkCallback($onFile, $onDir = NULL) {
		if(!$this->isCallableOrNull($onFile)) {
			throw new \InvalidArgumentException('$onFile must be callable or NULL!');
		}
		if(!$this->isCallableOrNull($onDir)) {
			throw new \InvalidArgumentException('$onDir must be callable or NULL!');
		}

		foreach($this->content as $i => $item) {
			if($item instanceof File) {
				if($onFile !== NULL) {
					call_user_func($onFile, $this->content[$i]);
				}
			} else {
				if($onDir !== NULL) {
					call_user_func($onDir, $this->content[$i]);
				}
			}
		}
	}

	private function recursiveWalkCallbackInternal($onFile, $onDirEnter, $onDirLeave) {
		foreach($this->content as $i => $item) {
			if($item instanceof File) {
				if($onFile !== NULL) {
					call_user_func($onFile, $this->content[$i]);
				}
			} else {
				if($onDirEnter !== NULL) {
					call_user_func($onDirEnter, $this->content[$i]);
				}
				$item->recursiveWalkCallbackInternal($onFile, $onDirEnter, $onDirLeave);
				if($onDirLeave !== NULL) {
					call_user_func($onDirLeave, $this->content[$i]);
				}
			}
		}
	}

	/**
	 * Change root directory. Or actually, copy old content into new root.
	 *
	 * @param Directory $root new root
	 * @return Directory new root with old content
	 */
	public function reRoot(Directory $root): Directory {
		if($this->parent === NULL) {
			// Get old content, place in new root
			$root->content = $this->content;
			// for each item in new root ($this)
			foreach($this->content as $i => $item) {
				/** @var File|Directory $item */
				// update its parent
				$this->content[$i]->setParent($this);
			}
		} else {
			throw new \LogicException('reRoot called on non-root directory!');
		}
		return $root;
	}
}
