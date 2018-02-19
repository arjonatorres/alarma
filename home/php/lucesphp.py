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
	cont=0
	while (dato!='00' and dato!='01' and cont!=5):
		cont=cont+1
		time.sleep(0.2)
                ser.setRTS(True)
                time.sleep(0.04)
                ser.write(nh)
                time.sleep(0.04)
                ser.flushInput()
                ser.setRTS(False)
                state=ser.read(8)
#               print state.encode('hex')
                if len(state.encode('hex')) == 16:
                        if ((str(state.encode('hex'))[len(state.encode('hex'))-16]) == '1' and (str(state.encode('hex'))[len(state.encode('hex'))-15]) == 'b'):
                                dato1 = (str(state.encode('hex'))[len(state.encode('hex'))-2])
                                dato2 = (str(state.encode('hex'))[len(state.encode('hex'))-1])
                                dato = dato1 + dato2
	return dato

#Principal

persiana1 = recibir(p1)
persiana2 = recibir(p2)
persiana3 = recibir(p3)
persiana4 = recibir(p4)
persiana5 = recibir(p5)

print (persiana1)
print (persiana2)
print (persiana3)
print (persiana4)
print (persiana5)


