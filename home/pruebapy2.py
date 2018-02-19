import psycopg2
import sys
import pws

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()

#sql="select * from placas where created_at::date = '%s'::date and created_at::date = '%s'::date;" % (sys.argv[1], sys.argv[1])
sql="""select temp
        from placas
        where extract (day from '%s'::date) = extract (day from created_at) and
        extract (month from '%s'::date) = extract (month from created_at) and
        extract (year from '%s'::date) = extract (year from created_at)
        order by created_at desc
        limit 24;""" % (sys.argv[1], sys.argv[1], sys.argv[1])

cur.execute(sql)
#row = cur.fetc()

for texto in cur.fetchall():
	print (texto[0])

conn.commit()
conn.close()
