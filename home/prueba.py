import time

archivo = open('/home/pi/prueba.txt','w')
archivo.write(time.ctime() + '\n')
archivo.close()
