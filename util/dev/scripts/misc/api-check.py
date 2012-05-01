#!/usr/bin/python

import urllib
import urllib2
import time

url = "http://biz.binaryvibes.co.in/webservice/messagequeue"
bizsense_api_key = 'd38326c24292937f2bc645839dbaa7f11ea1bd30'

params = {
    'custom_content': 1,
    'first_name': 'a',
    'middle_name': None,
    'last_name': 'b',
    'email': 'rtest1@rexample700.com',
    'format': 1,
    'custom_text_body': 'test body',
    'custom_html_body': 'test html',
    'custom_subject' : 'test subject'
}

request = urllib2.Request(url = url, data = urllib.urlencode(params.items()))
request.add_header('X-Bizsense-Apikey', bizsense_api_key)
try:
    result = urllib2.urlopen(request)
    print result.geturl()
    print result.info()
    print result.getcode()
    print result.read()
except urllib2.HTTPError, e:
    print e.getcode()
    print e.read()


