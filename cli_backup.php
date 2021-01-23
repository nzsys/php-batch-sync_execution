<?php
	/**
	* Backup Script
	* Author: nzsys
	* Created_at: 2021.01.23
	*/
	define('BACKUP_DIRECTORY', '/backup_directory');
	define('SCRIPT_DIRECTORY', './shell');

	if (file_exists('./classes/cli.progress.php')) {
		include './classes/cli.progress.php';
	} else {
		echo "\033[1;34;1m==> \033[0m";
		echo "\033[1;31;1mWarning\033[0m" . PHP_EOL;
		echo 'CLI Progress Class is Not Found.' . PHP_EOL;
		exit;
	}

	if (file_exists(BACKUP_DIRECTORY) === false) {
		echo "\033[1;34;1m==> \033[0m";
		echo "\033[1;31;1mWarning\033[0m" . PHP_EOL;
		echo 'Backup Directory is Not Found.' . PHP_EOL;
		exit;
	}

	$scripts = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(SCRIPT_DIRECTORY, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
	$rsyncs  = [];
	foreach ($scripts as $script) {
		if ($script->isFile() and strpos($script->getPathname(), '.sh') !== false) {
			$rsyncs[] = $script->getPathname();
		}
	}

	if (count($rsyncs) === 0) {
		echo "\033[1;34;1m==> \033[0m";
		echo "\033[1;31;1mWarning\033[0m" . PHP_EOL;
		echo 'Backup Shell Script is Not Found.' . PHP_EOL;
		exit;
	}

	$progress = new Progress(count($rsyncs), 'Synced Casks');
	foreach ($rsyncs as $rsync) {
		exec($rsync, $output, $result);
		$cask = $result === 0 ?
			$rsync . " \033[1;32;1m[OK]\033[0m":
			$rsync . " \033[1;31;1m[NG]\033[0m";
		$progress->bar($cask);
	}
