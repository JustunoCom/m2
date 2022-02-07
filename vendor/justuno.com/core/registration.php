<?php
use Magento\Framework\Component\ComponentRegistrar as R;
R::register(R::MODULE, 'Justuno_Core', __DIR__);
# 2017-11-13
# Today I have added the subdirectories support inside the `lib` folders,
# because some lib/*.php files became too big, and I want to split them.
$requireFiles = function($libDir) use(&$requireFiles) {
	# 2015-02-06
	# array_slice removes «.» and «..».
	# http://php.net/manual/function.scandir.php#107215
	foreach (array_slice(scandir($libDir), 2) as $c) {  /** @var string $resource */
		is_dir($resource = "{$libDir}/{$c}") ? $requireFiles($resource) : require_once "{$libDir}/{$c}";
	}
};
# 2017-04-25, 2017-12-13
# Unfortunately, I have not found a way to make this code reusable among my modules.
# I tried to move this code to a `/lib` function like df_lib(), but it raises a «chicken and egg» problem,
# because Magento runs the `registration.php` scripts before any `/lib` functions are initalized,
# whereas the `/lib` functions are initalized from the `registration.php` scripts.
$base = dirname(__FILE__); /** @var string $base */
if (is_dir($libDir = "{$base}/lib")) { /** @var string $libDir */
	$requireFiles($libDir);
}