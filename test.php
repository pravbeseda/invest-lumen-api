#!/usr/bin/php
<?php

$date = new \DateTime();

$date->setTime(23,59,59);
echo $date->format('Y-m-d H:i:s')."\n";

$date->modify('last day of this month');
echo $date->format('Y-m-d H:i:s')."\n";

$date->modify('last day of December this year');
echo $date->format('Y-m-d H:i:s')."\n";
