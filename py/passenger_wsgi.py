import imp
import os
import sys


sys.path.insert(0, os.path.dirname(__file__))

wsgi = imp.load_source('wsgi', 'public_html/py/index.py')
application = wsgi.public_html/py/index.py
