import re
import couchdb

fields = {
	0: 'id',
	1: 'type',
	2: 'title',
	3: 'url',
	4: 'work_date',
	5: 'artist',
	6: 'uploader',
	7: 'post_id',
	8: 'upload_date',
	9: 'foreign_key',
}
artworks_file = 'artworks.txt'
paran_pattern = '\\((.*?)\\)'
split_pattern = '''((?:[^,"']|"[^"]*"|'[^']*')+)'''
bad_chars = "()\n"
output = {}
db_name = "rawwar_raw"
db_doc = "artworks_raw"
couch = couchdb.Server()
db = couch[db_name]

def extract_string(s, p):
	db_str = p.match(s).group()
	for i in bad_chars:
		db_str = db_str.replace(i,"")
	return db_str	

if __name__ == "__main__":
	a_file = open(artworks_file, 'r')
	pattern = re.compile(paran_pattern)
	lines = a_file.readlines()
	artist_lines = []
	for i in lines:
		artist_lines.append(extract_string(i, pattern))
	for i in artist_lines:
		s_p = re.compile(split_pattern)
		PATTERN = re.compile(r'''((?:[^,"']|"[^"]*"|'[^']*')+)''')
		#cut_str = i.split(', ')
		cut_str = PATTERN.split(i)
		#print cut_str
		key = cut_str[1]
		count = 1
		data = {}
		for j in cut_str[2:-1]:
			#print "setting " + fields[count] + " to " + j
			if(j == ','):
				#print "passing"
				continue
			#print "past pass"
			#print j
			data[fields[count]] = j
			count = count + 1
		output[key] = data
	print output
	db[db_doc] = output