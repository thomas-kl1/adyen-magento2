<?php
/**
 * Adyen Payment module (https://www.adyen.com/)
 *
 * Copyright (c) 2022 Adyen BV (https://www.adyen.com/)
 * See LICENSE.txt for license details.
 *
 * Author: Adyen <magento@adyen.com>
 */
declare(strict_types=1);

namespace Adyen\Payment\Setup\Patch\Data;

use Adyen\Payment\Helper\PaymentMethods\ApplePayPaymentMethod;
use Adyen\Payment\Helper\PaymentMethods\GooglePayPaymentMethod;
use Adyen\Payment\Helper\PaymentMethods\PayPalPaymentMethod;
use Adyen\Payment\Helper\PaymentMethods\SepaPaymentMethod;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class PaymentMethods implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->addPaymentMethod(PayPalPaymentMethod::TX_VARIANT, true, true);
        $this->addPaymentMethod(SepaPaymentMethod::TX_VARIANT, true, true);
        $this->addPaymentMethod(GooglePayPaymentMethod::TX_VARIANT, true, true);
        $this->addPaymentMethod(ApplePayPaymentMethod::TX_VARIANT, true, true);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    private function addPaymentMethod(string $paymentMethod, bool $enableRecurring, bool $active)
    {
        $adyenPaymentMethodTable = $this->moduleDataSetup->getTable('adyen_payment_method');
        $connection = $this->moduleDataSetup->getConnection();

        $connection->insert($adyenPaymentMethodTable, [
            'payment_method' => $paymentMethod,
            'enable_recurring' => $enableRecurring,
            'active' => $active
        ]);
    }

    public static function getVersion()
    {
        return '8.4.0';
    }
}