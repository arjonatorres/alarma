#!/usr/bin/python
import curses
import smbus
import time
import xmpp
import psycopg2
import pws

# - Carga de modulo para I2C  -----------------------------------
miADC = smbus.SMBus(1)

# - Rutina de lectura del valor ADC -----------------------------
#   X = canal a leer (1 a 4)
# ---------------------------------------------------------------
def leeINPUT(X):
	# Configuro registro de control para lectura de canal X
	miADC.write_byte(0x48,0x40 + (X-1))
	time.sleep(0.1)
	lectura = miADC.read_byte(0x48) # read A/D
	lectura = miADC.read_byte(0x48)
	return lectura

contador = 0
suma = 0
temperatura = 0

#PROGRAMA PRINCIPAL
while contador <30:  # Rutina principal del programa

	an4 = leeINPUT(4)
#	print "Sonda",an4
	suma += an4

#	time.sleep(0.01)
	contador +=1
	media = suma/30

#	print "Total = ", suma
#	print "Media = ",(media)

if (30 <= media <= 92):
	temperatura = (media/2)-15
if (93 <= media <= 113):
	temperatura = (media/2)-16
if (114 <= media <= 135):
	temperatura = (media/2)-17
if (136 <= media <= 155):
	temperatura = (media/2)-17
if (156 <= media <= 173):
	temperatura = (media/2)-17
if (174 <= media <= 188):
	temperatura = (media/2)-14
if (189 <= media <= 201):
	temperatura = (media/2)-11
if (202 <= media <= 211):
	temperatura = (media/2)-7

temp = str(temperatura)

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()
sql="insert into placas (temp) values(%s);" % (temp)
cur.execute(sql)
conn.commit()
conn.close()
