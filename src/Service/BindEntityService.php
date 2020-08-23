<?php


namespace Doctrine\BindEntity\Service;
use Illuminate\Http\Request;
use LaravelDoctrine\ORM\Facades\EntityManager;

class BindEntityService
{

    private array $entity;

    public function __construct()
    {
        $this->entity = config("entityregister.entity");
    }

    /**
     * @param Request $request
     */
    public function bindEntityDoctrine(Request $request){
        $this->InstanceEntityPostOrPut($request);
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    private function InstanceEntityPostOrPut(Request $request): void
    {
        switch ($request->method()) {
            case "POST":
                $this->postEntityInstance($request);
                break;
            case "PUT":
                $this->putEntityInstance($request);
                break;
        }
    }

    private function postEntityInstance(Request $request){

        $bodyRequestKeys = array_keys($request->all());
        $entity = $bodyRequestKeys[0];

        $this->checkIfExistsEntityConfigurationFile($bodyRequestKeys, $entity);

        $constructParameter = $this->getArrayConstructorAttribute($request->all()[$entity]);
        $namespace = $this->entity[$entity];

        app()->bind($namespace,function() use($constructParameter,$namespace){
            $reflectionClass = new \ReflectionClass($namespace);
            return $reflectionClass->newInstanceArgs($constructParameter);
        });

    }

    private function putEntityInstance(Request $request){

        $bodyRequestKeys = array_keys($request->all());
        $entity = $bodyRequestKeys[0];

        $this->checkIfExistsEntityConfigurationFile($bodyRequestKeys, $entity);

        $idEntity = $request->segment(2);

         app()->bind($this->entity[$entity],function() use($entity,$idEntity){
             return EntityManager::getRepository($this->entity[$entity])->find($idEntity);
         });

    }

    private function getArrayConstructorAttribute($bodyRequest)
    {
        $attributes = [];
        foreach($bodyRequest as $key =>$value){
            $entity = $this->getEntityById($key,$value);
            $attributes[] = $entity;
        }
        return $attributes;
    }

    private function getEntityById($key,$value){
        if(array_key_exists($key,$this->entity)){
            return EntityManager::getRepository($this->entity[$key])->find($value);
        }
        return $value;
    }

    /**
     * @param array $bodyRequestKeys
     * @param $entity
     * @throws \Exception
     */
    private function checkIfExistsEntityConfigurationFile(array $bodyRequestKeys, $entity): void
    {
        if(count($this->entity)){
            throw new \Exception("Failed to process request: There are no entities set in the configuration file");
        }

        if (count($bodyRequestKeys) > 1 || !key_exists($entity, $this->entity)) {
            throw new \Exception("Failed to process request: Doctrine entity not found in configuration file");
        }
    }

}
