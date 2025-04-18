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

namespace Genesis\Api\Traits\Request\Financial\Threeds\V2;

use Genesis\Api\Constants\Transaction\Parameters\Threeds\V2\Browser\ColorDepths;
use Genesis\Exceptions\InvalidArgument;
use Genesis\Utils\Common;

/**
 * Trait Browser
 * @package Genesis\Api\Traits\Request\Financial\Threeds\V2
 *
 * @codingStandardsIgnoreStart
 * @method string getThreedsV2BrowserAcceptHeader()     Exact content of the HTTP accept headers as sent to the 3DS Requester from the Cardholder browser
 * @method string getThreedsV2BrowserLanguage()         Value representing the browser language as defined in IETF BCP47
 * @method int    getThreedsV2BrowserColorDepth()       Value representing the bit depth of the colour palette for displaying images, in bits per pixel
 * @method int    getThreedsV2BrowserScreenHeight()     Total height of the Cardholder's screen in pixels
 * @method int    getThreedsV2BrowserScreenWidth()      Total width of the Cardholder's screen in pixels
 * @method string getThreedsV2BrowserTimeZoneOffset()   Time difference between UTC time and the Cardholder browser local time, in minutes
 * @method string getThreedsV2BrowserUserAgent()        Exact content of the HTTP user-agent header
 * @codingStandardsIgnoreEnd
 */
trait Browser
{
    /**
     * Exact content of the HTTP accept headers as sent to the 3DS Requester from the Cardholder browser
     *
     * @var string $threeds_v2_browser_accept_header
     */
    protected $threeds_v2_browser_accept_header;

    /**
     * Boolean that represents the ability of the cardholder browser to execute Java
     *
     * @var bool $threeds_v2_browser_java_enabled
     */
    protected $threeds_v2_browser_java_enabled;

    /**
     * Value representing the browser language as defined in IETF BCP47
     *
     * @var string $threeds_v2_browser_language
     */
    protected $threeds_v2_browser_language;

    /**
     * Value representing the bit depth of the colour palette for displaying images, in bits per pixel
     *
     * @var int $threeds_v2_browser_color_depth
     */
    protected $threeds_v2_browser_color_depth;

    /**
     * Total height of the Cardholder's screen in pixels
     *
     * @var int $threeds_v2_browser_screen_height
     */
    protected $threeds_v2_browser_screen_height;

    /**
     * Total width of the Cardholder's screen in pixels
     *
     * @var int $threeds_v2_browser_screen_width
     */
    protected $threeds_v2_browser_screen_width;

    /**
     * Time difference between UTC time and the Cardholder browser local time, in minutes
     *
     * @var string $threeds_v2_browser_time_zone_offset
     */
    protected $threeds_v2_browser_time_zone_offset;

    /**
     * Exact content of the HTTP user-agent header
     *
     * @var string $threeds_v2_browser_user_agent
     */
    protected $threeds_v2_browser_user_agent;

    /**
     * Exact content of the HTTP accept headers as sent to the 3DS Requester from the Cardholder browser
     *
     * @param string $value
     * @return $this
     * @throws InvalidArgument
     */
    public function setThreedsV2BrowserAcceptHeader($value)
    {
        return $this->setLimitedString(
            'threeds_v2_browser_accept_header',
            $value,
            null,
            2048
        );
    }

    /**
     * Value representing the browser language as defined in IETF BCP47
     *
     * @param $value
     * @return $this
     * @throws InvalidArgument
     */
    public function setThreedsV2BrowserLanguage($value)
    {
        return $this->setLimitedString(
            'threeds_v2_browser_language',
            $value,
            null,
            8
        );
    }

    /**
     * Value representing the bit depth of the colour palette for displaying images, in bits per pixel
     *
     * @param string|int $value
     *
     * @return $this
     */
    public function setThreedsV2BrowserColorDepth($value)
    {
        $this->threeds_v2_browser_color_depth = $this->fetchColorDepth($value);

        return $this;
    }

    /**
     * Boolean that represents the ability of the cardholder browser to execute Java
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @return bool
     */
    public function getThreedsV2BrowserJavaEnabled()
    {
        return Common::toBoolean($this->threeds_v2_browser_java_enabled);
    }

    /**
     * Boolean that represents the ability of the cardholder browser to execute Java
     *
     * @param mixed $value
     * @return $this
     */
    public function setThreedsV2BrowserJavaEnabled($value)
    {
        $this->threeds_v2_browser_java_enabled = var_export(Common::toBoolean($value), true);

        return $this;
    }

    /**
     * Total height of the Cardholder's screen in pixels
     *
     * @param int $value
     * @return $this
     */
    public function setThreedsV2BrowserScreenHeight($value)
    {
        $this->threeds_v2_browser_screen_height = (int) $value;

        return $this;
    }

    /**
     * Total width of the Cardholder's screen in pixels
     *
     * @param int $value
     * @return $this
     */
    public function setThreedsV2BrowserScreenWidth($value)
    {
        $this->threeds_v2_browser_screen_width = (int) $value;

        return $this;
    }

    /**
     * Time difference between UTC time and the Cardholder browser local time, in minutes
     *
     * @param string $value
     * @return $this
     * @throws InvalidArgument
     */
    public function setThreedsV2BrowserTimeZoneOffset($value)
    {
        return $this->setLimitedString(
            'threeds_v2_browser_time_zone_offset',
            $value,
            null,
            5
        );
    }

    /**
     * Exact content of the HTTP user-agent header
     *
     * @param string $value
     * @return $this
     * @throws InvalidArgument
     */
    public function setThreedsV2BrowserUserAgent($value)
    {
        return $this->setLimitedString(
            'threeds_v2_browser_user_agent',
            $value,
            null,
            2048
        );
    }

    protected function getBrowserValidations()
    {
        return [
            'threeds_v2_browser_color_depth' => [
                $this->threeds_v2_browser_color_depth => [
                    ['threeds_v2_browser_color_depth' => ColorDepths::getAll()]
                ]
            ]
        ];
    }

    /**
     * Get the Browser Attributes
     *
     * @return array
     */
    protected function getBrowserAttributes()
    {
        return [
            'accept_header'    => $this->getThreedsV2BrowserAcceptHeader(),
            'java_enabled'     => $this->threeds_v2_browser_java_enabled,
            'language'         => $this->getThreedsV2BrowserLanguage(),
            'color_depth'      => $this->getThreedsV2BrowserColorDepth(),
            'screen_height'    => $this->getThreedsV2BrowserScreenHeight(),
            'screen_width'     => $this->getThreedsV2BrowserScreenWidth(),
            'time_zone_offset' => $this->getThreedsV2BrowserTimeZoneOffset(),
            'user_agent'       => $this->getThreedsV2BrowserUserAgent()
        ];
    }

    /**
     * Return all allowed zero non-empty field values
     * array(
     *     'class_property' => 'request_parameter'
     * )
     *
     * @return array
     */
    protected function getAllowedFieldsZeroValues()
    {
        return array(
            'threeds_v2_browser_time_zone_offset' => 'time_zone_offset'
        );
    }

    /**
     * Fetch proper color depth value
     *
     * The value as per EMVCo specs can be one of 1, 4, 8, 15, 16, 24, 32, 48.
     * In case, an unsupported color_depth is determined, the nearest supported value that is less than
     * the actual one needs to be submitted. For example, if the obtained value is 30,
     * which is not supported as per EMVCo specs, 24 has to be submitted.
     *
     * @param string|int $value
     *
     * @return int
     */
    protected function fetchColorDepth($value)
    {
        $colorDepth = (int) $value;

        if ($value > 0) {
            $filteredDepthsList = array_filter(
                ColorDepths::getAll(),
                function ($arrVal) use ($colorDepth) {
                    return $colorDepth >= $arrVal;
                }
            );

            $colorDepth = end($filteredDepthsList);
        }

        return $colorDepth;
    }
}
