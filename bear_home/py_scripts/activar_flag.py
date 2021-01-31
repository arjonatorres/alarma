import RPi.GPIO as GPIO
import sys

pin = int(sys.argv[1])

GPIO.setmode(GPIO.BCM)
GPIO.setup(pin, GPIO.OUT) ##GPIO pin como salida

GPIO.output(pin, True) ## Enciendo el pin
