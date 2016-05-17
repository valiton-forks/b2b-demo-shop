<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace YvesUnit\Pyz\Yves\Checkout\Process\Steps;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\Checkout\Process\Steps\PaymentStep;
use Spryker\Client\Cart\CartClient;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentStepTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testExecuteShouldSelectPlugin()
    {
        $paymentPluginMock = $this->createPaymentPluginMock();
        $paymentPluginMock->expects($this->once())->method('addToQuote');

        $paymentStepHandler = new StepHandlerPluginCollection();
        $paymentStepHandler->add($paymentPluginMock, 'test');
        $paymentStep = $this->createPaymentStep($paymentStepHandler);

        $quoteTransfer = new QuoteTransfer();

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentSelection('test');
        $quoteTransfer->setPayment($paymentTransfer);

        $paymentStep->execute($this->createRequest(), $quoteTransfer);
    }

    /**
     * @return void
     */
    public function testPostConditionsShouldReturnTrueWhenPaymentSet()
    {
        $quoteTransfer = new QuoteTransfer();
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentProvider('test');
        $quoteTransfer->setPayment($paymentTransfer);

        $paymentStep = $this->createPaymentStep(new StepHandlerPluginCollection());

        $this->getCartClient()->storeQuote($quoteTransfer);

        $this->assertTrue($paymentStep->postCondition());
    }


    /**
     * @return void
     */
    public function testShipmentRequireInputShouldReturnTrue()
    {
        $paymentStep = $this->createPaymentStep(new StepHandlerPluginCollection());
        $this->assertTrue($paymentStep->requireInput());
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection $paymentPlugins
     *
     * @return \Pyz\Yves\Checkout\Process\Steps\PaymentStep
     */
    protected function createPaymentStep(StepHandlerPluginCollection $paymentPlugins)
    {
        return new PaymentStep(
            $paymentPlugins,
            $this->getCartClient(),
            'payment',
            'escape_route'
        );
    }

    /**
     * @return \Spryker\Client\Cart\CartClient
     */
    protected function getCartClient()
    {
        return new CartClient();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest()
    {
        return Request::createFromGlobals();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface
     */
    protected function createPaymentPluginMock()
    {
        return $this->getMock(StepHandlerPluginInterface::class);
    }

}
