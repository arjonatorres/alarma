import serial
import time

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)
#ser.close()

#ser.open()
time.sleep(0.25)
ser.setRTS(True)
time.sleep(0.03)
ser.write("\x14\x6A")
time.sleep(0.03)
ser.flushInput()
ser.setRTS(False)
state=ser.read(8)
time.sleep(0.1)
print state.encode('hex')

ser.close()

#if len(state.encode('hex')) == 20:
#        if ((str(state.encode('hex'))[len(state.encode('hex'))-16$
#                dato1 = (str(state.encode('hex'))[len(state.encod$
#                dato2 = (str(state.encode('hex'))[len(state.encod$
#                dato = dato1 + dato2
#        else:
#                dato = "Error"
#        return dato
#else:
#        dato = "En movimiento"
#        return dato

