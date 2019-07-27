#! /usr/bin/python
import RPi.GPIO as GPIO

#Configuracion GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

#Sensores
pir1 = 5 #Salon
pir2 = 6 #Cuarto ordenador
pir3 = 13 #Distribuidor arriba

#Inicializacion de entradas y salidas GPIO
GPIO.setup(pir1,GPIO.IN)
GPIO.setup(pir2,GPIO.IN)
GPIO.setup(pir3,GPIO.IN)


print GPIO.input(pir1)
print GPIO.input(pir2)
print GPIO.input(pir3)