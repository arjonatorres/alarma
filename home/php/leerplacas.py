#!/bin/bash
import time

global estadot
time.sleep(0.2)
estado = open('/home/pi/estado_placas.txt','r')
estadot = estado.read(1)
estado.close()
print (estadot)
