import couchdb

db_name = "rawwar_raw"
artist_doc = "artists_raw"
artwork_doc = "artworks_raw"
post_doc  = "posts_raw"
couch = couchdb.Server()
db = couch[db_name]
artists_doc = db[artist_doc]
artworks_doc = db[artwork_doc]
posts_doc = db[post_doc]
 
def fetch_videos():
	r_artworks = []
	for i in artworks_doc.keys():
		if i == "_rev" or i == "_id":
			continue
		if 'type' in artworks_doc[i].keys():
			if artworks_doc[i]['type'] == 'video':
				r_artworks.append(artworks_doc[i])
	return r_artworks

def fix_urls():
	for i in artworks_doc.keys():
		if i == "_rev" or i == "_id":
			continue
		if 'url' in artworks_doc[i].keys():
			artworks_doc[i]['url'] = artworks_doc[i]['url'].replace(" ", "")
			artworks_doc[i]['url'] = artworks_doc[i]['url'].replace("'", "")
	db[artwork_doc] = artworks_doc

def fix_post_ids():
	for i in artworks_doc.keys():
		if i == "_rev" or i == "_id":
			continue
		if 'post_id' in artworks_doc[i].keys():
			artworks_doc[i]['post_id'] = artworks_doc[i]['post_id'].lstrip()
	db[artwork_doc] = artworks_doc

def fix_post_content():
	for i in artworks_doc.keys():
		if i == "_rev" or i == "_id":
			continue
		if 'post_id' in artworks_doc[i].keys():
			artworks_doc[i]['post_id'] = artworks_doc[i]['post_id'].lstrip()
	db[artwork_doc] = artworks_doc

if __name__ == "__main__":
	#fix_urls()
	#fix_titles()
	#fix_post_ids()
	videos = fetch_videos()
	for i in videos:
		#print i
		artist_ref = i['artist']
		artist = artists_doc[artist_ref]
		print "title = " + i['title']
		print "artist = " + artist['name']
		print "post title = " + posts_doc[i['post_id']]['post_title']