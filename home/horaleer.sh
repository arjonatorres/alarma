#!/bin/bash

dir="/etc/cron.d/p0"
if [ -f $dir ]
then
	lon=`cat $dir | cut -d" " -f1 | wc -m`
	primer=`cat $dir | cut -d" " -f1`

	if [ $lon -gt 3 ]
	then
		on=0
		min=`echo $primer | cut -d"#" -f2`
	else
		on=1
		min=`echo $primer`
	fi
	hora=` cat $dir | cut -d" " -f2`
	echo "$hora:$min"
	echo $on
fi
exit 0
