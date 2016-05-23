<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ReinaldoMendes\DevUtils\Plugin;

use \Magento\Framework\App\Request\Http as Request,
    \Magento\Framework\App\Config\ScopePool,
    \Magento\Store\Model\ScopeInterface;

class DebugHints
{

    /**
     *
     * @var \Magento\Framework\App\Request\Http 
     */
    private $request;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopePool 
     */
    private $scopePool;

    public function __construct(ScopePool $scopePool, Request $request)
    {
        $this->scopePool = $scopePool;
        $this->request = $request;
    }

    public function aroundCreate($object, $method, $argument)
    {
        if (null === $this->request->getQuery('hint')) {//return early if not hint query
            return $method($argument);
        }

        //force variables debug hints to be enable
        $paths = [
            'dev/debug/template_hints_storefront' => 1,
            'dev/debug/template_hints_admin' => 1,
            'dev/debug/template_hints_blocks' => 1
        ];
        $scope = $this->scopePool->getScope(ScopeInterface::SCOPE_STORE, null);
        foreach ($paths as $path => $value) {
            
            $paths[$path] = $scope->getValue($path, $value);//save original value in paths            
            $scope->setValue($path, $value);
        }

        //call original method
        $result = $method($argument);

        //return original states of variables
        foreach ($paths as $path => $originalValue) {
            $scope->setValue($path, $originalValue);
        }
        return $result;
    }

}
