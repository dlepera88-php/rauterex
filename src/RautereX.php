<?php
/**
 * MIT License
 *
 * Copyright (c) 2018 PHP DLX
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace RautereX;

/**
 * Class RautereX
 * @package RautereX
 * @method get(string $url, array $rota)
 * @method post(string $url, array $rota)
 * @method put(string $url, array $rota)
 * @method delete(string $url, array $rota)
 */
class RautereX
{
    /** @var array */
    private $rotas = [];

    public function __call($name, $arguments)
    {
        $arguments[] = $name;
        return call_user_func_array([$this, 'add'], $arguments);
    }

    /**
     * Adicionar uma nova rota.
     * @param string $url
     * @param array $rota
     * @param string $method
     */
    private function add(string $url, array $rota, string $method = 'get'): Rota
    {
        $rota = (new Rota())
            ->setUrl($url)
            ->setControle($rota[0])
            ->setAcao($rota[1]);

        $this->rotas[$method] = [$rota];

        return current($this->rotas[$method]);
    }
}