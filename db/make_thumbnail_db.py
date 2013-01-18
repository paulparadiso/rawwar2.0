import re
import couchdb

fields = {
	0: 'id',
	1: 'post_id',
	2: 'url',
	3: 'width',
	4: 'height',
	5: 'time_offset',
}
artworks_file = 'thumbnails.txt'
paran_pattern = '\\((.*?)\\)'
split_pattern = '''((?:[^,"']|"[^"]*"|'[^']*')+)'''
bad_chars = "()'\n"
output = {}
db_name = "rawwar_raw"
db_doc = "thumbnails_raw"
couch = couchdb.Server()
db = couch[db_name]

def extract_string(s, p):
	db_str = p.match(s).group()
	for i in bad_chars:
		db_str = db_str.replace(i,"")
	print db_str
	return db_str	

if __name__ == "__main__":
	a_file = open(artworks_file, 'r')
	pattern = re.compile(paran_pattern)
	lines = a_file.readlines()
	artist_lines = []
	for i in lines:
		artist_lines.append(extract_string(i, pattern))
	for i in artist_lines:
		cut_str = i.split(', ')
		key = cut_str[0]
		count = 1
		data = {}
		for j in cut_str[1:]:
			#print "setting " + fields[count] + " to " + j
			if(j == ','):
				#print "passing"
				continue
			#print "past pass"
			print j
			data[fields[count]] = j
			count = count + 1
		output[key] = data
	print output
	db[db_doc] = output