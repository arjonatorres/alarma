#Alarma domestica por Jose Arjona

import xmpp
import time
import RPi.GPIO as GPIO
import serial
from smtplib import SMTP
import os
import subprocess
import pws

#Configuracion GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

#Configuracion serial
ser = serial.Serial('/dev/ttyAMA0', baudrate=115200, timeout=4)

#Sensores
lectura = 4
pir1 = 5 #Salon
pir2 = 6 #Cuarto ordenador
pir3 = 13 #Distribuidor arriba
puerta = 19
buzz = 24
sirena = 23
vol1 = 12
vol2 = 16
vol3 = 20
vol4 = 21
vol5 = 26

#Inicializacion de entradas y salidas GPIO
GPIO.cleanup()
GPIO.setup(lectura,GPIO.OUT)
GPIO.setup(pir1,GPIO.IN)
GPIO.setup(pir2,GPIO.IN)
GPIO.setup(pir3,GPIO.IN)
GPIO.setup(puerta,GPIO.IN)
GPIO.setup(buzz,GPIO.OUT)
GPIO.setup(sirena,GPIO.OUT)
GPIO.setup(vol1,GPIO.OUT)
GPIO.setup(vol2,GPIO.OUT)
GPIO.setup(vol3,GPIO.OUT)
GPIO.setup(vol4,GPIO.OUT)
GPIO.setup(vol5,GPIO.OUT)

#Funcion para ver si hay internet
def ping():
	ret = subprocess.call(['ping', '-c', '3', '-W', '5', 'google.es'],
	stdout=open('/dev/null', 'w'),
	stderr=open('/dev/null', 'w'))
	return ret == 0

#Funcion guardar log.txt
def log(motivo):
	archivo = open('/home/pi/log.txt','r')
	lectura = archivo.read()
	archivo.close()
	archivo = open('/home/pi/log.txt','w')
	archivo.write(motivo + ' - ' + time.ctime() + '\n')
	archivo.write(lectura)
	archivo.close()

#Funcion llamar Jose
def llamarjose():
	cmd="ATD637371009;\r"
#	cmd="ATD646131938;\r"
	ser.write(cmd.encode())

#Funcion llamar Ro
def llamarro():
	cmd="ATD636887511;\r"
	ser.write(cmd.encode())

#Funcion enviar gmail jose
def enviarmailjose(mensaje,stat):
	if stat:
		EnviarCorreo = SMTP()
		EnviarCorreo.connect("smtp.gmail.com", 587)
		EnviarCorreo.ehlo()
		EnviarCorreo.starttls()
		EnviarCorreo.ehlo()
		EnviarCorreo.login("arjonarasp@gmail.com",pws.mail)
		Cabecera = 'To:' + "arjonatorres79@gmail.com" + '\n'
		Cabecera += 'From:' + "arjonarasp@gmail.com" + '\n'
		Cabecera += 'Subject:' + "Alarma" + '\n' + '\n'
		CuerpoMensaje = mensaje + '\n' + '\n'
		EnviarCorreo.sendmail("arjonarasp@gmail.com","arjonatorres79@gmail.com", Cabecera + CuerpoMensaje)
		EnviarCorreo.close()

#Funcion enviar gmail ro
def enviarmailro(mensaje,stat):
	if stat:
        	EnviarCorreo = SMTP()
        	EnviarCorreo.connect("smtp.gmail.com", 587)
        	EnviarCorreo.ehlo()
        	EnviarCorreo.starttls()
        	EnviarCorreo.ehlo()
        	EnviarCorreo.login("arjonarasp@gmail.com",pws.mail)
        	Cabecera = 'To:' + "robdp79@gmail.com" + '\n'
        	Cabecera += 'From:' + "arjonarasp@gmail.com" + '\n'
        	Cabecera += 'Subject:' + "Alarma" + '\n' + '\n'
        	CuerpoMensaje = mensaje + '\n' + '\n'
        	EnviarCorreo.sendmail("arjonarasp@gmail.com","robdp79@gmail.com", Cabecera + CuerpoMensaje)
        	EnviarCorreo.close()

#Funcion enviar Hangouts
def enviar(texto,stat):
	if stat:
		jid = xmpp.JID('arjonarasp@gmail.com')
		cl=xmpp.Client(jid.getDomain(),debug=[])
#		cl.connect()
		cl.connect(server=('talk.google.com', 5223))
		cl.auth(jid.getNode(),pws.mail)
		cl.send(xmpp.protocol.Message("arjonatorres79@gmail.com",texto, typ='chat'))
        	cl.send(xmpp.protocol.Message("robdp79@gmail.com",texto, typ='chat'))
#		cl.send(xmpp.protocol.Message("arjonabarrones@gmail.com",texto, typ='chat'))
		cl.disconnect()


#Funcion leer estado_alarma.txt
def leer():
	global estadot
	estado = open('/home/pi/estado_alarma.txt','r')
        estadot = estado.read(1)
        estado.close()
#	print "leyendo"


#Funcion escribir estado_alarma.txt
def escribir(ne):
	note = open('/home/pi/estado_alarma.txt','w')
	note.write(ne)
	note.close()


#Funcion de buzz al desactivar la alarma
def buzzoff():
	global buzzt
	time.sleep(0.4)
	GPIO.output(sirena,False)
	GPIO.output(buzz,True)
	time.sleep(0.08)
        GPIO.output(buzz,False)
	time.sleep(0.04)
        GPIO.output(buzz,True)
	time.sleep(0.08)
        GPIO.output(buzz,False)
#	log("Alarma desconectada")
	buzzt = 1


#Funcion salto de alarma
def saltoalarma(ns):
	GPIO.output(buzz,True)
	GPIO.output(sirena,True)
	GPIO.output(vol1,True)
	llamarjose()
	if ping():
		xstatus = 1
	else:
		xstatus = 0
	if (ns == 1):
		enviar("Alarma en salon",xstatus)
		enviarmailjose("Alarma en salon",xstatus)
                enviarmailro("Alarma en salon",xstatus)
		log("Alarma en salon")
	if (ns == 2):
                enviar("Alarma en cuarto ordenador",xstatus)
		enviarmailjose("Alarma en cuarto ordenador",xstatus)
                enviarmailro("Alarma en cuarto ordenador",xstatus)
		log("Alarma en cuarto ordenador")
	if (ns == 3):
                enviar("Alarma en distribuidor arriba",xstatus)
		enviarmailjose("Alarma en distribuidor arriba",xstatus)
                enviarmailro("Alarma en distribuidor arriba",xstatus)
		log("Alarma en distribuidor arriba")
	if (ns == 4):
                enviar("Alarma en puerta",xstatus)
		enviarmailjose("Alarma en puerta",xstatus)
                enviarmailro("Alarma en puerta",xstatus)
		log("Alarma en puerta")
	iteracion = 0
	while iteracion <60: ##Tiempo que esta la sirena activada
		if (iteracion == 2):
			GPIO.output(vol1,False)
			time.sleep(0.05)
			GPIO.output(vol2,True)
		if (iteracion == 5):
                        GPIO.output(vol2,False)
                        time.sleep(0.05)
                        GPIO.output(vol3,True)
		if (iteracion == 8):
                        GPIO.output(vol3,False)
                        time.sleep(0.05)
                        GPIO.output(vol4,True)
		if (iteracion == 11):
                        GPIO.output(vol4,False)
                        time.sleep(0.05)
                        GPIO.output(vol5,True)
		if (iteracion == 40):
			llamarro()

		if GPIO.input(lectura): ##Detecta si se ha escrito en estado_alarma.txt
			GPIO.output(lectura,False)
			leer()
			if (estadot == '6' or estadot == '0'):
				buzzoff()
				escribir("0")
				GPIO.output(sirena,False)
				GPIO.output(vol1,False)
				GPIO.output(vol2,False)
				GPIO.output(vol3,False)
				GPIO.output(vol4,False)
				GPIO.output(vol5,False)
				enviar("Sirena apagada",xstatus)
				log("Alarma desconectada via movil")
#				print "break"
				break
			if (estadot == '5' or estadot == '3'):
                                escribir("1")
			if (estadot == '4'):
				buzzoff()
				escribir("0")
				GPIO.output(sirena,False)
				GPIO.output(vol1,False)
                                GPIO.output(vol2,False)
                                GPIO.output(vol3,False)
                                GPIO.output(vol4,False)
				GPIO.output(vol5,False)
				enviar("Sirena apagada\nAlarma desconectada via RFID",xstatus)
				log("Alarma desconectada via RFID")
				break
		time.sleep(0.5)
		if GPIO.input(lectura):
                        GPIO.output(lectura,False)
                        leer()
	                if (estadot == '6' or estadot == '0'):
				buzzoff()
	                        escribir("0")
				GPIO.output(sirena,False)
				GPIO.output(vol1,False)
                                GPIO.output(vol2,False)
                                GPIO.output(vol3,False)
                                GPIO.output(vol4,False)
				GPIO.output(vol5,False)
	                        enviar("Sirena apagada")
				log("Alarma desconectada via movil",xstatus)
#	                        print "break"
	                        break
			if (estadot == '5' or estadot == '3'):
				escribir("1")
	                if (estadot == '4'):
	                        buzzoff()
	                        escribir("0")
				GPIO.output(sirena,False)
				GPIO.output(vol1,False)
                                GPIO.output(vol2,False)
                                GPIO.output(vol3,False)
                                GPIO.output(vol4,False)
				GPIO.output(vol5,False)
	                        enviar("Sirena apagada\nAlarma desconectada via RFID",xstatus)
				log("Alarma desconectada via RFID")
	                        break
		time.sleep(0.5)
		iteracion = iteracion +1
	if GPIO.input(sirena):
		enviar("Sirena apagada\nHay que desactivar la alarma para rearmarla",xstatus)
		GPIO.output(sirena,False)
		GPIO.output(vol1,False)
                GPIO.output(vol2,False)
                GPIO.output(vol3,False)
                GPIO.output(vol4,False)
		GPIO.output(vol5,False)

		GPIO.output(buzz,False)
	GPIO.output(lectura,True)
	print "tiempo agotado"
	while True:
		time.sleep(0.3)
		if GPIO.input(lectura):
                        GPIO.output(lectura,False)
                        leer()
			if (estadot == '6' or estadot == '0'):
#	                        print "break2"
				escribir("0")
				if (buzzt == 0):
					buzzoff()
					log("Alarma desconectada via movil")
	                        break
			if (estadot == '5' or estadot == '3'):
				enviar("Hay que desactivar la alarma para rearmarla",xstatus)
				escribir("1")
			if (estadot == '4'):
	                        buzzoff()
	                        escribir("0")
	                        time.sleep(0.1)
#	                        enviar("Alarma desconectada via RFID")
				log("Alarma desconectada via RFID")
#				print "break2"
	                        break
		time.sleep(0.3)

#Funcion para ver si hay algun sensor de movimiento activado
def sensores():
#Sensor cuarto ordenador
        if ((estadot == '1' or estadot == '2') and (GPIO.input(pir2))):
		print "ordenador"
		time.sleep(0.15)
		if (GPIO.input(pir2)):
			time.sleep(0.15)
	                if (GPIO.input(pir2)):
                		saltoalarma(2)
                		leer()

#Sensor salon
        if ((estadot == '1' or estadot == '2') and (GPIO.input(pir1))):
                time.sleep(0.15)
                if (GPIO.input(pir1)):
                        time.sleep(0.15)
                        if (GPIO.input(pir1)):
                                saltoalarma(1)
                                leer()

#Sensor distribuidor arriba
        if ((estadot == '1') and (GPIO.input(pir3))):
                time.sleep(0.15)
                if (GPIO.input(pir3)):
                        time.sleep(0.15)
                        if (GPIO.input(pir3)):
                                saltoalarma(3)
                                leer()

#Sensor puerta
	if ((estadot == '1' or estadot == '2') and GPIO.input(puerta)):
		time.sleep(0.15)
		if (GPIO.input(puerta)):
			time.sleep(0.15)
	                if (GPIO.input(puerta)):
		                saltoalarma(4)
		                leer()

#Cuerpo principal

if ping():
	xstatus = 1
else:
	xstatus = 0
enviar("Alarma reiniciada",xstatus)
print "alarma iniciada"
GPIO.output(lectura,True)
leer()
time.sleep(3)
global xstatus
xstatus = 0

while True:
	xstatus = 0
	activar=0
	buzzt = 0
	time.sleep(0.1)
	if (GPIO.input(lectura)):
		leer()
		GPIO.output(lectura,False)

	sensores()
#	print "ok"
	if (estadot == '3'):
		escribir("1")
		GPIO.output(buzz,True)
                time.sleep(1)
                GPIO.output(buzz,False)
#               enviar("Alarma conectada via RFID")
		log("Alarma conectada via RFID")
		leer()

	if (estadot == '4'):
		escribir("0")
		buzzoff()
		time.sleep(0.1)
#		enviar("Alarma desconectada via RFID")
		log("Alarma desconectada via RFID")
		leer()

	if (estadot == '5'):
		escribir("1")
                GPIO.output(buzz,True)
                time.sleep(1)
                GPIO.output(buzz,False)
		log("Alarma conectada via movil")
		leer()

        if (estadot == '6'):
		escribir("0")
                buzzoff()
		log("Alarma desconectada via movil")
                time.sleep(0.1)
		leer()
	if (estadot == '7'):
                escribir("2")
                GPIO.output(buzz,True)
                time.sleep(1)
                GPIO.output(buzz,False)
                log("Alarma parcial conectada via movil")
                leer()
