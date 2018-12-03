<?php
/**
 * Created by PhpStorm.
 * User: diegol
 * Date: 03/12/2018
 * Time: 14:10
 */

namespace Tests;


use PHPUnit\Framework\TestCase;
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
}