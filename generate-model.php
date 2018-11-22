<?php

$execute = false;

include getcwd()."/../../autoload.php";


use HandlerPhoenix\ModelGenerator;
use HandlerPhoenixRb\ModelCreator;



if (!empty($argv[1]) && !empty($argv[2])) {

    $jsonModelFile = $argv[2];
    $modelName = $argv[1];
    $overWrite = isset($argv[3]) ? (int) $argv[3] : false;
    $destinationDirectory = !empty($argv[4]) ? $argv[4] : null;

    if (file_exists($jsonModelFile)) {
        $modelArray = json_decode(file_get_contents($jsonModelFile), true);
    } else {
        throw new Exception('Json Model file does not exist');
    }


    $modelGenerator = new ModelGenerator(new ModelCreator());
    $modelGenerator->setModelArray($modelArray);
    $modelGenerator->setModelName($modelName);

    if (is_dir($destinationDirectory)) {
        $modelGenerator->setDestinationDirectory($destinationDirectory);
    } else {
        throw new Exception('Destination directory dosn\'t exist');
    }

    $modelGenerator->setOverWrite((bool) $overWrite);

    $modelGenerator->generateModel();
} else {
    throw new InvalidArgumentException('Error: Model name and ModelJson file path must be set');
}
