<?php

namespace Justuno\Jumagext\Helper;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{

    protected $storeManager;
    /**
     * @var TypeListInterface
     */
	private $cacheTypeList;

    /**
     * @var array
     */
	private $config = [];

	const OPTION_ACCID      = 'justuno_settings/options_interface/accid';
	const OPTION_JUAJAXURL  = 'justuno_settings/options_interface/juajaxurl';

    /**
     * Data constructor.
     * @param TypeListInterface $cacheTypeList
     * @param Context $context
     */
    public function __construct(
        TypeListInterface $cacheTypeList,
        Context $context,
		/**
		 * 2019-10-245 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * «Class Justuno\Jumagext\Helper\Magento\Store\Model\StoreManagerInterface does not exist»
		 * on `bin/magento setup:di:compile`: https://github.com/justuno-com/m2/issues/1
		 */
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
            parent::__construct($context);
            $this->cacheTypeList = $cacheTypeList;
            $this->_storeManager=$storeManager;
    }

   /**
     * @return string
     */
    public function getACCID()
    {
        return (string) $this->getValue(self::OPTION_ACCID);
	}

	/**
     * @return string
     */
    public function getJUAJAX()
    {
        return (string) $this->getValue(self::OPTION_JUAJAXURL);
	}

    /**
     * @param string $path
     * @return mixed
     */
    private function getValue($path) {
        if(isset($this->config[$path])) {
            return $this->config[$path];
        }
        return $this->scopeConfig->getValue($path);
    }


}
