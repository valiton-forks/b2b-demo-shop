<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Checkout;

use Pyz\Yves\Application\Plugin\Pimple;
use Pyz\Yves\Customer\Plugin\CustomerStepHandler;
use Pyz\Yves\Shipment\Plugin\ShipmentHandlerPlugin;
use Pyz\Yves\Shipment\Plugin\ShipmentSubFormPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

class CheckoutDependencyProvider extends AbstractBundleDependencyProvider
{

    const DUMMY_SHIPMENT = 'dummy_shipment';
    const CLIENT_CART = 'cart client';
    const CLIENT_CALCULATION = 'calculation client';
    const CLIENT_CHECKOUT = 'checkout client';
    const CLIENT_CUSTOMER = 'customer client';
    const PLUGIN_APPLICATION = 'application plugin';

    const PLUGIN_SHIPMENT_SUB_FORM = 'shipment sub form plugin';
    const PLUGIN_CUSTOMER_STEP_HANDLER = 'step handler plugin';

    const PAYMENT_METHOD_HANDLER = 'payment method handler';
    const PAYMENT_SUB_FORMS = 'payment sub forms';

    const PLUGIN_SHIPMENT_HANDLER = 'shipment handler plugin';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->provideClients($container);
        $container = $this->providePlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function provideClients(Container $container)
    {
        $container[self::CLIENT_CART] = function (Container $container) {
            return $container->getLocator()->cart()->client();
        };

        $container[self::CLIENT_CALCULATION] = function (Container $container) {
            return $container->getLocator()->calculation()->client();
        };

        $container[self::CLIENT_CHECKOUT] = function (Container $container) {
            return $container->getLocator()->checkout()->client();
        };

        $container[self::CLIENT_CUSTOMER] = function (Container $container) {
            return $container->getLocator()->customer()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function providePlugins(Container $container)
    {
        $container[self::PAYMENT_SUB_FORMS] = function () {
            return new SubFormPluginCollection();
        };

        $container[self::PAYMENT_METHOD_HANDLER] = function () {
            return new StepHandlerPluginCollection();
        };

        $container[self::PLUGIN_SHIPMENT_SUB_FORM] = function () {
            $shipmentSubForms = new SubFormPluginCollection();
            $shipmentSubForms->add(new ShipmentSubFormPlugin());

            return $shipmentSubForms;
        };

        $container[self::PLUGIN_CUSTOMER_STEP_HANDLER] = function () {
            return new CustomerStepHandler();
        };

        $container[self::PLUGIN_SHIPMENT_HANDLER] = function () {
            $shipmentHandlerPlugins = new StepHandlerPluginCollection();
            $shipmentHandlerPlugins->add(new ShipmentHandlerPlugin(), self::DUMMY_SHIPMENT);

            return $shipmentHandlerPlugins;
        };

        $container[self::PLUGIN_APPLICATION] = function () {
            $pimplePlugin = new Pimple();

            return $pimplePlugin->getApplication();
        };

        return $container;
    }

}
