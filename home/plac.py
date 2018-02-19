import psycopg2
import pws

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()
sql="select temp from placas order by created_at desc limit 1;"

cur.execute(sql)

for texto in cur.fetchall():
	print texto[0]
conn.close()
