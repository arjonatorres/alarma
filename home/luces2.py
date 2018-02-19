#! /usr/bin/python
import xmpp
import serial
import string
import time

p1="\x14\x6A"

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)


time.sleep(0.2)
ser.setRTS(True)
time.sleep(0.04)
ser.write(p1)
time.sleep(0.04)
ser.flushInput()
ser.setRTS(False)
state=ser.read(8)
print state.encode('hex')
