#! /usr/bin/python
import serial
import string
import time
import RPi.GPIO as GPIO

#Inicializacion GPIO y serial
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
buzz = 24
GPIO.setup(4, GPIO.OUT)
GPIO.setup (buzz, GPIO.OUT)


arduino = serial.Serial('/dev/ttyUSB1',9600,bytesize=8,parity='N',stopbits=1,timeout=4)
#arduino = serial.Serial('/dev/ttyAMA0',9600)
arduino.open()

#Funcion leer estado_alarma.txt
def leer():
	global estadot
	estado = open('/home/pi/estado_alarma.txt','r')
	estadot = estado.read(1)
	estado.close()

#Funcion escribir estado_alarma.txt
def escribir(ne):
	note = open('/home/pi/estado_alarma.txt','w')
	note.write(ne)
	note.close()
#Funcion guardar log.txt
def log(motivo):
	archivo = open('/home/pi/log.txt','a')
	archivo.write(motivo + ' - ' + time.ctime() + '\n')
	archivo.close()


try:
	while True:
		state=arduino.readline()
#		print state.encode('hex')
#Llavero Ro
		if ((str(state.encode('hex'))) == '1133e42bed0d0a'):
			GPIO.output(buzz,True)
			time.sleep(0.08)
			GPIO.output(buzz,False)
			state=arduino.readline()
			if ((str(state.encode('hex'))) == '000d0a'):
				escribir("4")
				GPIO.output(4, True)
#				print "Alarma desconectada"
			elif ((str(state.encode('hex'))) == '010d0a'):
				escribir("3")
                        	GPIO.output(4, True)
#                        	print "Alarma conectada"
#Llavero Jose
		elif ((str(state.encode('hex'))) == '71db15249b0d0a'):
                        GPIO.output(buzz,True)
                        time.sleep(0.08)
                        GPIO.output(buzz,False)
                        state=arduino.readline()
                        if ((str(state.encode('hex'))) == '000d0a'):
                                escribir("4")
                                GPIO.output(4, True)
#                                print "Alarma desconectada"
                        elif ((str(state.encode('hex'))) == '010d0a'):
                                escribir("3")
                                GPIO.output(4, True)
#                               print "Alarma conectada"

		else:
			GPIO.output(buzz,True)
                        time.sleep(0.08)
                        GPIO.output(buzz,False)
			time.sleep(0.04)
			GPIO.output(buzz,True)
                        time.sleep(0.08)
                        GPIO.output(buzz,False)
                        time.sleep(0.04)
			GPIO.output(buzz,True)
                        time.sleep(0.08)
                        GPIO.output(buzz,False)
                        time.sleep(0.04)
			GPIO.output(buzz,True)
                        time.sleep(0.08)
                        GPIO.output(buzz,False)
                        time.sleep(0.04)
			GPIO.output(buzz,True)
                        time.sleep(0.08)
                        GPIO.output(buzz,False)
                        time.sleep(0.04)
			GPIO.output(buzz,True)
                        time.sleep(0.08)
                        GPIO.output(buzz,False)
                        time.sleep(0.04)


#		if ((str(state.encode('hex'))[len(state.encode('hex'))-5]) == 'f'):
#			print('Recibido')
#		elif comando == 'L':
#			print('Led apagado')

except:
	log("Excepcion en rfid.py")
#	print "Fin"
	arduino.close()
