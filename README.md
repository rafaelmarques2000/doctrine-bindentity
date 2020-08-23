# doctrine-bindentity

## O que é ? 

Este pacote serve para efetuar o bind(carregamento) de entidades do doctrine de forma automatica durante a requisição do laravel assim ele pode ser injetado diretamente
na rota requisitada.

## Como instalar?

**Pre-requisito**
- php 7.4+
- laravel-doctrine/orm 1.6.*<br>

No seu terminal execute: <br>
**composer install rafaelmarques2000/doctrine-bindentity** <br>
ao concluir a instalação execute o comando:<br>
**php artisan vendor:publish**<br>
e escolha a opção que contem o namespace **Doctrine\BindEntity** para publicar o arquivo de configuração entityregister.php

## Como usar?

no arquivo de configuração set as entidades que deseja mapear durante a request e qual parametro da sua rota representa a chave primaria no banco de dados
```php
<?php

[
   entity =>[
      "produto" =>"Namespace da entidade",
      "parameterRouterEntityId" => "nome do parametro que represeta o id na rota para que o bind sabia buscar por este parametro"
   ]
];

```
<br>
Depois registe o namespace **Doctrine\BindEntity\BindEntityMiddleware::class** na lista de middlewares do laravel

## Controller

No controller implemente o atributo **entityBind** que será responsavel por informar ao middleware qual entidade mapear de acordo a configuração 
e implemente um metodo getter para que o mesmo consiga acessar a propridade a implementação segue como no exemplo abaixo: <br>

```php

class ExemploController extends Controller
{
    //propriedade para indicar ao middleware qual entidade procurar
    protected $entityBind = "vale_transporte";
    
    //Suas actions 
    
    //Metodo getter para o middleware acessar 
    public function getEntityBind(){
        return $this->entityBind;
    }
}
```

