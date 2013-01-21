import sql
import couchdb
import datetime

tables = {
	'rw_posts':{'couch_name':'posts_table','key':'ID'},
	'artworks':{'couch_name':'artworks_table','key':'id'},
	'artists':{'couch_name':'artists_table','key':'id'},
	'rw_terms':{'couch_name':'terms_table','key':'term_id'},
	'rw_term_relationships':{'couch_name':'term_relationships_table','key':'count'},
	'uploaders':{'couch_name':'uploaders_table','key':'id'},
	'thumbnails':{'couch_name':'thumbnails_table','key':'cache_id'},
	#'video_xml_cache':{'couch_name':'video_xml_cache_table','key':'foreign_key'},
}

def make_dict(l, key):
	output = {}
	count = 0
	for i in l:
		for k in i.keys():
			if isinstance(i[k], datetime.datetime):
				i[k] = i[k].strftime("%c")
		if(key == 'count'):
			output[str(count)] = i
			count = count + 1
		else:
			output[i[key]] = i	
	return output

if __name__ == "__main__":
	db_name = 'rawwar_raw'
	couch = couchdb.Server()
	db = couch[db_name]
	for k in tables.keys():
		print k
		table = sql.get_table(k)
		if tables[k]['couch_name'] in db:
			d = db[tables[k]['couch_name']]
			db.delete(d)
		db[tables[k]['couch_name']] = make_dict(table, tables[k]['key'])

	

