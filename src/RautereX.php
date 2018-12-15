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

use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RautereX\Contracts\MiddlewareInterface;
use RautereX\Exceptions\RotaNaoEncontradaException;

/**
 * Class RautereX
 * @package RautereX
 */
class RautereX
{
    /** @var array */
    private $rotas = [];
    /** @var Container */
    private $container;

    /**
     * @return array
     */
    public function getRotas(): array
    {
        return $this->rotas;
    }

    /**
     * RautereX constructor.
     * @param Container|null $container
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;
    }

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
    public function add(string $url, array $rota, string $method = 'get'): Rota
    {
        $rota = (new Rota())
            ->setUrl($url)
            ->setControle($rota[0])
            ->setAcao($rota[1]);

        $this->rotas[$method][strtolower($url)] = $rota;
        return current($this->rotas[$method]);
    }

    /**
     * Adicionar uma rota no para ser usada com o REQUEST_METHOD GET
     * @param string $url
     * @param array $rota
     * @return Rota
     */
    public function get(string $url, array $rota): Rota
    {
        return $this->add($url, $rota, 'get');
    }

    /**
     * Adicionar uma rota no para ser usada com o REQUEST_METHOD POST
     * @param string $url
     * @param array $rota
     * @return Rota
     */
    public function post(string $url, array $rota): Rota
    {
        return $this->add($url, $rota, 'post');
    }

    /**
     * Adicionar uma rota no para ser usada com o REQUEST_METHOD PUT
     * @param string $url
     * @param array $rota
     * @return Rota
     */
    public function put(string $url, array $rota): Rota
    {
        return $this->add($url, $rota, 'put');
    }

    /**
     * Adicionar uma rota no para ser usada com o REQUEST_METHOD DELETE
     * @param string $url
     * @param array $rota
     * @return Rota
     */
    public function delete(string $url, array $rota): Rota
    {
        return $this->add($url, $rota, 'delete');
    }

    /**
     * Todas as rotas de um m�todo espec�fico.
     * @param string $method
     * @return array|null
     */
    public function getRotasByMethod(string $method = 'get'): ?array
    {
        return !array_key_exists(strtolower($method), $this->rotas)
            ? null
            : $this->rotas[$method];
    }

    /**
     * Encontrar uma determinada rota pela sua URL.
     * @param string $url
     * @param string $method
     * @return null|Rota
     */
    public function findRotaByUrl(string $url, string $method = 'get'): ?Rota
    {
        if (!$this->existsRota($url, $method)) {
            return null;
        }

        return $this->rotas[$method][strtolower($url)];
    }

    /**
     * Verifica se a rota existe.
     * @param string $url
     * @param string $method
     * @return bool
     */
    private function existsRota(string $url, string $method = 'post'): bool
    {
        return array_key_exists($method, $this->rotas) && array_key_exists($url, $this->rotas[$method]);
    }

    /**
     * @param array $middlewares
     */
    public function executarMiddlewares(array $middlewares = [])
    {
        /** @var MiddlewareInterface $middleware */
        foreach ($middlewares as $middleware) {
            if ($middleware instanceof MiddlewareInterface) {
                if ($this->container instanceof Container) {
                    $this->executarViaContainer(
                        get_class($middleware),
                        'executar'
                    );
                } else {
                    $middleware->executar();
                }
            }
        }
    }

    /**
     * Obtém uma instância da classe via relection e executa determinado método
     * @param string $classe Nome da classe a ser executada
     * @param string $metodo Nome do método a ser executado
     * @param array $params Array contendo os parâmetros a serem passados para o MÉTODO
     * @return mixed
     * @throws \ReflectionException
     */
    public function executarViaReflection(string $classe, string $metodo, array $params = [])
    {
        $rfx_classe = new \ReflectionClass($classe);
        $inst_classe = $rfx_classe->newInstance();
        return $inst_classe->{$metodo}(...$params);
    }

    /**
     * Obtém uma intância da classe via container e executa determinado método
     * @param string $classe Nome da classe a ser executada
     * @param string $metodo Nome do método a ser executado
     * @param array $params Array contendo os parâmetros a serem passados para o MÉTODO
     * @return mixed
     */
    public function executarViaContainer(string $classe, string $metodo, array $params = [])
    {
        $instancia = $this->container->get($classe);
        return $instancia->{$metodo}(...$params);
    }

    /**
     * Executar determinada rota.
     * @param string $url
     * @param string $method
     * @return ResponseInterface
     * @throws RotaNaoEncontradaException
     * @throws \ReflectionException
     */
    public function executarRota(
        string $url,
        ServerRequestInterface $request,
        string $method = 'get'
    ): ResponseInterface {
        $rota = $this->findRotaByUrl($url, strtolower($method));

        if (is_null($rota)) {
            throw new RotaNaoEncontradaException($url);
        }

        $this->executarMiddlewares($rota->getMiddlewares());

        if ($this->container instanceof Container) {
            return $this->executarViaContainer(
                $rota->getControle(),
                $rota->getAcao(),
                [$request]
            );
        }

        return $this->executarViaReflection(
            $rota->getControle(),
            $rota->getAcao(),
            [$request]
        );
    }
}