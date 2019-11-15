<?php
namespace Justuno\M2\Setup;
use Magento\Authorization\Model\Acl\Role\Group as G;
use Magento\Authorization\Model\Role;
use Magento\Authorization\Model\Rules;
use Magento\Authorization\Model\UserContextInterface as C;
use Magento\User\Model\User as U;
// 2019-11-15
class UpgradeData extends \Df\Framework\Upgrade\Data {
	/**
	 * 2019-11-15
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	protected function _process() {
		if ($this->v('1.1.0')) {
			df_uninstall('Justuno_Jumagext');
			$u = df_new_om(U::class); /** @var U $u */
			$u->loadByUsername('justunouser');
			if (!$u->getId()) {
				$role = df_new_om(Role::class); /** @var Role $r  */
				$role->setRoleName('justunoUser');
				$role->setParentId(0);
				$role->setRoleType(G::ROLE_TYPE);
				$role->setUserType(C::USER_TYPE_ADMIN);
				$role->save();
				$rules = df_new_omd(Rules::class, ['resources' => [
					'Magento_Backend::admin',
					'Magento_Catalog::catalog',
					'Magento_Catalog::categories',
					'Magento_Catalog::products',
					'Magento_Customer::customer',
					'Magento_Customer::manage',
					'Magento_Sales::actions_view',
					'Magento_Sales::sales',
					'Magento_Sales::sales_order'
				]]); /** @var Rules $rules */
				$rules->setRoleId($role->getId());
				$rules->saveRel();
				$u->addData([
					'email' => 'info123@justuno.com',
					'firstname' => 'justuno',
					'interface_locale' => 'en_US',
					'is_active' => 1,
					'lastname'  => 'user',
					'password'  => 'hello@123',
					'role_id' => $role->getId(),
					'username'  => 'justunouser'
				]);
				$u->save();
			}
		}
	}
}