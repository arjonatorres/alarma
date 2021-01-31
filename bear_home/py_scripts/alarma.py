# encoding: utf-8
# Alarma domestica por Jose Arjona

from bearFunctions import *
import RPi.GPIO as GPIO
import serial
import threading

# Configuracion GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

# Configuracion serial
#sim900 = serial.Serial('/dev/ttyAMA0', baudrate=115200, timeout=4)
arduino = serial.Serial('/dev/ttyUSB1',9600,bytesize=8,parity='N',stopbits=1,timeout=4)

# Pines
corriente = 17 # Sensor de corriente
buzz = 24
sirena = 23
vol1 = 12
vol2 = 16
vol3 = 20
vol4 = 21
vol5 = 26
estado_alarma_flag = 28
sensores_flag = 29


# Inicializacion de entradas y salidas GPIO
GPIO.cleanup()
GPIO.setup(estado_alarma_flag,GPIO.OUT)
GPIO.setup(sensores_flag,GPIO.OUT)
GPIO.setup(corriente,GPIO.IN)
GPIO.setup(buzz,GPIO.OUT)
GPIO.setup(sirena,GPIO.OUT)
GPIO.setup(vol1,GPIO.OUT)
GPIO.setup(vol2,GPIO.OUT)
GPIO.setup(vol3,GPIO.OUT)
GPIO.setup(vol4,GPIO.OUT)
GPIO.setup(vol5,GPIO.OUT)

def rfid():
	global estadot
	db = conectar()
	cur = db.cursor()
	sql="SELECT code, u.usuario FROM rfid INNER JOIN usuarios AS u ON rfid.usuario_id=u.id"
	cur.execute(sql)
	p = cur.fetchall()
	frecs = {}
	for row in p:
		frecs[row[0]] = row[1]
	db.close()

	while True:
		state=arduino.readline()
#		print state.encode('hex')

		code = str(state.encode('hex'))
		if (frecs.has_key(code)):
			GPIO.output(buzz,True)
			time.sleep(0.08)
			GPIO.output(buzz,False)
			state=arduino.readline()
			code2 = str(state.encode('hex'))
			if (code2 == '000d0a'):
				escribirEstadoAlarma("0")
				buzzoff()
				time.sleep(0.1)
				log("Alarma desconectada via RFID por " + frecs[code])
				estadot = '0'
			elif (code2 == '010d0a'):
				escribirEstadoAlarma("1")
				GPIO.output(buzz,True)
				time.sleep(1)
				GPIO.output(buzz,False)
				log("Alarma conectada via RFID por " + frecs[code])
				estadot = '1'

		elif ((str(state.encode('hex'))) != '000d0a' and (str(state.encode('hex'))) != '001d0a' and (str(state.encode('hex'))) != ''):
			enviar("RFID no registrado. Código: " + code)
			for x in range(6):
				GPIO.output(buzz,True)
				time.sleep(0.08)
				GPIO.output(buzz,False)
				time.sleep(0.04)


def initSensores():
	db = conectar()
	cur = db.cursor()
	sql="SELECT pin, nombre, activo FROM sensores"
	cur.execute(sql)
	p = cur.fetchall()
	sens = {}
	for row in p:
		GPIO.setup(row[0],GPIO.IN)
		if (row[2]):
			sens[row[0]] = row[1]
	db.close()

	return sens

def llamar():
	"""Realiza una llamada de teléfono a todos los números disponibles.

	"""
	db = conectar()
	cur = db.cursor()
	sql="SELECT telefono FROM usuarios WHERE telefono IS NOT NULL"
	cur.execute(sql)
	p = cur.fetchall()
	res = []
	for row in p:
		res.append(row[0])
	db.close()

	for num in res:
		numero = 'ATD' + num + ";\r"
		print numero
		#sim900.write(cmd.encode())
		for x in range(40):
			if (estadot == '0'):
				return
			time.sleep(1)


# Funcion de buzz al desactivar la alarma
def buzzoff():
	apagarSirena()
	time.sleep(0.4)
	GPIO.output(buzz,True)
	time.sleep(0.08)
	GPIO.output(buzz,False)
	time.sleep(0.04)
	GPIO.output(buzz,True)
	time.sleep(0.08)
	GPIO.output(buzz,False)
#	log("Alarma desconectada")


def apagarSirena():
	GPIO.output(sirena,False)
	GPIO.output(vol1,False)
	GPIO.output(vol2,False)
	GPIO.output(vol3,False)
	GPIO.output(vol4,False)
	GPIO.output(vol5,False)


# Funcion salto de alarma
def saltoalarma(nombre_salto):
	global estadot
	GPIO.output(buzz,True)
	GPIO.output(sirena,True)
	GPIO.output(vol1,True)
	t = threading.Thread(target=llamar)
	t.start()

	enviar("Alarma en " + nombre_salto, True, 'todos')

	iteracion = 0
	while iteracion < 60: # Tiempo que esta la sirena activada
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

		for x in range(2):
			if (estadot == '0'):
				enviar("Sirena apagada y alarma desconectada via RFID", False)
				#apagarSirena() - Ya se apaga con el buzzoff()
				return
			if GPIO.input(estado_alarma_flag): # Detecta si se ha escrito a traves del movil
				GPIO.output(estado_alarma_flag,False)
				estadot = leerEstadoAlarma()
				if (estadot == '0'):
					buzzoff()
					enviar("Sirena apagada y alarma desconectada via movil", False)
					#escribirEstadoAlarma("0")
					#apagarSirena()
					#enviar("Sirena apagada y alarma desconectada via movil")
					return
			time.sleep(0.5)
		iteracion = iteracion + 1
	if GPIO.input(sirena):
		enviar("Sirena apagada, hay que desactivar la alarma para rearmarla")
		apagarSirena()
		GPIO.output(buzz,False)

	GPIO.output(estado_alarma_flag,True)
	#print "tiempo agotado"
	while True:
		time.sleep(0.3)
		if (estadot == '0'):
			enviar("Sirena apagada y alarma desconectada via RFID", False)
			return
		if GPIO.input(estado_alarma_flag): # Detecta si se ha escrito en estado_alarma
			GPIO.output(estado_alarma_flag,False)
			estadot = leerEstadoAlarma()
			if (estadot == '0'):
				buzzoff()
				#escribirEstadoAlarma("0")
				#apagarSirena()
				enviar("Sirena apagada y alarma desconectada via movil", False)
				return
		time.sleep(0.3)


def tripleComprobacion(pint, nombret):
	global estadot
	time.sleep(0.15)
	if (GPIO.input(pint)):
		time.sleep(0.15)
		if (GPIO.input(pint)):
			saltoalarma(nombret)
			estadot = leerEstadoAlarma()


# Funcion para ver si hay corriente 220v
def sensor220v():
	global xcorriente
	#Se va la corriente
	if ((xcorriente == 0) and (not(GPIO.input(corriente)))):
		time.sleep(0.15)
		if (not(GPIO.input(corriente))):
			time.sleep(0.15)
			if (not(GPIO.input(corriente))):
				xcorriente = 1
				enviar("Corriente 220v interrumpida", True, 'todos')
				time.sleep(0.5)

	#Vuelve la corriente
	if ((xcorriente == 1) and (GPIO.input(corriente))):
		time.sleep(0.15)
		if (GPIO.input(corriente)):
			time.sleep(0.15)
			if (GPIO.input(corriente)):
				xcorriente = 0
				enviar("Corriente 220v restablecida", True, 'todos')
				time.sleep(0.5)


# Cuerpo principal

enviar("BeAr Control reiniciado")
#print "alarma iniciada"
GPIO.output(estado_alarma_flag,False)
GPIO.output(sensores_flag,False)
estadot = leerEstadoAlarma()
time.sleep(3)

xcorriente = 0
r = threading.Thread(target=rfid)
r.daemon = True
r.start()

sensores=initSensores()

while True:
	time.sleep(0.1)
	if (GPIO.input(sensores_flag)):
		#print 'iniciando sensores'
		GPIO.output(sensores_flag,False)
		sensores=initSensores()
	if (GPIO.input(estado_alarma_flag)):
		estadot = leerEstadoAlarma()
		GPIO.output(estado_alarma_flag,False)

		if (estadot == '0'):
			buzzoff()
			#print("Alarma desconectada via movil")
			time.sleep(0.1)
		elif (estadot == '1'):
			GPIO.output(buzz,True)
			time.sleep(1)
			GPIO.output(buzz,False)
			#print("Alarma conectada via movil")


	if (estadot == '1'):
		for pin, nombre in sensores.items():
			if (GPIO.input(pin)):
				tripleComprobacion(pin, nombre)
				break

	sensor220v()
