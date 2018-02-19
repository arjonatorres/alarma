import psycopg2
import sys
import pws

conn = psycopg2.connect(host='localhost', dbname='jose', user='jose', password=pws.db)
cur = conn.cursor()

#sql="select * from placas where created_at::date = '%s'::date and created_at::date = '%s'::date;" % (sys.argv[1], sys.argv[1])
sql="""select posicion1, posicion2, posicion3, posicion4
        from persianas"""

cur.execute(sql)
#row = cur.fetc()
posiciones = cur.fetchall()
print posiciones
ps1=posiciones[0][1]
#ps2=posiciones[1]
#ps3=posiciones[2]
#ps4=posiciones[3]
print ps1
#sum = ps1+ps2+ps3+ps4
#print sum
#if (ps1==4):
#	print "Es igual"
#else:
#	print "No es igual"

#for texto in cur.fetchall():
#	print (texto[0])


conn.commit()
conn.close()
