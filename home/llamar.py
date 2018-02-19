import serial
import time

ser = serial.Serial('/dev/ttyAMA0', baudrate=115200, timeout =4)
#ser.open()
cmd="ATD637371009;\r"
ser.write(cmd.encode())
msg=ser.readline()
print(msg)
ser.close()
