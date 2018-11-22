<?php

namespace HandlerPhoenixRb;

class ModelWriter
{
    protected $modelName;

    protected $namespace;

    protected $extendClass;

    public function writeObjectGetterAndSetterMethods($name)
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

    public function writeVariableDefinitions($key, $value)
    {
        $type = gettype($value);

        return
            PHP_EOL.'
    /**
     *@var null|' . $type . '
     */
     protected $' . $key . ' = null;';
    }

    public function writeGetterAndSetterMethods($name)
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

        return '<?php' . PHP_EOL . PHP_EOL . 'namespace ' . $this->namespace . $currentPath . ';' . PHP_EOL . PHP_EOL;
    }

    public function writeClassStart($modelName)
    {
        $extend = $this->getExtendClass() ? ' extends '. $this->getExtendClass() : null;

        return PHP_EOL . PHP_EOL . 'class ' . ucfirst($this->modelName) . $extend . $this->getExtensionClass() . PHP_EOL . '{';
    }

    public function writeClassFooter()
    {
        return PHP_EOL . '}' . PHP_EOL;
    }


}
