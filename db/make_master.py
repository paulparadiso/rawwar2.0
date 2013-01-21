import couchdb

db_name = 'rawwar_raw'
couch = couchdb.Server()
db = couch[db_name]
artworks = db['artworks_table']
artists = db['artists_table']
posts = db['posts_table']
terms = db['terms_table']
relationships = db['term_relationships_table']
uploaders = db['uploaders_table']

if __name__ == "__main__":
	for a in artworks.keys():
		if a == '_rev' or a == '_id':
			continue
		post_id = str(artworks[a]['post_id'])
		if post_id == 'None':
			continue
		post = posts[post_id]
		if post == None:
			continue
		artist = artworks[a]['artist']
		cats = []
		for k in relationships.keys():
			if k == '_rev' or k == '_id':
				continue
			#print k
			if relationships[k]['object_id'] == int(artworks[a]['post_id']):
				cats.append(relationships[k])
		for c in cats:
			c['name'] = terms[str(c['term_taxonomy_id'])]['name']
		print artworks[a]['title']
		for c in cats:
			print '\t' + c['name']
