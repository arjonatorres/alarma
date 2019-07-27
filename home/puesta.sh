#!/bin/bash

hora=`php puesta_sol.php`
res=`sudo ./horaleer.sh`
on=`echo $res | cut -d" " -f2`
if [ $on -ne 1 ]
then
	on=""
fi
sudo ./horagrabar.sh $hora $on

exit 0
