<?php

namespace ReinaldoMendes\DevUtils\Observer;

use \Magento\Framework\Event\ObserverInterface,
    \Magento\Framework\App\Config\ScopePool,
    \Magento\Store\Model\ScopeInterface,
    \Magento\Framework\ObjectManagerInterface,
    \Magento\Developer\Helper\Data as DevHelper,
    \Magento\Framework\App\Request\Http as Request;

class Hints implements ObserverInterface
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     *
     * @var ScopePool
     */
    protected $scopePool;

    /**
     *
     * @var DevHelper
     */
    protected $devHelper;

    /**
     *
     * @var Request
     */
    protected $request;

    public function __construct(ObjectManagerInterface $objectManager, ScopePool $scopePool, DevHelper $devHelper, Request $request)
    {
        $this->objectManager = $objectManager;
        $this->scopePool = $scopePool;
        $this->devHelper = $devHelper;
        $this->request = $request;

        /*
         *
          dev/debug/profiler
          dev/debug/template_hints_storefront
          dev/debug/template_hints_admin
          dev/debug/template_hints_blocks
         * 
         */
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->devHelper->isDevAllowed() &&
                null !== $this->request->getQuery('hint')) {

            $paths = [
                'dev/debug/template_hints_storefront' => 1,
                'dev/debug/template_hints_admin' => 1,
                'dev/debug/template_hints_blocks' => 1
            ];
            $scope = $this->scopePool->getScope(ScopeInterface::SCOPE_STORE, null);
            foreach ($paths as $path => $value) {
                $scope->setValue($path, $value);
            }
        }
    }

}
