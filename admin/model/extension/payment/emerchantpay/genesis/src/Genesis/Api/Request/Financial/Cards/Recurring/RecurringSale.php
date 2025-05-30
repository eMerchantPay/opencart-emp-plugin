<?php

/**
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author      emerchantpay
 * @copyright   Copyright (C) 2015-2025 emerchantpay Ltd.
 * @license     http://opensource.org/licenses/MIT The MIT License
 */

namespace Genesis\Api\Request\Financial\Cards\Recurring;

use Genesis\Api\Request\Base\Financial;
use Genesis\Api\Traits\Request\Financial\Business\BusinessAttributes;
use Genesis\Api\Traits\Request\Financial\PaymentAttributes;
use Genesis\Api\Traits\Request\Financial\ReferenceAttributes;
use Genesis\Api\Traits\Request\Financial\TravelData\TravelDataAttributes;
use Genesis\Api\Traits\Request\MotoAttributes;
use Genesis\Utils\Common;
use Genesis\Utils\Currency;

/**
 * Class RecurringSale
 *
 * Recurring Sale Request
 *
 * @package Genesis\Api\Request\Financial\Cards\Recurring
 */
class RecurringSale extends Financial
{
    use BusinessAttributes;
    use MotoAttributes;
    use PaymentAttributes;
    use ReferenceAttributes;
    use TravelDataAttributes;

    /**
     * Returns the Request transaction type
     * @return string
     */
    protected function getTransactionType()
    {
        return \Genesis\Api\Constants\Transaction\Types::RECURRING_SALE;
    }

    /**
     * Set the required fields
     *
     * @return void
     */
    protected function setRequiredFields()
    {
        $requiredFields = [
            'transaction_id',
            'reference_id',
            'amount'
        ];

        $this->requiredFields = \Genesis\Utils\Common::createArrayObject($requiredFields);
    }

    /**
     * Identify the Currency of the Transaction
     *
     * @param string $value
     * @return $this
     * @throws \Genesis\Exceptions\InvalidArgument
     */
    public function setCurrency($value)
    {
        if (empty($value)) {
            $this->currency = null;

            return $this;
        }

        return $this->allowedOptionsSetter(
            'currency',
            Currency::getList(),
            $value,
            'Invalid value given for currency parameter.'
        );
    }

    /**
     * @return array
     */
    protected function getPaymentTransactionStructure()
    {
        return [
            'reference_id'        => $this->reference_id,
            'amount'              => !empty($this->currency) ?
                $this->transformAmount($this->amount, $this->currency) : $this->amount,
            'moto'                => Common::toBoolean($this->moto),
            'travel'              => $this->getTravelData(),
            'business_attributes' => $this->getBusinessAttributesStructure()
        ];
    }
}
