<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Search\Controller\Adminhtml\Term;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class MassDeleteTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $messageManager;

    /** @var  \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $objectManager;

    /** @var \Magento\Search\Controller\Adminhtml\Term\MassDelete */
    private $controller;

    /** @var ObjectManagerHelper */
    private $objectManagerHelper;

    /** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
    private $context;

    /** @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $pageFactory;

    /** @var \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $redirectFactory;

    /** @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $response;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $request;
    /** @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject */
    private $redirect;

    protected function setUp()
    {
        $this->request = $this->getMockBuilder('\Magento\Framework\App\RequestInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMockForAbstractClass();
        $this->response = $this->getMockBuilder('\Magento\Framework\App\ResponseInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMockForAbstractClass();
        $this->objectManager = $this->getMockBuilder('\Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();
        $this->messageManager = $this->getMockBuilder('\Magento\Framework\Message\ManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['addSuccess', 'addError'])
            ->getMockForAbstractClass();
        $this->context = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->setMethods(['getRequest', 'getResponse', 'getObjectManager', 'getMessageManager'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->atLeastOnce())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->context->expects($this->atLeastOnce())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
        $this->context->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->context->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManager));
        $this->pageFactory = $this->getMockBuilder('Magento\Framework\View\Result\PageFactory')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->redirect = $this->getMockBuilder('Magento\Backend\Model\View\Result\Redirect')
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->redirectFactory = $this->getMockBuilder('Magento\Backend\Model\View\Result\RedirectFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->redirectFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->redirect));
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->controller = $this->objectManagerHelper->getObject(
            'Magento\Search\Controller\Adminhtml\Term\MassDelete',
            [
                'context' => $this->context,
                'resultPageFactory' => $this->pageFactory,
                'resultRedirectFactory' => $this->redirectFactory
            ]
        );
    }

    public function testExecute()
    {
        $ids = [1, 2];
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('search')
            ->will($this->returnValue($ids));

        $this->createQuery(0, 1);
        $this->createQuery(1, 2);
        $this->messageManager->expects($this->once())
            ->method('addSuccess')
            ->will($this->returnSelf());
        $this->redirect->expects($this->once())
            ->method('setPath')
            ->with('search/*/')
            ->will($this->returnSelf());

        $result = $this->controller->execute();
        $this->assertSame($this->redirect, $result);
    }

    /**
     * @param $index
     * @param $id
     * @return \Magento\Search\Model\Query|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createQuery($index, $id)
    {
        $query = $this->getMockBuilder('Magento\Search\Model\Query')
            ->disableOriginalConstructor()
            ->setMethods(['load', 'delete'])
            ->getMock();
        $query->expects($this->at(0))
            ->method('delete')
            ->will($this->returnSelf());
        $query->expects($this->at(0))
            ->method('load')
            ->with($id)
            ->will($this->returnSelf());
        $this->objectManager->expects($this->at($index))
            ->method('create')
            ->with('Magento\Search\Model\Query')
            ->will($this->returnValue($query));
        return $query;
    }
}
