#! /usr/bin/python
import xmpp
import serial
import string
import time

p1="\x14\x6A"
p2="\x15\x6A"
p3="\x16\x6A"
p4="\x17\x6A"
p5="\x18\x6A"
p6="\x19\x6A"
ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)

#Funcion recibir datos
def recibir(nh):
	dato="-1"
	while (dato!='00' and dato!='01'):
		time.sleep(0.2)
        	ser.setRTS(True)
        	time.sleep(0.04)
        	ser.write(nh)
        	time.sleep(0.04)
		ser.flushInput()
        	ser.setRTS(False)
		state=ser.read(8)
#		print state.encode('hex')
		if len(state.encode('hex')) == 16:

			if ((str(state.encode('hex'))[len(state.encode('hex'))-16]) == '1' and (str(state.encode('hex'))[len(state.encode('hex'))-15]) == 'b'):
				dato1 = (str(state.encode('hex'))[len(state.encode('hex'))-2])
				dato2 = (str(state.encode('hex'))[len(state.encode('hex'))-1])
				dato = dato1 + dato2
	print dato
	return dato

#Principal

persiana1 = recibir(p1)
if (persiana1 != "Error") and (persiana1 != "En movimiento"):
	if persiana1 == "00":
		persiana1 = "Apagado"
	if persiana1 == "01":
		persiana1 = "Encendido"

persiana2 = recibir(p2)
if (persiana2 != "Error") and (persiana2 != "En movimiento"):
        if persiana2 == "00":
                persiana2 = "Apagado"
        if persiana2 == "01":
                persiana2 = "Encendido"

persiana3 = recibir(p3)
if (persiana3 != "Error") and (persiana3 != "En movimiento"):
        if persiana3 == "00":
                persiana3 = "Apagado"
        if persiana3 == "01":
                persiana3 = "Encendido"

persiana4 = recibir(p4)
if (persiana4 != "error") and (persiana4 != "En movimiento"):
        if persiana4 == "00":
                persiana4 = "Apagado"
        if persiana4 == "01":
                persiana4 = "Encendido"

persiana5 = recibir(p5)
if (persiana5 != "error") and (persiana5 != "En movimiento"):
        if persiana5 == "00":
                persiana5 = "Apagado"
        if persiana5 == "01":
                persiana5 = "Encendido"

persiana6 = recibir(p6)
if (persiana6 != "error") and (persiana6 != "En movimiento"):
        if persiana6 == "00":
                persiana6 = "Apagado"
        if persiana6 == "01":
                persiana6 = "Encendido"

print ("1 - Salon        = " + persiana1 +
"\n2 - C.Ordenador      = " + persiana2 +
"\n3 - Dormitorio Matr. = " + persiana3 +
"\n4 - Bano Matr.   = " + persiana4 +
"\n5 - Dormitorio Der.    = " + persiana5 +
"\n6 - Dormitorio Izq.    = " + persiana6)
