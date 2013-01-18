import web
import couchdb
import json

db_name = "rawwar_raw"
couch = couchdb.Server()
db = couch[db_name]

archive_db = db['archive_raw_cleaned']
archive_output = []
for i in archive_db.keys():
	if i == '_rev' or i == '_id':
		continue
	archive_output.append(archive_db[i])

urls = (
	'/', 'Archive',
	'/archive', 'Archive',	
	'/submit', 'Submit',
	'/events', 'Events',
	'/view', 'View',
	'/about', 'About',
	'/fetch', 'Fetch',
)

render = web.template.render('templates')
	
class Index:
	
	def GET(self):
		pass

class Archive:

	def GET(self):
		header = render.header('archive')
		archive = render.archive(header)
		return archive

class Submit:

	def GET(self):
		header = render.header('submit')
		submit = render.submit(header)
		return submit

class Events:

	def GET(self):
		header = render.header('events')
		events = render.events(header)
		return events

class View:

	def GET(self):
		header = render.header('view')
		view = render.view(header)
		return view

class About:

	def GET(self):
		header = render.header('about')
		about = render.about(header)
		return about

class Fetch:

	def GET(self):
		return json.dumps(archive_output)


if __name__ == "__main__":
	app = web.application(urls, globals())
	app.run()