<?php

namespace HandlerPhoenixRb;

class ModelCreator
{
    /**
     * @var
     */
    protected $classNamespacePrefix;

    /**
     * @var
     */
    protected $extensionClass;

    /**
     * @var
     */
    protected $useDefinition = '';

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var int
     */
    protected $level = 0;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $modelArray;

    /**
     * @var bool
     */
    protected $overWrite;

    /**
     * @param array $modelArray
     * @param       $modelName
     * @param null  $currentPath
     * @throws \Exception
     */
    public function createModel(array $modelArray, $modelName, $currentPath = null)
    {

        if (!$this->directory){
            throw new \Exception('Error: Directory not set or null');
        }

        if ($this->directory && !$currentPath) {
            $directory = $this->directory . '/' . $modelName;
        } elseif ($this->directory && $currentPath) {
            $directory = $this->directory . '/' . str_replace('\\', '/' ,$currentPath);
        }

        if (strstr(strtolower($modelName),'tags')){
            echo PHP_EOL. $currentPath. PHP_EOL;
        }

        $this->createDirectory($directory);
        $currentPath = ($currentPath) ?: $modelName;
        $variableDefinitions = '';
        $methodDefinitions = '';
        $useDefinition = '';

        foreach ($modelArray as $key => $value) {
            if (!is_array($value)) {
                $variableDefinitions .= $this->writeVariableDefinitions($key, $value);
                $methodDefinitions .= $this->writeGetterAndSetterMethods($key);
            } else {

                if (isset($value[0])) {
                    //Get first array within not associative array and us as object definition
                    $value = $value[0];
                }

                //If first array element is not associative then ignore and move one to next
                if (is_array($value) && !isset($value[0])) {
                    echo 'Creation getter and setter for key ' . $key . PHP_EOL;
                    $newDirectory = $directory . '/' . ucfirst($modelName);
                    $newPath = $currentPath . '\\' . ucfirst($modelName);
                    $classNameSpace = $this->classNamespacePrefix . '\\' . $newPath . '\\' . ucfirst($key);
                    $newName = ucfirst($key);

                    echo '
                    Name:           ' . $newName . '
                    Directory:      ' . $newDirectory . '
                    Path:           ' . $newPath . '
                    NextClassPath:  ' . $classNameSpace . '
                    ' . PHP_EOL;

                    $useDefinition = $this->appendUseDefinition($classNameSpace, $useDefinition);
                    $variableDefinitions .= $this->writeVariableDefinitions($key, $value);
                    $methodDefinitions .= $this->writeObjectGetterAndSetterMethods($key);
                    $this->createModel($value, $newName, $newPath);
                }
            }
        }

        $useDefinition = $this->useDefinition. $useDefinition;
        $fileInput = $this->getFileInput($modelName, $variableDefinitions, $methodDefinitions, $useDefinition, $currentPath);
        $fileName = $directory . '/' . $modelName . '.php';
        $this->writeModel($fileName, $fileInput);
    }

    protected function writeModel($fileName, $fileInput)
    {
        if ((file_exists($fileName) && $this->getOverWrite()) || !file_exists($fileName)) {
            echo 'Creating Model ' . $fileName . PHP_EOL;
            file_put_contents($fileName, $fileInput);
        } else {
            echo 'Model already exists and OverWrite is set to false';
        }
    }

    /**
     * @param $directory
     * @return mixed
     */
    public function createDirectory($directory)
    {
        if (!file_exists($directory)) {
            echo 'Creating directory: ' . $directory . PHP_EOL;
            mkdir($directory);
            echo 'Directory created: ' . $directory . PHP_EOL . PHP_EOL;
        }

        return $directory;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     * @return ModelCreator
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassNamespacePrefix()
    {
        return $this->classNamespacePrefix;
    }

    /**
     * @param mixed $classNamespacePrefix
     * @return ModelCreator
     */
    public function setClassNamespacePrefix($classNamespacePrefix)
    {
        $this->classNamespacePrefix = $classNamespacePrefix;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtensionClass()
    {
        return $this->extensionClass;
    }

    /**
     * @param $extensionClass
     * @return $this
     */
    public function setExtensionClass($extensionClass)
    {
        $this->extensionClass = $extensionClass;

        $this->setUseDefinition($this->appendUseDefinition($extensionClass));

        return $this;
    }

    /**
     * @return bool
     */
    public function getOverWrite()
    {
        return $this->overWrite;
    }

    /**
     * @param bool $overWrite
     * @return ModelCreator
     */
    public function setOverWrite($overWrite)
    {
        $this->overWrite = $overWrite;

        return $this;
    }

    protected function getFileInput($modelName, $variableDefinitions, $methodDefinitions, $useDefinitions, $path)
    {
        return $this->writeClassHeader($path) .
            $useDefinitions .
            $this->writeClassStart($modelName) .
            $variableDefinitions .
            $methodDefinitions .
            $this->writeClassFooter();
    }

    public function validateName($name)
    {
        if (!$name || !$this->coreMockery->$name()) {
            throw new \Exception('Error: model name either not defined or invalid');
        }
        $this->modelName = $name;
    }

    protected function getVariableDefinitions()
    {
        return $this->variableDefinitions;
    }

    protected function getMethodDefinitions()
    {
        return $this->methodDefinitions;
    }

    public function getUseDefinition()
    {
        return $this->useDefinition;
    }

    public function setUseDefinition($useDefinition)
    {
        $this->useDefinition = $useDefinition;

        return $this;
    }

    public function appendUseDefinition($class, $useDefinition = '')
    {
        $useDefinition .= 'use ' . $class . ';' . PHP_EOL;

        return $useDefinition;
    }

    function writeObjectGetterAndSetterMethods($name)
    {

        $formattedName = ucfirst($name);

        return PHP_EOL . '
    public function get' . $formattedName . '()
    {
        return $this->' . $name . ';
    }
    
    public function set' . $formattedName . '($' . $name . ')
    {
        $this->' . $name . ' = new ' . $formattedName . '($' . $name . ', $this);
        
        return $this;
    }';
    }

    function writeVariableDefinitions($key, $value)
    {
        $type = gettype($value);

        return
        PHP_EOL.'
    /**
     *@var null|' . $type . '
     */
     protected $' . $key . ' = null;';
    }

    function writeGetterAndSetterMethods($name)
    {
        return PHP_EOL .
        '
    public function get' . ucfirst($name) . '()
    {
        return $this->' . $name . ';
    }
        
    public function set' . ucfirst($name) . '($' . $name . ')
    {
        $this->' . $name . ' = $' . $name . ';
        
        return $this;
    }';
    }

    public function writeClassHeader($currentPath = null)
    {
        if ($currentPath){
            $currentPath = '\\'.$currentPath;
        }

        return '<?php' . PHP_EOL . PHP_EOL . 'namespace ' . $this->classNamespacePrefix . $currentPath . ';' . PHP_EOL . PHP_EOL;
    }

    protected function writeClassStart($modelName)
    {
        $extend = $this->getExtensionClass() ? ' extends ' : null;

        return PHP_EOL . PHP_EOL . 'class ' . ucfirst($modelName) . $extend . $this->getClassNameFromNamespace($this->getExtensionClass()) . PHP_EOL . '{';
    }

    protected function getClassNameFromNamespace($class)
    {
        $class = explode('\\', $class);

        if (!empty($class)) {
            return array_pop($class);
        }
    }

    public function writeClassFooter()
    {
        return PHP_EOL . '}' . PHP_EOL;
    }
}
