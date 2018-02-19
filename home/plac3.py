import psycopg2
import pws

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()
sql="""select temp
	from placas
	where extract (day from (current_timestamp - '1 day'::interval) = extract (day from created_at) and
	extract (month from (current_timestamp - '1 day'::interval) = extract (month from created_at) and
	extract (year from (current_timestamp - '1 day)::interval) = extract (year from created_at)
	order by created_at desc
	limit 24;"""

cur.execute(sql)

for texto in cur.fetchall():
	print (texto[0])
conn.close()
