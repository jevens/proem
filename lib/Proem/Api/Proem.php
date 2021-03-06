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
 * @namespace Proem\Api
 */
namespace Proem\Api;

use Proem\Service\Manager as ServiceManager,
    Proem\Signal\Manager as SignalManager,
    Proem\Service\Asset\Generic as Asset,
    Proem\Bootstrap\Filter\Event,
    Proem\Bootstrap\Signal\Event\Bootstrap,
    Proem\Filter\Manager as FilterManager,
    Proem\Ext\Generic as Extension,
    Proem\Ext\Module\Generic as Module,
    Proem\Ext\Plugin\Generic as Plugin;

/**
 * Proem\Api\Proem
 *
 * The Proem boostrap wrapper (eventually)
 */
class Proem
{
    /**
     * Store the framework version
     */
    const VERSION = '0.1.5';

    /**
     * Store events
     *
     * @var Proem\Api\Signal\Manager
     */
    private $events;

    /**
     * Store Modules / Plugins
     */
    private $extensions = [];

    /**
     * Bootstrap Extensions
     */
    private function bootstrapExtensions(ServiceManager $serviceManager, $env = null)
    {
        foreach ($this->extensions as $extension) {
            $extension->init($serviceManager, $env);
        }
    }

    /**
     * Register Modules / Plugins
     */
    private function attachExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
        return $this;
    }

    /**
     * Setup bootstraping
     */
    public function __construct()
    {
        $this->events = new Asset;
        $this->events->set('\Proem\Signal\Manager', $this->events->single(function($asset) {
            return new SignalManager;
        }));
    }

    /**
     * Attach a listener to the Signal Event Manager
     */
    public function attachEventListener(Array $listener)
    {
        $this->events->get()->attach($listener);
        return $this;
    }

    /**
     * Attach a series of event to the Signal Event Manager
     */
    public function attachEventListeners(Array $listeners)
    {
        foreach ($listeners as $listener) {
            $this->attachEventListener($listener);
        }
        return $this;
    }

    /**
     * Register a Plugin
     */
    public function attachPlugin(Plugin $plugin)
    {
        return $this->attachExtension($plugin);
    }

    /**
     * Register a Module
     */
    public function attachModule(Extension $module)
    {
        return $this->attachExtension($module);
    }

    /**
     * Setup and execute the Filter Manager
     */
    public function init($env = null)
    {
        $serviceManager = (new ServiceManager)->set('events', $this->events);
        $this->bootstrapExtensions($serviceManager, $env);

        (new FilterManager($serviceManager))
            ->attachEvent(new Event\Response, FilterManager::RESPONSE_EVENT_PRIORITY)
            ->attachEvent(new Event\Request, FilterManager::REQUEST_EVENT_PRIORITY)
            ->attachEvent(new Event\Route, FilterManager::ROUTE_EVENT_PRIORITY)
            ->attachEvent(new Event\Dispatch, FilterManager::DISPATCH_EVENT_PRIORITY)
            ->init();
    }
}
