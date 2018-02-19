import os
import sys
import time
import string
import serial
#from jose.per import *

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)

ord=chr(int(("0x" + sys.argv[1]),0)) + chr(0x6A)
#ord3=chr(ord)+chr(0x6A)
#print ord
#datos=recibir(ord)
cont=0
time.sleep(0.15)
ser.setRTS(True)
time.sleep(0.03)
ser.write(ord)
time.sleep(0.03)
ser.flushInput()
ser.setRTS(False)
state=ser.read(8)
time.sleep(0.1)

#print state.encode('hex')

if len(state.encode('hex')) == 16:
	while ((str(state.encode('hex'))[len(state.encode('hex'))-16]) != '1' and (str(state.encode('hex'))[len(state.encode('hex'))-15]) != 'b' and cont!=8):
        	cont=cont+1
		time.sleep(0.15)
	        ser.setRTS(True)
       		time.sleep(0.03)
       		ser.write(ord)
       		time.sleep(0.03)
       		ser.flushInput()
       		ser.setRTS(False)
       		state=ser.read(8)
       		time.sleep(0.1)
#		print state.encode('hex')


	mitad1 = (str(state.encode('hex'))[len(state.encode('hex'))-12])
	mitad2 = (str(state.encode('hex'))[len(state.encode('hex'))-11])
	mitad3 = (str(state.encode('hex'))[len(state.encode('hex'))-10])
	mitad4 = (str(state.encode('hex'))[len(state.encode('hex'))-9])
	mitad5 = (str(state.encode('hex'))[len(state.encode('hex'))-8])
	mitad6 = (str(state.encode('hex'))[len(state.encode('hex'))-7])
	mitad7 = (str(state.encode('hex'))[len(state.encode('hex'))-6])
	mitad8 = (str(state.encode('hex'))[len(state.encode('hex'))-5])

	dato1 = int((mitad1 + mitad2),16)
	dato2 = int((mitad3 + mitad4),16)
	dato3 = int((mitad5 + mitad6),16)
	dato4 = int((mitad7 + mitad8),16)

print dato1
print dato2
print dato3
print dato4
