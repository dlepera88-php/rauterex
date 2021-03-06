<?php
/**
 * Created by PhpStorm.
 * User: diegol
 * Date: 03/12/2018
 * Time: 14:10
 */

namespace Tests;


use League\Container\Container;
use League\Container\ReflectionContainer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RautereX\Exceptions\RotaNaoEncontradaException;
use RautereX\RautereX;
use RautereX\Rota;

class RautereXTest extends TestCase
{
    public function test_adicionar_rota_via_get()
    {
        $rauter_x = new RautereX();
        $rota = $rauter_x->get('/index', [RautereX::class, 'add']);

        $rotas_get = $rauter_x->getRotasByMethod('get');

        $this->assertInstanceOf(Rota::class, $rota);
        $this->assertArrayHasKey('get', $rauter_x->getRotas());
        $this->assertCount(1, $rauter_x->getRotasByMethod('get'));
        $this->assertTrue(array_key_exists('/index', $rotas_get));
    }

    public function test_adicionar_rota_via_post()
    {
        $rauter_x = new RautereX();
        $rota = $rauter_x->post('/index', [RautereX::class, 'add']);

        $rotas_post = $rauter_x->getRotasByMethod('post');

        $this->assertInstanceOf(Rota::class, $rota);
        $this->assertArrayHasKey('post', $rauter_x->getRotas());
        $this->assertCount(1, $rauter_x->getRotasByMethod('post'));
        $this->assertTrue(array_key_exists('/index', $rotas_post));
    }

    public function test_adicionar_rota_via_put()
    {
        $rauter_x = new RautereX();
        $rota = $rauter_x->put('/index', [RautereX::class, 'add']);

        $rotas_put = $rauter_x->getRotasByMethod('put');

        $this->assertInstanceOf(Rota::class, $rota);
        $this->assertArrayHasKey('put', $rauter_x->getRotas());
        $this->assertCount(1, $rauter_x->getRotasByMethod('put'));
        $this->assertTrue(array_key_exists('/index', $rotas_put));
    }

    public function test_adicionar_rota_via_delete()
    {
        $rauter_x = new RautereX();
        $rota = $rauter_x->delete('/index', [RautereX::class, 'add']);

        $rotas_delete = $rauter_x->getRotasByMethod('delete');

        $this->assertInstanceOf(Rota::class, $rota);
        $this->assertArrayHasKey('delete', $rauter_x->getRotas());
        $this->assertCount(1, $rauter_x->getRotasByMethod('delete'));
        $this->assertTrue(array_key_exists('/index', $rotas_delete));
    }

    public function test_inclusao_rotas_com_middleares()
    {
        $rauter_x = new RautereX();
        $rauter_x
            ->get('/index', [RautereX::class, 'add'])
            ->middlewares(
                new ExemploMiddleware(),
                new ExemploMiddleware()
            );

        $rota_adicionada = $rauter_x->findRotaByUrl('/index', 'get');

        $this->assertInstanceOf(Rota::class, $rota_adicionada);
        $this->assertEquals('/index', $rota_adicionada->getUrl());
        $this->assertEquals(RautereX::class, $rota_adicionada->getControle());
        $this->assertEquals('add', $rota_adicionada->getAcao());
        $this->assertCount(2, $rota_adicionada->getMiddlewares());
    }

    public function test_inclusao_varias_rotas_com_middleware()
    {
        $rauter_x = new RautereX();
        $rauter_x
            ->get('/index', [RautereX::class, 'add'])
            ->middlewares(new ExemploMiddleware());

        $rauter_x
            ->get('/index2', [RautereX::class, 'add'])
            ->middlewares(new ExemploMiddleware());

        $rota1 = $rauter_x->findRotaByUrl('/index', 'get');
        $rota2 = $rauter_x->findRotaByUrl('/index2', 'get');

        $this->assertCount(1, $rota1->getMiddlewares());
        $this->assertCount(1, $rota2->getMiddlewares());
    }

    /**
     * @throws \RautereX\Exceptions\RotaNaoEncontradaException
     * @throws \ReflectionException
     */
    public function test_executarRota_com_rota_invalida()
    {
        $this->expectException(RotaNaoEncontradaException::class);

        $rauter_x = new RautereX();
        $rauter_x->executarRota('/index', new ExemploServerRequest(), 'get');
    }

    /**
     * @throws RotaNaoEncontradaException
     * @throws \ReflectionException
     */
    public function test_executarRota_com_rota_valida()
    {
        $rauter_x = new RautereX();
        $rauter_x
            ->get('/index', [ExemploController::class, 'executar'])
            ->middlewares(new ExemploMiddleware());

        $response = $rauter_x->executarRota('/index', new ExemploServerRequest(), 'get');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @throws RotaNaoEncontradaException
     * @throws \ReflectionException
     */
    public function test_create_com_container()
    {
        $container = new Container();
        $container->delegate(new ReflectionContainer());

        $rauter_x = new RautereX($container);
        $rauter_x->get('/index', [ExemploComDependenciasController::class, 'executar']);

        // Se a rota for executada corretamente, significa que funcionou, pois o controller ExemploComDependenciasController
        // tem dependência no seu constructor. Se tentar instanciar o controller sem a dependência, o PHP lança uma
        // exception
        $response = $rauter_x->executarRota('/index', new ExemploServerRequest(), 'get');
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}