import re
import couchdb

fields = {
	0: 'id',
	1: 'post_author',
	2: 'post_date',
	3: 'post_date_gmt',
	4: 'post_content',
	5: 'post_title',
	6: 'post_excerpt',
	7: 'post_status',
	8: 'comment_status',
	9: 'ping_status',
	10: 'post_password',
	11: 'post_name',
	12: 'to_ping',
	13: 'pinged',
	14: 'post_modified',
	15: 'post_modified_gmt',
	16: 'post_content_filtered',
	17: 'post_parent',
	18: 'guid',
	19: 'menu_order',
	20: 'post_type',
	21: 'post_mime_type',
	22: 'comment_count',
}
artworks_file = 'posts.txt'
paran_pattern = '\\((.*?)\\)'
split_pattern = '''((?:[^,"']|"[^"]*"|'[^']*')+)'''
bad_chars = "()\n"
output = {}
db_name = "rawwar_raw"
db_doc = "posts_raw"
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
			#print "setting " + fields[count] + " to " + j
			data[fields[count]] = j
			count = count + 1
		output[key] = data
	print output
	#db[db_doc] = output