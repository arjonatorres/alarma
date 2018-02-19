#!/bin/bash
#Programado por Jose Arjona

case $1 in
	ONBATT)
		python /home/pi/onbatt.py
	;;
	ONLINE)
		python /home/pi/online.py
	;;
        LOWBATT)
                python /home/pi/lowbatt.py
        ;;
        REPLBATT)
                python /home/pi/replbatt.py
        ;;
esac
