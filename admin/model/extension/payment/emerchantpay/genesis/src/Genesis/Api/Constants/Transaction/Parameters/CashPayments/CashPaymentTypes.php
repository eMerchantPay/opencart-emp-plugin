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

namespace Genesis\Api\Constants\Transaction\Parameters\CashPayments;

use Genesis\Utils\Common;

/**
 * Class CashTransactionTypes
 *
 * @package Genesis\Api\Constants\Transaction\Parameters\CashPayments
 */
class CashPaymentTypes
{
    /**
     * 7 Eleven
     *
     * @var string
     */
    const SEVEN_ELEVEN = 'seven_eleven';

    /**
     * Bancomer
     *
     * @var string
     */
    const BANCOMER = 'bancomer';

    /**
     * Farmacias del Dr. Ahorro
     *
     * @var string
     */
    const PHARMACIES_DEL_DR_AHORRO = 'pharmacies_del_dr_ahorro';

    /**
     * Farmacias Santa Maria
     *
     * @var string
     */
    const PHARMACIES_SANTA_MARIA = 'pharmacies_santa_maria';

    /**
     * OXXO
     *
     * @var string
     */
    const OXXO = 'oxxo';

    /**
     * Scotiabank
     *
     * @var string
     */
    const SCOTIABANK = 'scotiabank';

    /**
     * Get all Cash Transaction Types
     *
     * @return array
     */
    public static function getAll()
    {
        return Common::getClassConstants(self::class);
    }
}
