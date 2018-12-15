RautereX
===
Controlador de rotas simples.

***ATENÇÃO:*** ESSE PROJETO AINDA ESTÁ EM DESENVOLVIMENTO.

O que o RautereX faz?
--
O **RautereX** gerencia suas rotas e executa da maneira mais simples possível.
Para faclitar, o **RautereX** permite passar uma ServerRequestInterface como parâmetro da ação do controller e espera uma ResponseInterface.

Uso básico
--

```
<?php
use RautereX;

$rauter_x = new RautereX($container);
$rauter_x->get(
    '/index',
    [AlgumaClasse::class, 'index']
);

$rauter_x->executarRota(
    '/index',
    null,
    'get'
);
```

v1.1
--
- Adicionado suporte para injeção de dependências.
Obs: Por enquanto está sendo usado o container do League Router e é o único suportado.

```php
<?php
use League\Container\Container;
use League\Container\ReflectionContainer;
use RautereX\RautereX;

class AlgumaClasse {
    /** @var OutraClasse */
    private $alguma_coisa;
   
    public function __construct(OutraClasse $outra_classe) {
        $this->alguma_coisa = $outra_classe;
    }
}

class OutraClasse {
    
}

$container = new Container;
$container->delegate(new ReflectionContainer);

$rauter_x = new RautereX($container);
$rauter_x->get(
    '/index',
    [AlgumaClasse::class, 'index']
); 
```
- RequestServerInterface não é mais um parâmetro origatório.