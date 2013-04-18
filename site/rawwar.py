#!/usr/bin/env python

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
	'/contact', 'Contact',
	'/fetch', 'Fetch',
)

render = web.template.render('templates')
	
class Index:
	
	def GET(self):
		pass

# archive.html

class Archive:

	def GET(self):
		header = render.header('archive')
		footer = render.footer()
		archive = render.archive(header, footer)
		return archive

# submit.html

class Submit:

	def GET(self):
		header = render.header('submit')
		footer = render.footer()
		submit = render.submit(header, footer)
		return submit

	def POST(self):
		params = web.input()
		self._save_submission(params)
		header = render.header('submit')
		footer = render.footer()
		thanks = render.thanks(header, footer)
		return thanks

	def _save_submission(self, s):
		print s

# events.html

class Events:

	def GET(self):
		header = render.header('events')
		footer = render.footer()
		events = render.events(header, footer)
		return events

# view.html

class View:

	def GET(self):
		header = render.header('view')
		footer = render.footer()
		view = render.view(header, footer)
		return view

# about.html

class About:

	def GET(self):
		header = render.header('about')
		footer = render.footer()
		about = render.about(header, footer)
		return about

# contact.html

class Contact:

	def GET(self):
		header = render.header('')
		footer = render.footer()
		contact = render.contact(header, footer)
		return contact

# fetch all items in the database.

class Fetch:

	def POST(self):
		return json.dumps(archive_output)


if __name__ == "__main__":
	app = web.application(urls, globals())
	app.run()