import MySQLdb as mdb
from MySQLdb import converters
import couchdb

conv = converters.conversions.copy()
conv[10] = str
con = mdb.connect('localhost','root', '', 'rawwar', charset='utf8', conv=conv)
cur = con.cursor(mdb.cursors.DictCursor)
db_name = "rawwar_raw"
couch = couchdb.Server()
db = couch[db_name]
thumbnail_template = "http://i.ytimg.com/vi/%s/default.jpg"

def get_records(table, key, value):
	query = 'SELECT * FROM %s WHERE %s = "%s"' % (table, key, value	)
	#print query
	cur.execute(query)
	t_rows = cur.fetchall()
	records = []
	for r in t_rows:
		records.append(r)
	return records

def get_table(table):
	query = "SELECT * FROM %s" % (table)
	cur.execute(query)
	return cur.fetchall()

if __name__ == "__main__":
	artworks = get_table('artworks')
	output = {}
	count = 0
	for i in artworks:
		if i['type'] == 'video':
			#print "POST ID = " + str(i['post_id'])
			if i['post_id'] != None:
				thumbnail = thumbnail_template % (i['foreign_key'])
				title = i['title']
				post = get_records('rw_posts', 'ID', i['post_id'])
				if len(post) > 0:
					status = post[0]['post_status']
					description = post[0]['post_content']
				else:
					status = "NO POST"
					description = "NO DESCRIPTION"
				artist_name = get_records('artists', 'id', i['artist'])[0]['name']
				work_date = i['work_date']
				if(status == "publish"): 
					entry = {'title': title, 
							 'name': artist_name, 
							 'work_date': work_date, 
							 'thumbnail_url': thumbnail,
							 'foreign_key': i['foreign_key'],
							 'description': description,
							 }
					print entry
					#print "__________________________________"
					#print "\t" + work_date
					#print "\t" + thumbnail
					#print "\t" + title
					#print "\t" + artist_name
					output[str(count)] = entry
					count = count + 1
					#if(count > 5):
					#	break			
	print "added %d records." % (count)
	if 'archive_raw' in db:
		arch = db['archive_raw']
		db.delete(arch)
	db['archive_raw'] = output
	#print output