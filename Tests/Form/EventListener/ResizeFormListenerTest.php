<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests\Form\EventListener;

use Arxy\EntityTranslationsBundle\Form\EventListener\ResizeFormListener;
use Arxy\EntityTranslationsBundle\Tests\Entity\Language;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class ResizeFormListenerTest extends TestCase
{
    private $dispatcher;

    /**
     * @var FormFactory|\PHPUnit_Framework_MockObject_MockBuilder
     */
    private $factory;

    /**
     * @var FormInterface
     */
    private $form;

    private $languages = [];

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder(
            'Symfony\Component\EventDispatcher\EventDispatcherInterface'
        )->getMock();
        $this->factory = $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')->getMock();
        $this->form = $this->getBuilder()
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->getForm();

        $this->languages[] = new Language('en');
        $this->languages[] = new Language('bg');
    }

    protected function tearDown(): void
    {
        $this->dispatcher = null;
        $this->factory = null;
        $this->form = null;
        $this->languages = [];
    }

    protected function getBuilder($name = 'name')
    {
        return new FormBuilder($name, null, $this->dispatcher, $this->factory);
    }

    protected function getForm($name = 'name')
    {
        return $this->getBuilder($name)->getForm();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDataMapper()
    {
        return $this->getMockBuilder('Symfony\Component\Form\DataMapperInterface')->getMock();
    }

    protected function getMockForm()
    {
        return $this->getMockBuilder('Symfony\Component\Form\Test\FormInterface')->getMock();
    }

    public function testPreSetDataResizesFormWithEmptyData()
    {
        $this->factory->expects($this->at(0))
            ->method('createNamed')
            ->will($this->returnValue($this->getForm('en')));

        $this->factory->expects($this->at(1))
            ->method('createNamed')
            ->will($this->returnValue($this->getForm('bg')));

        $data = [];

        $event = new FormEvent($this->form, $data);

        $listener = new ResizeFormListener('text', [], [], $this->languages);
        $listener->preSetData($event);

        $this->assertTrue($this->form->has('en'));
        $this->assertTrue($this->form->has('bg'));
    }
}