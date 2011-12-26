<?php
include_once("faiclass.php");

$faiclass = new FaiClass();

$faiclass->setConfigSpaceDir('/srv/fai/config/generic/');
print "Config Space dir  is : ". $faiclass->getConfigSpaceDir() .'<br />';

$faiclass->parseClass('TEST');
print "Parse class : ". $faiclass->getClass() .'<br />';

print "Parsed class:";
print nl2br(print_r($faiclass->classDatas, true));

?>
