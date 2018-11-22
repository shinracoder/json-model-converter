<?php

namespace HandlerPhoenix;

use HandlerPhoenix\Hydration\AbstractEntity;
use HandlerPhoenixRb\ModelCreator;

class ModelGenerator
{
    const CLASS_PREFIX = 'DashCore\Service\Air\Hydration';

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var array
     */
    protected $modelArray;

    /**
     * @var string
     */
    protected $modelFilePath;

    /**
     * @var string
     */
    protected $destinationDirectory = null;

    protected $overWrite;

    /**
     * @var ModelCreator
     */
    protected $modelCreator;

    public function __construct(
        ModelCreator $modelCreator
    ) {
        $this->modelCreator = $modelCreator;
    }

    public function generateModel()
    {
        if ($this->modelName && $this->getModelArray()){
            $this->modelCreator->setClassNamespacePrefix(self::CLASS_PREFIX);
            $this->modelCreator->setOverWrite($this->getOverWrite());
            $this->modelCreator->setExtensionClass(\DashCore\Service\Air\Hydration\AbstractEntity::class);
            $this->modelCreator->setDirectory($this->getDestinationDirectory());
            $this->modelCreator->createModel($this->getModelArray(), $this->getModelName());
        }
    }

    public function validateName($modelName)
    {
        if (!$modelName || !ctype_alnum($modelName)) {
            throw new \Exception('Error: model name either not defined or is invalid');
        }

        return true;
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * @param $modelName
     * @return $this
     */
    public function setModelName($modelName)
    {
        if ($this->validateName($modelName)) {
            $this->modelName = $modelName;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getModelArray()
    {
        return $this->modelArray;
    }

    /**
     * @param $modelArray
     * @return $this
     */
    public function setModelArray($modelArray)
    {
        $this->modelArray = $modelArray;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelFilePath()
    {
        return $this->modelFilePath;
    }

    /**
     * @param $modelFilePath
     * @return $this
     */
    public function setModelFilePath($modelFilePath)
    {
        $this->modelFilePath = $modelFilePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationDirectory()
    {
        return $this->destinationDirectory;
    }

    /**
     * @param string $destinationDirectory
     * @return ModelGenerator
     */
    public function setDestinationDirectory($destinationDirectory)
    {
        $this->destinationDirectory = $destinationDirectory;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOverWrite()
    {
        return $this->overWrite;
    }

    /**
     * @param mixed $overWrite
     * @return ModelGenerator
     */
    public function setOverWrite($overWrite)
    {
        $this->overWrite = $overWrite;

        return $this;
    }
}
