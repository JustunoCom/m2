<?php
namespace Justuno\Jumagext\Setup;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\ResourceModel\Role\Collection;
use Magento\Authorization\Model\Role;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\User\Model\User;
class InstallData implements InstallDataInterface {
	/**
	 * @param \Magento\Authorization\Model\RoleFactory $roleFactory
	 * @param \Magento\Authorization\Model\RulesFactory $rulesFactory
	 */
	function __construct(
		\Magento\User\Model\UserFactory $userFactory,
		\Magento\Authorization\Model\RoleFactory $roleFactory, /* Instance of Role*/
		\Magento\Authorization\Model\RulesFactory $rulesFactory, /* Instance of Rule */
		\Magento\Authorization\Model\Role $roleAuthModel ,
		\Magento\User\Model\User $userModel ,
		\Magento\Authorization\Model\ResourceModel\Role\Collection $justunorole
	) {
		$this->_userFactory     = $userFactory;
		$this->justunorole      = $justunorole;
		$this->roleAuthModel    = $roleAuthModel;
		$this->roleFactory      = $roleFactory;
		$this->rulesFactory     = $rulesFactory;
		$this->userModel        = $userModel;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$role = $this->roleFactory->create();
		$role->setName('justunoUser')
				->setPid(0)
				->setRoleType(RoleGroup::ROLE_TYPE)
				->setUserType(UserContextInterface::USER_TYPE_ADMIN);
		$role->save();
		$resource=['Magento_Backend::admin',
					'Magento_Catalog::catalog',
					'Magento_Catalog::products',
					'Magento_Catalog::categories',
					'Magento_Customer::customer',
					'Magento_Customer::manage',
					'Magento_Sales::sales',
					'Magento_Sales::sales_order',
					'Magento_Sales::actions_view'
				  ];
		$this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
		$checkRole = $this->justunorole->addFieldToFilter('role_name', ['eq' => 'justunoUser'] );
		$roleID = $checkRole->getFirstItem()->getRoleId();
		$UserInfo = [
			'username'  => 'justunouser',
			'firstname' => 'justuno',
			'lastname'  => 'user',
			'email'     => 'info123@justuno.com',
			'password'  => 'hello@123',
			'interface_locale' => 'en_US',
			'is_active' => 1
		];
		$userModel = $this->_userFactory->create();
		$userModel->setData($UserInfo);
		$userModel->setRoleId($roleID);
		$userModel->save();
	}

	private $_userFactory;
	private $justunorole;
	private $roleAuthModel;
	private $roleFactory;
	private $rulesFactory;
	private $userModel;
}