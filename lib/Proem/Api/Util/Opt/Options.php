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
 * @namespace Proem\Api\Util\Opt
 */
namespace Proem\Api\Util\Opt;

/**
 * Proem\Api\Util\Opt\Options
 */
trait Options
{
    /**
     * Merge default Options with user supplied arguments applying validation in the process.
     *
     * @param array $default Default Options
     * @param array $options User supplied Options
     * @return array $defaults End result of merging default options with validated user options
     */
    public function setOptions($defaults, $options)
    {
        foreach ($options as $key => $value) {
            if (isset($defaults[$key]) && ($defaults[$key] instanceof Option)) {
                $defaults[$key]->setValue($value);
            } else {
                $defaults[$key] = new Option($value);
            }
        }

        foreach ($defaults as $key => $value) {
            if ($value instanceof Option) {
                try {
                    $value->validate($options);
                    $defaults[$key] = $value->getValue();
                } catch (\InvalidArgumentException $e) {
                    throw new \InvalidArgumentException($key . $e->getMessage());
                } catch (\RuntimeException $e) {
                    throw new \RuntimeException($e->getMessage());
                }
            }
        }
        return (object) $defaults;
    }
}
