import psycopg2
import sys
import pws

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()

sql="select * from placas where id = %s;" % (sys.argv[1])

cur.execute(sql)
#row = cur.fetc()

for texto in cur.fetchall():
	print (texto[0])
	print (texto[1])
	print (texto[2])


conn.commit()
conn.close()
