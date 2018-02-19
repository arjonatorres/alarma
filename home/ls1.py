import os
import sys
import xmpp
import serial
import time
import string
import pws

#Orden puerto serie
ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)

#Funcion recibir datos
def recibir():
	time.sleep(0.15)
	ser.setRTS(True)
	time.sleep(0.03)
	ser.write("\x14\x63")
        time.sleep(1)
	ser.write("\x14\x6A")
	time.sleep(0.03)
	ser.flushInput()
	ser.setRTS(False)
	state=ser.read(8)
#	print state.encode('hex')
	if len(state.encode('hex')) == 16:
		if ((str(state.encode('hex'))[len(state.encode('hex'))-16]) == '1' and (str(state.encode('hex'))[len (state.encode('hex'))-15]) == 'b'):
			dato1 = (str(state.encode('hex'))[len(state.encode('hex'))-2])
			dato2 = (str(state.encode('hex'))[len(state.encode('hex'))-1])
			dato = dato1 + dato2
		else:
			dato = "Error"
		return dato
	else:
		dato = "En movimiento"
		return dato
	ser.close()

#Principal
estado = recibir()
if estado == "01":
	print ("Luz salon encendida")
elif estado == "00":
	print ("Luz salon apagada")
else:
	print ("Error")
