# encoding: utf-8
import xmpp
from smtplib import SMTP
import mysql.connector as mariadb
import time
import pws
from pprint import pprint


def conectar():
	"""Devuelve la conexion a la BD.

	"""
	return mariadb.connect(user=pws.dbuser, password=pws.dbpassword, host=pws.dbhost, database=pws.dbdatabase)


def enviar(mensaje, logFlag = True, tipo = 'hangouts'):
	"""Envía notificaciones.

	Parámetros:
	mensaje -- Texto del mensaje a enviar
	log -- True para registrar el mensaje en el log
	tipo -- 'todos', 'mail' o vacío para 'hangouts'

	"""
	destinatarios = mailsNotificaciones()
	if (destinatarios):
		if (tipo == 'todos'):
			enviarHangouts(mensaje, destinatarios)
			enviarMail(mensaje, destinatarios)
		elif (tipo == 'hangouts'):
			enviarHangouts(mensaje, destinatarios)
		elif (tipo == 'mail'):
			enviarMail(mensaje, destinatarios)
	if (logFlag):
		log(mensaje)

def enviarHangouts(mensaje, destinatarios):
	"""Envía un mensaje vía Hangouts.

	Parámetros:
	mensaje -- Texto del mensaje a enviar
	destinatarios -- Lista de destinatarios del mail

	"""
	jid = xmpp.JID('arjonarasp@gmail.com')
	cl=xmpp.Client(jid.getDomain(),debug=[])
	cl.connect(server=('talk.google.com', 5223))
	cl.auth(jid.getNode(),pws.mail)
	for destinatario in destinatarios:
		cl.send(xmpp.protocol.Message(destinatario, mensaje, typ='chat'))
	cl.disconnect()

def enviarMail(mensaje, destinatarios, asunto = 'BeAr Control'):
	"""Envía un mail a cada destinatario.

	Parámetros:
	mensaje -- Texto del mensaje a enviar
	destinatarios -- Lista de destinatarios del mail
	asunto -- Asunto del mail (opcional)

	"""
	EnviarCorreo = SMTP()
	EnviarCorreo.connect("smtp.gmail.com", 587)
	EnviarCorreo.ehlo()
	EnviarCorreo.starttls()
	EnviarCorreo.ehlo()
	EnviarCorreo.login("arjonarasp@gmail.com",pws.mail)
	cuerpoMensaje = mensaje + '\n' + '\n'
	for destinatario in destinatarios:
		cabecera = 'To:' + destinatario + '\n'
		cabecera += 'From:' + "arjonarasp@gmail.com" + '\n'
		cabecera += 'Subject:' + asunto + '\n' + '\n'
		EnviarCorreo.sendmail("arjonarasp@gmail.com",destinatario, cabecera + cuerpoMensaje)
	EnviarCorreo.close()

def mailsNotificaciones():
	"""Busca los mails para notificaciones.

	Devuelve un array con los destinatarios

	"""
	db = conectar()
	cur = db.cursor()
	sql="SELECT mail FROM usuarios WHERE notificaciones=1"
	cur.execute(sql)
	p = cur.fetchall()
	res = []
	for row in p:
		res.append(row[0])
	db.close()

	return res

def log(mensaje):
	"""Guarda un registro en la tabla de logs.

	"""
	db = conectar()
	cur = db.cursor()
	sql = "INSERT INTO logs (mensaje) VALUES (%s)"
	cur.execute(sql, (mensaje,))
	db.commit()
	db.close()


def leerEstadoAlarma():
	""" Se lee el estado de la alarma

	Devuelve el estado de la alarma

	"""
	db = conectar()
	cur = db.cursor()
	sql="SELECT valor FROM parametros WHERE nombre='estado_alarma'"
	cur.execute(sql)
	p = cur.fetchall()
	db.close()
	return p[0][0]

def escribirEstadoAlarma(ne):
	""" Guarda en BD el estado de la alarma

	"""
	db = conectar()
	cur = db.cursor()
	sql = "UPDATE parametros SET valor=%s WHERE nombre='estado_alarma'"
	cur.execute(sql, (ne,))
	db.commit()
	db.close()

def enviarArduino(ser, cadena_envio):
	##time.sleep(0.05)
	#ser.setRTS(True)
	##time.sleep(0.03)
	ser.write(cadena_envio)
	##time.sleep(0.03)
	ser.flush()
	#ser.setRTS(False)

def recibirArduino(ser, cadena_envio, num_bytes):
	##time.sleep(0.05)
	#ser.setRTS(True)
	##time.sleep(0.03)
	ser.write(cadena_envio)
	##time.sleep(0.03)
	ser.flush()
	#ser.setRTS(False)
	state=ser.read(num_bytes)
	#time.sleep(0.03)
	return state.encode('hex')