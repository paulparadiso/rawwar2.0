import couchdb
import httplib
import urllib2

db_name = "rawwar_raw"
archive_db_orig = "archive_raw"
archive_db_edit = "archive_raw_cleaned"
couch = couchdb.Server()
db = couch[db_name]
archive_orig = db[archive_db_orig]
thumbnail_path_template = "http://i.ytimg.com/vi/%s/default.jpg"

def exists(url):
	print "testing " + url
	try:
 		f = urllib2.urlopen(urllib2.Request(url))
		exists = True
	except:
		exists = False
	return exists

if __name__ == "__main__":
	count = 0
	new_archive = {}
	for i in archive_orig.keys():
		if i == "_rev" or i == "_id":
			continue
		fk = archive_orig[i]['foreign_key']
		if exists(thumbnail_path_template % (fk)):
			print fk + " exitsts."
			new_archive[str(count)] = archive_orig[i]
			count = count + 1
		else:
			print fk + " DOES NOT exist."
	if archive_db_edit in db:
		arch = db[archive_db_edit]
		db.delete(arch)
	db[archive_db_edit] = new_archive
	print new_archive
	print "%d artworks moved to edited archive." % (count)