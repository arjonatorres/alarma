# encoding: utf-8
from bearFunctions import *
import serial
import sys

codigo = sys.argv[1].decode("hex")
orden = sys.argv[2].decode("hex")
pers = sys.argv[3].replace(' ', '\',\'')
pos = sys.argv[4]
orden_solicitar = sys.argv[5].decode("hex")
cadena_envio = codigo + orden

if (pos == 'bajar'):
	pos = '0'
elif (pos == 'subir'):
	pos = 'p.posicion4'
elif (pos == 'parar'):
	pos = pos
else:
	pos = 'p.' + pos.replace(" ", "")

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=0.5)

def getPersianas(pos, pers):
	""" Obtiene el tiempo máximo de las persianas

	"""
	db = conectar()
	cur = db.cursor()
	sql = "SELECT a.codigo, " + pos + ", p.posicion4 "\
	"FROM persianas p INNER JOIN arduinos a ON p.arduino_id = a.id "\
	"WHERE a.codigo in('" + pers + "') "\
	"ORDER BY a.id"
	cur.execute(sql)
	p = cur.fetchall()
	res = {}
	numMax = 0
	for row in p:
		if (int(row[2]) > numMax):
			numMax = int(row[2])
		res[row[0]] = {'posicion': int(row[1]), 'posicion4': int(row[2])}
	db.close()

	return res, numMax


def enviarPosicionFinal(arrayPersianas):
	cadena = ''

	db = conectar()
	cur = db.cursor()
	sql = "SELECT a.codigo, h.nombre, p.posicion4 "\
	"FROM persianas p INNER JOIN habitaciones h ON p.habitacion_id = h.id "\
	"INNER JOIN arduinos a ON p.arduino_id = a.id "\
	"ORDER BY h.id"
	cur.execute(sql)
	p = cur.fetchall()
	db.close()
	res = {}
	numMax = 0
	for row in p:
		if (not arrayPersianas.has_key(row[0])):
			cadena_envio = row[0].decode("hex") + orden_solicitar
			estado = recibirArduino(ser, cadena_envio, 5)
			if (len(estado) != 10):
				res[row[0]] = {'nombre': row[1], 'posFinal': 'error'}
			else:
				alturaHex = (estado[4:6])
				res[row[0]] = {'nombre': row[1], 'posFinal': str((int(alturaHex, 16)*100)/int(row[2])) + ' %'}
		else:
			res[row[0]] = {'nombre': row[1], 'posFinal': arrayPersianas[row[0]]['posFinal']}

	res = sorted(res.items())

	for fila in res:
		cadena += fila[1]['nombre'] + ' = ' + str(fila[1]['posFinal']) + "\n"

	enviar(cadena, False)


enviarArduino(ser, cadena_envio)

if (pos == 'parar'):
	time.sleep(0.3)
	enviarPosicionFinal({})
else:
	arrayPersianas, alturaMax = getPersianas(pos, pers)

	#alturaMax = 2

	posFinal = {}

	intentos = 3

	for i in range(intentos):
		contador = 0
		time.sleep(alturaMax+1)
		for persiana in arrayPersianas.keys():
			arrayPersianas[persiana]['posFinal'] = 'error'
			cadena_envio = persiana.decode("hex") + orden_solicitar
			estado = recibirArduino(ser, cadena_envio, 5)
			if (len(estado) != 10):
				continue
			alturaHex = (estado[4:6])
			arrayPersianas[persiana]['posFinal'] = str((int(alturaHex,16)*100)/int(arrayPersianas[persiana]['posicion4'])) + ' %'
			if (int(alturaHex,16) == arrayPersianas[persiana]['posicion']):
				contador += 1
		if (contador == len(arrayPersianas) or i == (intentos-1)):
			break
		enviar('Reintentando...', False)

	enviarPosicionFinal(arrayPersianas)


ser.close()
