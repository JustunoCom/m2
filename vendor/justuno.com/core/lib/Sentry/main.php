<?php
use Justuno\Core\Sentry\Client as Sentry;
use Exception as E;
use Justuno\Core\Exception as DFE;
use Magento\Framework\DataObject as _DO;
use Magento\User\Model\User;
/**
 * 2016-12-22
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Justuno_Core» module name.
 * 2020-06-24 "Port the `df_sentry` function": https://github.com/justuno-com/core/issues/118
 * @used-by ju_log()
 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
 * @used-by \Justuno\M2\Response::p()
 * @param string|object|null $m
 * @param _DO|mixed[]|mixed|E $v
 * @param array(string => mixed) $context [optional]
 */
function ju_sentry($m, $v, array $context = []) {
	static $domainsToSkip = []; /** @var string[] $domainsToSkip */
	if ($v instanceof E || !in_array(ju_domain_current(), $domainsToSkip)) {
		$m = ju_sentry_module($m);
		static $d; /** @var array(string => mixed) $d */
		$d = $d ?: ['extra' => [], 'fingerprint' => [
			ju_core_version(), ju_domain_current(), ju_magento_version(), ju_package_version($m), ju_store_code()
		]];
		# 2017-01-09
		if ($v instanceof DFE) {
			$context = ju_extend($context, $v->sentryContext());
		}
		$context = ju_extend($d, $context);
		if ($v instanceof E) {
			# 2016-12-22 https://docs.sentry.io/clients/php/usage/#reporting-exceptions
			ju_sentry_m($m)->captureException($v, $context);
		}
		else {
			$v = ju_dump($v);
			# 2016-12-22 https://docs.sentry.io/clients/php/usage/#reporting-other-errors
			ju_sentry_m($m)->captureMessage($v, [
				'fingerprint' => array_merge(jua($context, 'fingerprint', []), [$v]), 'level' => Sentry::DEBUG
			] + $context);
		}
	}
}

/**
 * 2017-01-10
 * 1) It could be called as `df_sentry_extra(['a' => 'b'])` or df_sentry_extra('a', 'b').
 * 2) $m could be:
 * 2.1) A module name: «A_B»
 * 2.2) A class name: «A\B\C».
 * 2.3) An object: it comes down to the case 2 via @see get_class()
 * 2.4) `null`: it comes down to the case 1 with the «Justuno_Core» module name.
 * 2021-02-22
 * @used-by \Justuno\M2\Store::v()
 * @param string|object|null $m
 * @param mixed ...$v
 */
function ju_sentry_extra($m, ...$v) {ju_sentry_m($m)->extra(!$v ? $v : (is_array($v[0]) ? $v[0] : [$v[0] => $v[1]]));}

/**
 * 2016-12-22
 * $m could be:
 * 1) a module name: «A_B»
 * 2) a class name: «A\B\C».
 * 3) an object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Justuno_Core» module name.
 * 2020-06-26 "Port the `df_sentry_m` function": https://github.com/justuno-com/core/issues/161
 * @used-by ju_sentry()
 * @used-by ju_sentry_extra()
 * @used-by ju_sentry_m()
 * @param string|object|null $m
 * @return Sentry
 */
function ju_sentry_m($m) {return jucf(function($m) {
	$r = null; /** @var Sentry $r */
	/** @var array(string => $r) $a */ /** @var array(string => string)|null $sa */
	if (($a = ju_module_json($m, 'df', false)) && ($sa = jua($a, 'sentry'))) {
		$r = new Sentry(intval($sa['id']), $sa['key1'], $sa['key2']);
		# 2016-12-23 https://docs.sentry.io/clientdev/interfaces/user
		/** @var User|null $u */
		$r->user((ju_is_cli() ? ['username' => ju_cli_user()] : (
			($u = ju_backend_user()) ? [
				'email' => $u->getEmail(), 'id' => $u->getId(), 'username' => $u->getUserName()
			] : (!ju_is_frontend() ? [] : (($c = ju_customer())
				? ['email' => $c->getEmail(), 'id' => $c->getId(), 'username' => $c->getName()]
				: ['id' => ju_customer_session_id()]
			))
			)) + ['ip_address' => ju_visitor_ip()], false);
		$r->tags([
			'Core' => ju_core_version()
			,'Magento' => ju_magento_version()
			,'MySQL' => ju_db_version()
			,'PHP' => phpversion()
		]);
	}
	return $r ?: ($m !== 'Justuno_Core' ? ju_sentry_m('Justuno_Core') :
		ju_error('Sentry settings for Justuno_Core are absent.')
	);
}, [ju_sentry_module($m)]);}

/**
 * 2017-03-15
 * 2020-06-25 "Port the `df_sentry_module` function": https://github.com/justuno-com/core/issues/137
 * @used-by ju_sentry()
 * @used-by ju_sentry_m()
 * @param string|object|null $m [optional]
 * @return string
 */
function ju_sentry_module($m = null) {return !$m ? 'Justuno_Core' : ju_module_name($m);}