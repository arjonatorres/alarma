import sys
import xmpp
import time
import string
from smtplib import SMTP
import pws

#Envio mensaje al movil
jid = xmpp.protocol.JID('arjonarasp@gmail.com')
cl=xmpp.Client(jid.getDomain(),debug=[])
#cl.connect()
cl.connect(server=('talk.google.com', 5223))
cl.auth(jid.getNode(),pws.mail)
cl.send(xmpp.protocol.Message("arjonatorres79@gmail.com","Hay que cambiar la bateria", typ='chat'))
#cl.send(xmpp.protocol.Message("robdp79@gmail.com","Hay que cambiar la bateria", typ='chat'))
cl.disconnect()
time.sleep(0.05)

#Funcion enviar gmail jose
def enviarmailjose(mensaje):

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
def enviarmailro(mensaje):

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

enviarmailjose("Hay que cambiar la bateria")
#enviarmailro("Hay que cambiar la bateria")
