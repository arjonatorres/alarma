import serial
import time
import RPi.GPIO as GPIO

ser=serial.Serial('/dev/ttyAMA0', baudrate=115200, timeout=4)

GPIO.setmode(GPIO.BCM)
RTS=18
GPIO.setup(RTS,GPIO.OUT)
GPIO.output(RTS,True)
time.sleep(2)
GPIO.output(RTS,False)
time.sleep(2)
cmd="ATS0=1\r"
ser.write(cmd.encode())
msg=ser.readline()
print(msg)
