import RPi.GPIO as GPIO
import time
GPIO.setmode(GPIO.BCM)
GPIO.setup(4, GPIO.OUT) ##GPIO 4 como salida

GPIO.output(4, True) ## Enciendo el 4

