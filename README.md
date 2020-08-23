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
e escolha a opção que contem o namespace **Doctrine\BindEntity** para publicar o arquivo de configuração bindentityconfig.php

## Como usar?

no arquivo de configuração set as entidades que deseja mapear durante a request e qual parametro da sua rota representa a chave primaria no banco de dados
```php
<?php

[
   entity =>[
       "produto" => [
            "namespace" => \App\Modules\Produtos\Domain\Model\Produto::class,
            "parameterRouterEntityId" => "" // nome do parametro em rota em que é passado o id(EX: id_produto)
        ],
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
    //propriedade para indicar ao middleware qual entidade procurar(tem que esta configurado no arquivo bindentityconfig)
    //agora o middleware sabe que é uma entidade produto e irá buscar as configurações pelo valor definido abaixo
    protected $entityBind = "produto";
    
    //Suas actions 
    
    //Metodo getter para o middleware acessar o nome da entidade a instanciar
    public function getEntityBind(){
        return $this->entityBind;
    }
}
```
## Requisições

- GET(sem parametro):
  Ao efetuar um listar completo o processador de entidades nao interfere na requisição e portanto não instancia entidades.
  
- GET(com parametro):
  Ao informar um ID para fazer uma busca o o processador de entidades faz o processo de busca no banco de dados de acordo a entidade registrada no arquivo de configuração e informada no controller no parametro $entityBind;

- POST: 
Deve ser passado como parametro para cadastro todos os parametros do construtor da entidade afim de de que o mesmo seja construido caso um dos parametros seja outra entidade deve ser passar o nome registrado no arquivo de configuração seguido do seu ID para que a entidade seja buscada e instanciada dentro do objeto principal ex:

```
 {
    "empresa":"111-1111-1111-11",
    "nome_produto":"teste"
 }
```
Se "empresa" for uma entidade ela deve ser registrada no arquivo de configuração para que seja possivel instanciar o seu objeto correspondente

- PUT: ao informar o ID na url é feito a busca automática da entidade no banco de dados e injetada na rota para que seja atualizada

- DELETE: segue o mesmo conceito do PUT ao passar o ID busca no banco de dados a entidade a ser deletada e efetua a injeção na rota.

## Considerações

Abaixo uma lista de considerações sobre o projeto:

- Quando entidades são instanciadas de forma automatica elas retornam a concreta e não suas abstratas(caso haja) então em alguns se flexibilidade forçando a criação de varios endpoints para tratar os varios tipos EX: se vc tiver um cadastro de beneficios Tipo Transporte,Alimentação você terá que criar um para cada caso.

- O arquivo de configuração **bindentityconfig.php** é necessario para que você tenha controle do que vai ser instânciado ou não durante a requisição, deixando o controle de forma configuravel é garantido que somente as entidades necessarias serão instâciadas pelo laravel, mantendo a performace da aplicação.

 

