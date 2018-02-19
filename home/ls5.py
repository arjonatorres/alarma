import os
import sys
import xmpp
import serial
import time
import string
import RPi.GPIO as GPIO

#Configuracion GPIO y RTS
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
RTS =18
GPIO.setup(RTS,GPIO.OUT)
GPIO.output(RTS,False)

#Orden puerto serie
ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)

#Funcion recibir datos
def recibir():
	GPIO.output(RTS,True)
	time.sleep(0.03)
	ser.write("\x18\x63")
	time.sleep(1)
	ser.write("\x18\x6A")
	time.sleep(0.03)
	GPIO.output(RTS,False)
	ser.flushInput()
	state=ser.read(8)
	if len(state.encode('hex')) == 16:
		if ((str(state.encode('hex'))[len(state.encode('hex'))-16]) == '1' and (str(state.encode('hex'))[len (state.encode('hex'))-15]) == 'b'):
			dato1 = (str(state.encode('hex'))[len(state.encode('hex'))-2])
			dato2 = (str(state.encode('hex'))[len(state.encode('hex'))-1])
			dato = dato1 + dato2
		else:
			dato = "Error"
		return dato
	else:
		dato = "Error"
		return dato

#Principal
estado = recibir()
if estado == "01":
	print ("Luz distribuidor dorm. encendida")
elif estado == "00":
	print ("Luz distribuidor dorm. apagada")
else:
	print ("Error")
