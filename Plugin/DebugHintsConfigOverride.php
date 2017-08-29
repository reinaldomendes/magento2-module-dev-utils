<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ReinaldoMendes\DevUtils\Plugin;

use \Magento\Framework\App\Request\Http as Request,
    \Magento\Framework\App\Config\ScopeConfigInterface,
    \Magento\Store\Model\StoreManagerInterface;


class DebugHintsConfigOverride
{

    /**
     *
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
    * @var ScopeConfigInterface
    */
    private $paths = [
        'dev/debug/template_hints_storefront' => 1,
        'dev/debug/template_hints_admin' => 1,
        'dev/debug/template_hints_blocks' => 1,
    ];

    /**
    * @var boolean
    */
    private $isHint = false;


    public function __construct(Request $request,StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        $this->request = $request;
        if (null !== $this->request->getQuery('hint')) {//return early if not hint query
            $this->isHint = true;
        }
    }



    public function aroundGetValue($subject,callable $proceed,$path=null,$scope= ScopeConfigInterface::SCOPE_TYPE_DEFAULT,$scopeCode=null){
        if($this->isHint && isset($this->paths[$path])){
            return $this->paths[$path];
        }

        $returnValue = $proceed($path,$scope,$scopeCode);
        return $returnValue;

    }
}
