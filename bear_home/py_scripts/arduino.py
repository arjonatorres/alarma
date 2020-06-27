# encoding: utf-8
from BearFunctions import *
import serial
import sys

tipo_envio = sys.argv[1]
codigo = sys.argv[2].decode("hex")
orden = sys.argv[3].decode("hex")
cadena_envio = codigo + orden

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=0.5)


if (tipo_envio == 'enviar'):
	enviarArduino(ser, cadena_envio)
elif (tipo_envio == 'recibir'):
	num_bytes = int(sys.argv[4])
	estado = recibirArduino(ser, cadena_envio, num_bytes)
	print estado

#state=ser.read(4)
#time.sleep(0.03)
#print state.encode('hex')

ser.close()
