<?php

$isEnable = $_GET['isEnable'];
$command = "http://192.168.1.18:88/cgi-bin/CGIProxy.fcgi?cmd=setMotionDetectConfig&usr=test&pwd=test12345&isEnable=$isEnable&linkage=136&sensitivity=2&schedule0=281474976710655&schedule1=281474976710655&schedule2=281474976710655&schedule3=281474976710655&schedule4=281474976710655&schedule5=281474976710655&schedule6=281474976710655&area0=1023&area1=1023&area2=1023&area3=1023&area4=1023&area5=1023&area6=1023&area7=1023&area8=1023&area9=1023";

system('curl "' . $command . '"', $retval);
