# encoding: utf-8
from bearFunctions import *
import serial
import sys

tipo_envio = sys.argv[1]
codigo = sys.argv[2].decode("hex")
orden = sys.argv[3].decode("hex")
cadena_envio = codigo + orden

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=0.5)


if (tipo_envio == 'enviar'):
	if (len(sys.argv) == 5):
		switch = sys.argv[4].decode("hex")
		cadena_envio += switch
	enviarArduino(ser, cadena_envio)
elif (tipo_envio == 'recibir'):
	num_bytes = int(sys.argv[4])
	estado = recibirArduino(ser, cadena_envio, num_bytes)
	print estado
elif (tipo_envio == 'grabar'):
	part3 = sys.argv[4].decode("hex")
	part4 = sys.argv[5].decode("hex")
	part5 = sys.argv[6].decode("hex")
	part6 = sys.argv[7].decode("hex")
	cadena_envio += part3 + part4 + part5 + part6
	enviarArduino(ser, cadena_envio)

ser.close()
