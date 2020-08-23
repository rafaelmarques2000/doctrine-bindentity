<?php

namespace Doctrine\BindEntity\Helper;
use Illuminate\Http\Request;
use LaravelDoctrine\ORM\Facades\EntityManager;

class BindEntityHelper
{
    private array $entity;

    public function __construct(){
        $this->entity = config("bindentityconfig.entity");
    }

    public function getArrayConstructorAttribute($bodyRequest)
    {
        $attributes = [];
        foreach($bodyRequest as $key =>$value){
            $entity = $this->getEntityById($key,$value);
            $attributes[] = $entity;
        }
        return $attributes;
    }

    public function getEntityById($key, $value)
    {
        if(array_key_exists($key, $this->entity)){
            return EntityManager::getRepository($this->entity[$key]['namespace'])->find($value);
        }
        return $value;
    }

    /**
     * @param $entity
     * @throws \Exception
     */
    public function checkIfExistsEntityConfigurationFile($entity): void
    {
        if(count($this->entity) === 0){
            throw new \Exception("Failed to process request: There are no entities set in the configuration file");
        }

        if (!key_exists($entity, $this->entity)) {
            throw new \Exception("Failed to process request: Doctrine entity not found in configuration file");
        }
    }

    /**
     * @param $routerParameterId
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getRouterParameterByConfig($routerParameterId, Request $request): string
    {
        if ($routerParameterId != "") {
            $routerParameters = $request->route()->parameters();
            if (!key_exists($routerParameterId, $routerParameters)) {
                throw new \Exception("Parameter not found in router");
            }
            $idEntity = $routerParameters[$routerParameterId];
        }
        return $idEntity;
    }
}
