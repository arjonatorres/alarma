import time

def log(motivo):
	archivo = open('/home/pi/test.txt','r')
	lectura = archivo.read()
	print (lectura)
	archivo.close()
	archivo = open('/home/pi/test.txt','w')
        archivo.write(motivo + ' - ' + time.ctime() + '\n')
        archivo.write(lectura)
        archivo.close()
log("Prueba")
