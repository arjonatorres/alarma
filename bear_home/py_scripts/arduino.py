# encoding: utf-8
import serial
import time
import sys

tipo_envio = sys.argv[1]
codigo = sys.argv[2].decode("hex")
orden = sys.argv[3].decode("hex")
cadena_envio = codigo + orden

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=0.5)

def enviarArduino(cadena_envio):
	time.sleep(0.05)
	#ser.setRTS(True)
	time.sleep(0.03)
	ser.write(cadena_envio)
	time.sleep(0.03)
	#ser.flushInput()
	#ser.setRTS(False)

def recibirArduino(cadena_envio):
	time.sleep(0.05)
	#ser.setRTS(True)
	time.sleep(0.03)
	ser.write(cadena_envio)
	time.sleep(0.03)
	#ser.flushInput()
	#ser.setRTS(False)
	state=ser.read(5)
	#time.sleep(0.03)
	print state.encode('hex')

if (tipo_envio == 'enviar'):
	tipo_act = sys.argv[4]
	enviarArduino(cadena_envio)
elif (tipo_envio == 'recibir'):
	recibirArduino(cadena_envio)
#state=ser.read(4)
#time.sleep(0.03)
#print state.encode('hex')

ser.close()
