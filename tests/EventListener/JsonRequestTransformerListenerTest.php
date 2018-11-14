<?php

namespace App\Tests\EventListener;

use App\EventListener\JsonRequestTransformerListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonRequestTransformerListenerTest extends WebTestCase
{
    /**
     * @var JsonRequestTransformerListener
     */
    private $listener;

    public function setUp()
    {
        $this->listener = new JsonRequestTransformerListener();
    }

    /**
     * @test
     * @dataProvider jsonContentTypes
     */
    public function itTransformsRequestsWithAJsonContentType($contentType)
    {
        $data    = ['foo' => 'bar'];
        $request = $this->createRequest($contentType, json_encode($data));
        $event   = $this->createGetResponseEventMock($request);
        $this->listener->onKernelRequest($event);
        $this->assertEquals(
            $data,
            $event->getRequest()->request->all()
        );
        $this->assertNull($event->getResponse());
    }

    public function jsonContentTypes()
    {
        return array(
            array('application/json'),
            array('application/x-json'),
        );
    }

    /**
     * @test
     */
    public function itReturnsABadRequestResponseIfJsonIsInvalid()
    {
        $request = $this->createRequest('application/json', '{meh}');
        $event   = $this->createGetResponseEventMock($request);
        $this->listener->onKernelRequest($event);
        $this->assertEquals(400, $event->getResponse()->getStatusCode());
    }

    /**
     * @test
     * @dataProvider notJsonContentTypes
     */
    public function itDoesNotTransformOtherContentTypes($contentType)
    {
        $request = $this->createRequest($contentType, 'some=body');
        $event   = $this->createGetResponseEventMock($request);
        $this->listener->onKernelRequest($event);
        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function itDoesNotReplaceRequestDataIfThereIsNone()
    {
        $request = $this->createRequest('application/json', '');
        $event   = $this->createGetResponseEventMock($request);
        $this->listener->onKernelRequest($event);
        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function itDoesNotReplaceRequestDataIfContentIsJsonNull()
    {
        $request = $this->createRequest('application/json', 'null');
        $event   = $this->createGetResponseEventMock($request);
        $this->listener->onKernelRequest($event);
        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    public function notJsonContentTypes()
    {
        return array(
            array('application/x-www-form-urlencoded'),
            array('text/html'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'),
        );
    }

    private function createRequest($contentType, $body)
    {
        $request = new Request([], [], [], [], [], [], $body);
        $request->headers->set('CONTENT_TYPE', $contentType);
        return $request;
    }

    private function createGetResponseEventMock(Request $request)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getRequest'))
            ->getMock();
        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        return $event;
    }
}