<?php

namespace ReinaldoMendes\DevUtils\Test\Unit;
use \Magento\Framework\ObjectManagerInterface,
        \ReinaldoMendes\DevUtils\Plugin\DebugHintsConfigOverride;
class DebugHintsConfigOverrideTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Developer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $devHelperMock;

    /**
     * @var DebugHintsFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $debugHintsFactory;

    /**
     * @var Magento\Framework\App\Request\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
    * @var ObjectManagerInterface
    */
    protected $objectManager;

    public function setUp( )
    {

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')->getMockForAbstractClass();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
                ->disableOriginalConstructor()
                ->getMock();

        $this->scopeConfigMock = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getMockForAbstractClass();

        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
                ->getMockForAbstractClass();

        $this->devHelperMock = $this->getMockBuilder('Magento\Developer\Helper\Data')
                ->disableOriginalConstructor()
                ->getMock();

        $this->debugHintsFactory = $this->getMockBuilder(
                        'Magento\Developer\Model\TemplateEngine\Decorator\DebugHintsFactory'
                )
                ->setMethods(['create'])
                ->disableOriginalConstructor()
                ->getMock();

        $this->configMock = $this->getMockBuilder('Magento\Framework\App\Config')
                                ->disableOriginalConstructor()
                                 ->getMock();


        $this->storeMock = $this->getMockBuilder('\Magento\Store\Api\Data\StoreInterface')
                                ->getMockForAbstractClass();
    }


    public function testConfigReturns()
    {
        $this->devHelperMock->expects($this->any())
                ->method('isDevAllowed')
                ->willReturn(true);
        $this->assertTrue($this->devHelperMock->isDevAllowed());

        $this->requestMock->expects($this->any())
                ->method('getQuery')
                ->willReturn('?hint');
        $this->assertEquals($this->requestMock->getQuery(), '?hint');

        $debugHintsConfigOverride = new DebugHintsConfigOverride($this->requestMock,$this->storeManager,$this->devHelperMock);
        $matches = [
            'dev/debug/template_hints_admin' => 1,
            'dev/debug/template_hints_storefront' => 1,
            'dev/debug/template_hints_blocks' => 1,
            'nao-existe' => null
        ];
        $this->configMock->expects($this->once())
                ->method('getValue')
                ->with($this->equalTo('nao-existe'),$this->anyThing(),$this->anyThing())
                ->willReturn($matches['nao-existe']);
        foreach($matches as $path => $match){
            $testValue = $debugHintsConfigOverride->aroundGetValue($this->configMock,array($this->configMock,'getValue'),$path,'default',null);
            $this->assertEquals($testValue,$match,"{$path} Should return {$match}");
        }






    }
    public function testCreateActive()
    {
        $this->devHelperMock->expects($this->any())
                ->method('isDevAllowed')
                ->willReturn(true);
        $this->assertTrue($this->devHelperMock->isDevAllowed());

        $this->requestMock->expects($this->any())
                ->method('getQuery')
                ->willReturn('?hint');
        $this->assertEquals($this->requestMock->getQuery(), '?hint');



        $debugHintsDecorator = $this->getMockBuilder(
                        'Magento\Developer\Model\TemplateEngine\Decorator\DebugHints'
                )
                ->disableOriginalConstructor()
                ->getMock();

                

        $engineMock = $this->getMock('Magento\Framework\View\TemplateEngineInterface');
        $this->debugHintsFactory->expects($this->once())
                ->method('create')
                ->with([
                    'subject' => $engineMock,
                    'showBlockHints' => true,
                ]);

        $subjectMock = $this->getMockBuilder('Magento\Framework\View\TemplateEngineFactory')
                ->disableOriginalConstructor()
                ->getMock();

        $this->storeManager->expects($this->once())
                        ->method('getStore')
                        ->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())
                        ->method('getCode')
                        ->willReturn('default');

        $path = 'dev/debug/template_hints_storefront';
        $debugHints = new \Magento\Developer\Model\TemplateEngine\Plugin\DebugHints(
            $this->scopeConfigMock,
            $this->storeManager,
            $this->devHelperMock,
            $this->debugHintsFactory,
            $path
        );


        $debugHintsConfigOverride = new DebugHintsConfigOverride($this->requestMock,$this->storeManager,$this->devHelperMock);
        $this->scopeConfigMock->expects($this->any())
                            ->method('getValue')
                            ->with(
                                $this->stringContains('dev/debug/'),
                                $this->anyThing(),
                                $this->anyThing()
                            )
                            ->willReturn(1);
         $debugHints->afterCreate($subjectMock, $engineMock);
    }

}
