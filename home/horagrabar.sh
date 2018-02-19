#!/bin/bash

dir="/etc/cron.d/p0"

if [ $# -eq 2 ]
then
	init=""
else
	init="#"
fi
hora=`echo $1 | cut -d":" -f1`
min=`echo $1 | cut -d":" -f2`
frase="$init$min $hora * * * pi sudo python /home/pi/p0.py"
echo "$frase" > /etc/cron.d/p0

exit 0
