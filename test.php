#!/usr/bin/php
<?php

$url = 'https://iss.moex.com/iss/securities/VTBE.xml?iss.meta=off&iss.only=boards&boards.columns=secid,is_primary,boardid';
$xml = simplexml_load_file($url);
$res = (string) $xml->xpath('/document/data/rows/row[@is_primary="1"]')[0]->attributes()->boardid;

echo $res;
