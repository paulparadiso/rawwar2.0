import re

artists_file = 'artists.txt'
paran_pattern = '\\((.*?)\\)'

def extract_string(s, p):
	return p.match(s).group()

if __name__ == "__main__":
	a_file = open(artists_file, 'r')
	pattern = re.compile(paran_pattern)
	lines = a_file.readlines()
	artist_lines = []
	for i in lines:
		artist_lines.append(extract_string(i, pattern))
	for i in artist_lines:
		print i