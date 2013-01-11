import web
#import couchdb

urls = (
	'/', 'Archive',
	'/archive', 'Archive',	
	'/submit', 'Submit',
	'/events', 'Events',
	'/view', 'View',
	'/about', 'About',
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

if __name__ == "__main__":
	app = web.application(urls, globals())
	app.run()