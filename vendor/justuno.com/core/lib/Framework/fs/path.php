<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;

/**
 * 2020-06-15 "Port the `df_adjust_paths_in_message` function": https://github.com/justuno-com/core/issues/25
 * @used-by ju_ets()
 * @param string $m
 * @return string
 */
function ju_adjust_paths_in_message($m) {
	$bpLen = mb_strlen(BP); /** @var int $bpLen */
	do {
		$begin = mb_strpos($m, BP); /** @var int|false $begin */
		if (false === $begin) {
			break;
		}
		$end = mb_strpos($m, '.php', $begin + $bpLen); /** @var int|false $end */
		if (false === $end) {
			break;
		}
		$end += 4; # 2016-12-23 It is the length of the «.php» suffix.
		$m =
			mb_substr($m, 0, $begin)
			# 2016-12-23 I use `+ 1` to cut off a slash («/» or «\») after BP.
			. ju_path_n(mb_substr($m, $begin + $bpLen + 1, $end - $begin - $bpLen - 1))
			. mb_substr($m, $end)
		;
	} while(true);
	return $m;
}

/**
 * 2017-05-08
 * 2020-08-13 "Port the `df_path_is_internal` function" https://github.com/justuno-com/core/issues/177
 * @used-by \Justuno\Core\Sentry\Trace::info()
 * @param string $p
 * @return bool
 */
function ju_path_is_internal($p) {return '' === $p || ju_starts_with(ju_path_n($p), ju_path_n(BP));}

/**
 * 2020-06-15 "Port the `df_path_n` function": https://github.com/justuno-com/core/issues/26
 * @used-by ju_adjust_paths_in_message()
 * @used-by ju_explode_path()
 * @used-by ju_file_name()
 * @used-by ju_path_is_internal()
 * @used-by ju_path_relative()
 * @param string $p
 * @return string
 */
function ju_path_n($p) {return str_replace('//', '/', str_replace('\\', '/', $p));}

/**
 * 2015-12-06
 * It trims the ending «/».
 * @uses \Magento\Framework\Filesystem\Directory\Read::getAbsolutePath() produces a result with a trailing «/».
 * 2020-08-13 "Port the `df_path_relative` function" https://github.com/justuno-com/core/issues/174
 * @used-by ju_file_write()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
 * @used-by \Justuno\Core\Sentry\Trace::info()
 * @param string $p
 * @param string $b [optional]
 * @return string
 */
function ju_path_relative($p, $b = DL::ROOT) {return ju_trim_text_left(ju_trim_ds_left(
	ju_path_n($p)), ju_trim_ds_left(ju_fs_r($b)->getAbsolutePath()
));}