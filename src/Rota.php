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


use Psr\Http\Message\ServerRequestInterface;
use RautereX\Contracts\MiddlewareInterface;

class Rota
{
    /** @var string */
    private $url;
    /** @var string */
    private $controle;
    /** @var string */
    private $acao;
    /** @var array */
    private $middlewares;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Rota
     */
    public function setUrl(string $url): Rota
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getControle(): string
    {
        return $this->controle;
    }

    /**
     * @param string $controle
     * @return Rota
     */
    public function setControle(string $controle): Rota
    {
        $this->controle = $controle;
        return $this;
    }

    /**
     * @return string
     */
    public function getAcao(): string
    {
        return $this->acao;
    }

    /**
     * @param string $acao
     * @return Rota
     */
    public function setAcao(string $acao): Rota
    {
        $this->acao = $acao;
        return $this;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return Rota
     */
    public function addMiddleware(MiddlewareInterface $middleware): Rota
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @param MiddlewareInterface ...$middlewares
     */
    public function middlewares(MiddlewareInterface ...$middlewares): Rota
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        return $this;
    }

    /**
     * Executar os middleares dessa cota
     */
    public function executarMiddlewares()
    {
        foreach ($this->middlewares as $middleware) {
            call_user_func_array([$middleware, 'executar'], []);
        }
    }

    /**
     * Executar o controle referente a essa rota.
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function executar(ServerRequestInterface $request)
    {
        $this->executarMiddlewares();
        return call_user_func_array([$this->getControle(), $this->getAcao()], [$request]);
    }
}