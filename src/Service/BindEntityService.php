<?php


namespace Doctrine\BindEntity\Service;
use Doctrine\BindEntity\Helper\BindEntityHelper;
use Illuminate\Http\Request;
use LaravelDoctrine\ORM\Facades\EntityManager;

class BindEntityService
{

    private array $entity;
    private string $entityBind;
    private string $namespace;
    private string $parameterRouterEntityId;
    private BindEntityHelper $bindEntityHelper;

    public function __construct(BindEntityHelper $bindEntityHelper)
    {
        $this->entity = config("bindentityconfig.entity");
        $this->bindEntityHelper = $bindEntityHelper;
    }

    /**
     * @param Request $request
     */
    public function bindEntityDoctrine(Request $request){

        $this->entityBind = $request->route()->getController()->getEntityBind();
        $this->namespace = $this->entity[$this->entityBind]['namespace'];
        $this->parameterRouterEntityId = $this->entity[$this->entityBind]['parameterRouterEntityId'];

        $this->InstanceEntityHttpRequest($request);
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    private function InstanceEntityHttpRequest(Request $request): void
    {
        switch ($request->method()) {
            case "POST":
                $this->postEntityInstance($request);
                break;
            case "PUT":
                $this->putEntityInstance($request);
                break;
            case "DELETE":
                $this->getDeleteEntityInstance($request);
                break;
            case "GET":
                $this->getGetEntityInstance($request);
                break;
        }
    }

    private function getGetEntityInstance(Request $request)
    {
        $routeParameters = $request->route()->parameters();
        if(count($routeParameters) > 0){
            $idEntity = $this->bindEntityHelper->getRouterParameterByConfig($this->parameterRouterEntityId, $request);
            app()->bind($this->namespace,function() use($idEntity){
                return EntityManager::getRepository($this->namespace)->find($idEntity);
            });
        }
    }

    private function postEntityInstance(Request $request)
    {
        $this->bindEntityHelper->checkIfExistsEntityConfigurationFile($this->entityBind);
        $constructParameter = $this->bindEntityHelper->getArrayConstructorAttribute($request->all());
        app()->bind($this->namespace, function() use($constructParameter){
            $reflectionClass = new \ReflectionClass($this->namespace);
            return $reflectionClass->newInstanceArgs($constructParameter);
        });
    }

    private function putEntityInstance(Request $request)
    {
        $this->bindEntityHelper->checkIfExistsEntityConfigurationFile($this->entityBind);
        $idEntity = $this->bindEntityHelper->getRouterParameterByConfig($this->parameterRouterEntityId, $request);
        app()->bind($this->namespace,function() use($idEntity){
             return EntityManager::getRepository($this->namespace)->find($idEntity);
         });
    }

    private function getDeleteEntityInstance(Request $request)
    {
        $this->bindEntityHelper->checkIfExistsEntityConfigurationFile($this->entityBind);
        $idEntity = $this->bindEntityHelper->getRouterParameterByConfig($this->parameterRouterEntityId, $request);
        app()->bind($this->namespace,function() use($idEntity){
            return EntityManager::getRepository($this->namespace)->find($idEntity);
        });
    }

}
