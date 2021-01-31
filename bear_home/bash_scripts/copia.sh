#!/bin/bash

# Dump de la BD
mysqldump -u bear -pbear bearcontrol > /home/bear/db_dump/copia.sql

#Copia de los dump a Discorasp
sudo mv /media/usbcopia/copia.sql /media/usbcopia/copia.bak
sudo cp /home/bear/db_dump/copia.sql /media/usbcopia/copia.sql
