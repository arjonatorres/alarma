#!/bin/bash

sudo -u postgres pg_dump -a jose -t placas > copia.sql
sudo cp copia.sql /media/discorasp/copia
