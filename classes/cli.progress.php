<?php
	class Progress
	{
		private const PROGRESS_CURRENT    = '🍺';
		private const PROGTESS_FINISHED   = '#';
		private const PROGTESS_UNFINISHED = ' ';
		private string $format;
		private string $work = '';
		private int    $start_ts;
		private int    $before = 0;
		private int    $current_ts;
		private int    $current_count = 0;
		public  int    $exec_count;
		public  string $summary;

		function __construct(int $count = 0, string $summary = 'Summary',  string $format = null)
		{
			$this->summary    = $summary;
			$this->start_ts   = microtime(true);
			$this->exec_count = $count;
			$this->format     = sprintf($format ?? ' %% 3s%%%% [%%s%%s%%s] ETA %%s %% %ss/%s', strlen($count), $count);
		}

		public function bar(string $work = null) : void
		{
			if ($work) {
				$this->work .= $work . PHP_EOL;
			}
			++$this->current_count;
			$this->current_ts = microtime(true);
			$elapsed          = ($this->current_ts - $this->start_ts) / $this->current_count * ($this->exec_count - $this->current_count);
			$eta              = sprintf('%02.2s:%02s:%02s.%0-3.3s', $elapsed / 60 / 60 % 60, $elapsed / 60 % 60, $elapsed % 60, round(($elapsed - floor($elapsed)) * 1000));
			$percent          = $this->current_count / $this->exec_count * 100;
			$progress         = round($percent / 2);
			$bar              = sprintf($this->format, round($percent), str_repeat(self::PROGTESS_FINISHED, $progress), self::PROGRESS_CURRENT, str_repeat(self::PROGTESS_UNFINISHED, 50 - $progress), $eta, $this->current_count);
			$width            = mb_strwidth($bar);
			if ($this->before > $width) {
				$diff = $this->before - $width;
				$sol = sprintf('%s%s', str_repeat(' ', $diff), str_repeat("\x08", $diff));
			}
			$this->before     = $width;
			echo sprintf('%s%s%s', $sol ?? '', $bar, $this->current_count === $this->exec_count ? PHP_EOL : "\r");
			if ($this->current_count === $this->exec_count) {
				if ($this->work) {
					echo "\033[1;34;1m==> \033[0m";
					echo "\033[1;32;1m{$this->summary}\033[0m" . PHP_EOL;
					echo $this->work;
				}
			}
		}
	}
