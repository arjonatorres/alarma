#!/usr/bin/python
# Title      : netisup.py
# Description: Check if your Internet connection is alive
# Author     : linuxitux
# Date       : 01-02-2016
# Usage      : ./netisup.py
# Notes      : ICMP traffic must not be filtered

import subprocess, time


def ping():
	ret = subprocess.call(['ping', '-c', '3', '-W', '5', '8.7.8.8'],
	stdout=open('/dev/null', 'w'),
	stderr=open('/dev/null', 'w'))
	return ret == 0

def prueba():
	if xstatus:
        	print "[%s] Network is up!" % time.strftime("%Y-%m-%d %H:%M:%S")
	else:
        	print "[%s] Network is down :(" % time.strftime("%Y-%m-%d %H:%M:%S")

global xstatus

xstatus = 0

if ping():
	xstatus = 1
prueba()



print xstatus
