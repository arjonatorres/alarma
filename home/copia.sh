#!/bin/bash

# Dump de la BD
sudo -u postgres pg_dump -a jose -t placas > copia.sql
#sudo -u postgres pg_dump -a recetas > copia_recetas.sql

#Copia de los dump a Discorasp
sudo mv /media/discorasp/copia/copia.sql /media/discorasp/copia/copia.bak
sudo cp copia.sql /media/discorasp/copia
#sudo mv /media/discorasp/copia/copia_recetas.sql /media/discorasp/copia/copia_recetas.bak
#sudo cp copia_recetas.sql /media/discorasp/copia

#Copia de las imágenes de las recetas a Discorasp
#sudo cp -u /var/www/html/recetas/web/images/avatar/*.* /media/discorasp/copia/images/avatar
#sudo cp -u /var/www/html/recetas/web/images/pasos/*.* /media/discorasp/copia/images/pasos
#sudo cp -u /var/www/html/recetas/web/images/recetas/*.* /media/discorasp/copia/images/recetas
