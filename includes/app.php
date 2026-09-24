<?php

require 'funciones.php';
require 'config/environment.php';
require 'config/database.php';
require __DIR__ . "/../vendor/autoload.php";

// conectar a la BD

$db = conectarDB();

use Model\ActiveRecord;

ActiveRecord::setDB($db);
