import os
import sys
import xmpp
import serial
import time
import string
import threading
import psycopg2
import pws

intentos=3

p0="\x1A\x65"
p1="\x1A\x66"
p2="\x1A\x68"
p3="\x1A\x67"
p4="\x1A\x64"

pa0="\x1E\x65"
pa1="\x1E\x66"
pa2="\x1E\x68"
pa3="\x1E\x67"
pa4="\x1E\x64"

pb0="\x1D\x65"
pb1="\x1D\x66"
pb2="\x1D\x68"
pb3="\x1D\x67"
pb4="\x1D\x64"

rp1="\x14\x6A"
rp2="\x15\x6A"
rp3="\x16\x6A"
rp4="\x17\x6A"
rp5="\x18\x6A"
rp6="\x19\x6A"

tp0="Bajando persianas"
tp1="Posicion 1 persianas"
tp2="Posicion 2 persianas"
tp3="Posicion 3 persianas"
tp4="Subiendo persianas"

tpa0="Bajando planta alta"
tpa1="Posicion 1 planta alta"
tpa2="Posicion 2 planta alta"
tpa3="Posicion 3 planta alta"
tpa4="Subiendo planta alta"

tpb0="Bajando planta baja"
tpb1="Posicion 1 planta baja"
tpb2="Posicion 2 planta baja"
tpb3="Posicion 3 planta baja"
tpb4="Subiendo planta baja"

tr="Reintentando..."

persianat=0
persiana1=0
persiana2=0
persiana3=0
persiana4=0
persiana5=0
persiana6=0

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)

#Funcion enviar Hangouts
def enviar(texto):

	jid = xmpp.protocol.JID('arjonarasp@gmail.com')
#	jid = xmpp.JID('arjonarasp@gmail.com')
        cl=xmpp.Client(jid.getDomain(),debug=[])
#        cl.connect()
	cl.connect(server=('talk.google.com', 5223))
        cl.auth(jid.getNode(),pws.mail)
#	cl.SendInitPresence(requestRoster=0)
        cl.send(xmpp.protocol.Message("arjonatorres79@gmail.com",texto, typ='chat'))
	cl.send(xmpp.protocol.Message("robdp79@gmail.com",texto, typ='chat'))
	time.sleep(1)
        cl.disconnect()

#Funcion orden puerto serie
def orden(cod):

	time.sleep(0.1)
	ser.setRTS(True)
	time.sleep(0.1)
	ser.write(cod)
	time.sleep(0.1)
	ser.setRTS(False)
	time.sleep(0.1)



#Funcion recibir datos
def recibir(nh):
	#dato="-1"
        cont=0
	time.sleep(0.15)
        ser.setRTS(True)
        time.sleep(0.03)
        ser.write(nh)
        time.sleep(0.03)
	ser.flushInput()
        ser.setRTS(False)
        state=ser.read(8)
	time.sleep(0.1)
#	print state.encode('hex')

        if len(state.encode('hex')) == 16:

                while ((str(state.encode('hex'))[len(state.encode('hex'))-16]) != '1' and (str(state.encode('hex'))[len(state.encode('hex'))-15]) != 'b' and cont!=5):
                        cont=cont+1

			time.sleep(0.15)
		        ser.setRTS(True)
        		time.sleep(0.03)
        		ser.write(nh)
        		time.sleep(0.03)
        		ser.flushInput()
        		ser.setRTS(False)
        		state=ser.read(8)
        		time.sleep(0.1)

		dato1 = (str(state.encode('hex'))[len(state.encode('hex'))-4])
                dato2 = (str(state.encode('hex'))[len(state.encode('hex'))-3])
                dato = dato1 + dato2

                return dato
        else:
                dato = "En movimiento"
                return dato


#Funcion comprobar
def comprobar():
	global persianat
	global persiana1
	global persiana2
	global persiana3
	global persiana4
	global persiana5
	global persiana6
	persianat=0
	persiana1 = recibir(rp1)
	if (persiana1 != "Error") and (persiana1 != "En movimiento"):
	        persiana1 = str(int(persiana1,16))
		persianat += int(persiana1)
	persiana2 = recibir(rp2)
	if (persiana2 != "Error") and (persiana2 != "En movimiento"):
	        persiana2 = str(int(persiana2,16))
		persianat += int(persiana2)
	persiana3 = recibir(rp3)
	if (persiana3 != "Error") and (persiana3 != "En movimiento"):
	        persiana3 = str(int(persiana3,16))
		persianat += int(persiana3)
	persiana4 = recibir(rp4)
	if (persiana4 != "error") and (persiana4 != "En movimiento"):
	        persiana4 = str(int(persiana4,16))
		persianat += int(persiana4)
	persiana5 = recibir(rp5)
	if (persiana5 != "error") and (persiana5 != "En movimiento"):
	        persiana5 = str(int(persiana5,16))
		persianat += int(persiana5)
	persiana6 = recibir(rp6)
	if (persiana6 != "error") and (persiana6 != "En movimiento"):
	        persiana6 = str(int(persiana6,16))
		persianat += int(persiana6)
	return persianat

#Funcion comprobar arriba
def comprobararriba():
        global persianat
        global persiana1
        global persiana2
        global persiana3
        global persiana4
        global persiana5
        global persiana6
        persianat=0
	persiana1 = recibir(rp1)
        if (persiana1 != "Error") and (persiana1 != "En movimiento"):
                persiana1 = str(int(persiana1,16))
#                persianat += int(persiana1)
        persiana2 = recibir(rp2)
        if (persiana2 != "Error") and (persiana2 != "En movimiento"):
                persiana2 = str(int(persiana2,16))
#                persianat += int(persiana2)
        persiana3 = recibir(rp3)
        if (persiana3 != "Error") and (persiana3 != "En movimiento"):
                persiana3 = str(int(persiana3,16))
                persianat += int(persiana3)
        persiana4 = recibir(rp4)
        if (persiana4 != "error") and (persiana4 != "En movimiento"):
                persiana4 = str(int(persiana4,16))
                persianat += int(persiana4)
        persiana5 = recibir(rp5)
        if (persiana5 != "error") and (persiana5 != "En movimiento"):
                persiana5 = str(int(persiana5,16))
                persianat += int(persiana5)
        persiana6 = recibir(rp6)
        if (persiana6 != "error") and (persiana6 != "En movimiento"):
                persiana6 = str(int(persiana6,16))
                persianat += int(persiana6)
	return persianat

#Funcion comprobarabajo
def comprobarabajo():
        global persianat
        global persiana1
        global persiana2
        global persiana3
        global persiana4
        global persiana5
        global persiana6
        persianat=0
        persiana1 = recibir(rp1)
        if (persiana1 != "Error") and (persiana1 != "En movimiento"):
                persiana1 = str(int(persiana1,16))
                persianat += int(persiana1)
        persiana2 = recibir(rp2)
        if (persiana2 != "Error") and (persiana2 != "En movimiento"):
                persiana2 = str(int(persiana2,16))
                persianat += int(persiana2)
	persiana3 = recibir(rp3)
        if (persiana3 != "Error") and (persiana3 != "En movimiento"):
                persiana3 = str(int(persiana3,16))
#                persianat += int(persiana3)
        persiana4 = recibir(rp4)
        if (persiana4 != "error") and (persiana4 != "En movimiento"):
                persiana4 = str(int(persiana4,16))
#                persianat += int(persiana4)
        persiana5 = recibir(rp5)
        if (persiana5 != "error") and (persiana5 != "En movimiento"):
                persiana5 = str(int(persiana5,16))
#                persianat += int(persiana5)
        persiana6 = recibir(rp6)
        if (persiana6 != "error") and (persiana6 != "En movimiento"):
                persiana6 = str(int(persiana6,16))
#                persianat += int(persiana6)
	return persianat

#Funcion initper
def initper():
	conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
	cur = conn.cursor()

	sql="""select posicion1, posicion2, posicion3, posicion4
                 from persianas order by codigo"""
	cur.execute(sql)
	p = cur.fetchall()
	conn.close()
	return p[0][0],p[0][1],p[0][2],p[0][3],p[1][0],p[1][1],p[1][2],p[1][3],p[2][0],p[2][1],p[2][2],p[2][3],p[3][0],p[3][1],p[3][2],p[3][3],p[4][0],p[4][1],p[4][2],p[4][3],p[5][0],p[5][1],p[5][2],p[5][3]


#Funcion thread1
def hilo1(f1,h1):

	initper()
	orden(f1)
#	os.system("su - pi -c 'echo %s | sendxmpp -t arjonatorres79@gmail.com'"%(h1))
	enviar(h1)
	time.sleep(40)
#	time.sleep(5)

#Funcion thread2
def hilo2():
#	os.system("""su - pi -c 'echo "1-Salon = %s \n2-C Ordenador = %s \n3-Dormitorio matr. = %s \n4-Bano matr.= %s \n5-Dormitorio der. = %s \n6-Dormitorio izq. = %s" | sendxmpp -t arjonatorres79@gmail.com'"""%(persiana1,persiana2,persiana3,persiana4,persiana5,persiana6))
	enviar("1-Salon = %s \n2-C Ordenador = %s \n3-Dormitorio matr. = %s \n4-Bano matr.= %s \n5-Dormitorio der. = %s \n6-Dormitorio izq. = %s"%(persiana1,persiana2,persiana3,persiana4,persiana5,persiana6))

def hilo3():
	print(persiana1)
	print(persiana2)
	print(persiana3)
        print(persiana4)
	print(persiana5)
        print(persiana6)

p1s,p2s,p3s,p4s,p1o,p2o,p3o,p4o,p1m,p2m,p3m,p4m,p1b,p2b,p3b,p4b,p1d,p2d,p3d,p4d,p1i,p2i,p3i,p4i = initper()
