<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2012 Tony R Quilkey <trq@proemframework.org>
 *
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
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


/**
 * @namespace Proem\Api\Filter\Event
 */
namespace Proem\Api\Filter\Event;

use Proem\Filter\Manager as FilterManager,
    Proem\Service\Manager as ServiceManager;

/**
 * Proem\Api\Filter\Event\Generic
 */
abstract class Generic
{
    /**
     * preIn
     *
     * Called prior to inBound
     */
    public function preIn(ServiceManager $assets) {}

    /**
     * inBound
     *
     * Define the method to be called on the way into the filter.
     */
    public abstract function inBound(ServiceManager $assets);

    /**
     * postIn
     *
     * Called after inBound
     */
    public function postIn(ServiceManager $assets) {}

    /**
     * preOut
     *
     * Called prior outBound
     */
    public function preOut(ServiceManager $assets) {}

    /**
     * outBound
     *
     * Define the method to be called on the way out of the filter.
     */
    public abstract function outBound(ServiceManager $assets);

    /**
     * postOut
     *
     * Called after outBound
     */
    public function postOut(ServiceManager $assets) {}

    /**
     * init
     *
     * Call inBound(), the next event in the filter, then outBound()
     *
     * @param Proem\Api\Filter\Manager $filterManager
     * @return Proem\Api\Filter\Manager
     */
    public function init(FilterManager $filterManager)
    {
        $this->preIn($filterManager->getServiceManager());
        $this->inBound($filterManager->getServiceManager());
        $this->postIn($filterManager->getServiceManager());

        if ($filterManager->hasEvents()) {
            $event = $filterManager->getNextEvent();
            if (is_object($event)) {
                $event->init($filterManager);
            }
        }

        $this->preOut($filterManager->getServiceManager());
        $this->outBound($filterManager->getServiceManager());
        $this->postOut($filterManager->getServiceManager());

        return $this;
    }
}
