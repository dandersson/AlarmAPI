<?php

require('autoload.php');

$alarm = new AlarmAPI\Alarm();

$fields = isset($_GET['fields']) ? $_GET['fields'] : [];

AlarmAPI\API::sendJSONResponse($alarm->getCurrentStatus($fields));
