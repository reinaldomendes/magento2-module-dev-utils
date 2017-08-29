<?php

namespace ReinaldoMendes\DevUtils\Test\Unit;

class DebugHintsTest extends \PHPUnit_Framework_TestCase
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

    public function setUp()
    {

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
    }

    public function testCreateActive()
    {
        $this->devHelperMock->expects($this->once())
                ->method('isDevAllowed')
                ->willReturn(true);
        $this->assertTrue($this->devHelperMock->isDevAllowed());
        $this->requestMock->expects($this->once())
                ->method('getQuery')
                ->willReturn('?hint');
        $this->assertEquals($this->requestMock->getQuery(), '?hint');



        $debugHintsDecorator = $this->getMockBuilder(
                        'Magento\Developer\Model\TemplateEngine\Decorator\DebugHints'
                )
                ->disableOriginalConstructor()
                ->getMock();

        $engine = $this->getMock('Magento\Framework\View\TemplateEngineInterface');
        $this->debugHintsFactory->expects($this->once())
                ->method('create')
                ->with([
                    'subject' => $engine,
                    'showBlockHints' => true,
                ])
                ->willReturn($debugHintsDecorator);

        $subjectMock = $this->getMockBuilder('Magento\Framework\View\TemplateEngineFactory')
                ->disableOriginalConstructor()
                ->getMock();
        
        $debugHints = new \ReinaldoMendes\DevUtils\Plugin\DebugHints(
                $this->scopeConfigMock, $this->storeManager,
                $this->devHelperMock, $this->debugHintsFactory,
                'dev/debug/template_hints_storefront'
        );


        $this->assertEquals($debugHintsDecorator,
                $debugHints->afterCreate($subjectMock, $engine));
    }

}
