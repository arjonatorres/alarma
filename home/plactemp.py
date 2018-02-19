import psycopg2
import pws

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()
sql="insert into placas (temp) values (%d);" % (25)
print sql
cur.execute(sql)

sql="select temp from placas;"

cur.execute(sql)
#row = cur.fetc()

for texto in cur.fetchall():
	print (texto[0])
conn.commit()
conn.close()
