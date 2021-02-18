<?php
/** @var \App\ACS\Logic\Script\Functions $func */
$func->setParameter('A.B.C.D', 'test', 'RWS', 'xsd:string');
return $func->getParameterValue('A.B.C.D');
