import os
import sys
import xmpp
import serial
import time
import string

#Orden puerto serie
ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)

#Funcion recibir datos
time.sleep(0.15)
ser.setRTS(True)
time.sleep(0.03)
ser.write("\x14\x63")
time.sleep(0.1)

