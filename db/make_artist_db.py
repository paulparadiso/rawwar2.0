import re
import couchdb

fields = {
	0: 'id',
	1: 'name',
	2: 'dob',
	3: 'bio',
}
artists_file = 'artists.txt'
paran_pattern = '\\((.*?)\\)'
bad_chars = "()'\n"
output = {}
db_name = "rawwar_raw"
db_doc = "artists_raw"
couch = couchdb.Server()
db = couch[db_name]

def extract_string(s, p):
	db_str = p.match(s).group()
	for i in bad_chars:
		db_str = db_str.replace(i,"")
	return db_str	

if __name__ == "__main__":
	a_file = open(artists_file, 'r')
	pattern = re.compile(paran_pattern)
	lines = a_file.readlines()
	artist_lines = []
	for i in lines:
		artist_lines.append(extract_string(i, pattern))
	for i in artist_lines:
		cut_str = i.split(',')
		key = cut_str[0]
		count = 1
		data = {}
		for j in cut_str[1:]:
			#print j
			#print "setting " + fields[count] + " to " + j
			data[fields[count]] = j
			count = count + 1
		output[key] = data
	print output
	db[db_doc] = output