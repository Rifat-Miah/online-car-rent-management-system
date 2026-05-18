<?php
require_once 'models/carModel.php';

$cars = getAllAvailableCars();
require_once 'views/carList.php';
?>