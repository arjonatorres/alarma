import os
import sys
import time
import string
import serial
import psycopg2
import pws

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()

ser = serial.Serial('/dev/ttyUSB0',9600,bytesize=8,parity='N',stopbits=1,timeout=1)
part1=chr(int(("0x" + sys.argv[1]),0))
part2= chr(0x69)
part3=chr(int(("0x" + sys.argv[2]),0))
part4=chr(int(("0x" + sys.argv[3]),0))
part5=chr(int(("0x" + sys.argv[4]),0))
part6=chr(int(("0x" + sys.argv[5]),0))

ord=part1+part2+part3+part4+part5+part6

time.sleep(0.15)
ser.setRTS(True)
time.sleep(0.03)
ser.write(ord)
time.sleep(0.03)
ser.flushInput()
ser.setRTS(False)
state=ser.read(8)
time.sleep(0.1)

ser.close()
#print int(sys.argv[3],16)

sql="""update persianas
          set posicion1=%s, posicion2=%s, posicion3=%s, posicion4=%s
        where codigo=%s;""" % (int(sys.argv[2],16), int(sys.argv[3],16), int(sys.argv[4],16), int(sys.argv[5],16), sys.argv[1])

cur.execute(sql)
conn.commit()
conn.close()
