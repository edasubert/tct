#!/usr/bin/env python

from urllib2 import urlopen, Request, HTTPError
from urllib import urlencode
from time import sleep
import json
import sys

src=sys.argv[1]
tgt=sys.argv[2]

urlbase = "https://www.googleapis.com/language/translate/v2/"
data = {
    'key' : 'EMPTY',
    'source' : src,
    'target' : tgt,
}

headers = {
    'X-HTTP-Method-Override' : 'GET',
}

lineid = 0
for line in sys.stdin:
    lineid += 1
    data['q'] = line
    success = False
    while not success:
        try:
            out = urlopen(Request(urlbase, urlencode(data), headers)).read()
            parsed = json.loads(out)
            print parsed['data']['translations'][0]['translatedText']
            success = True
        except HTTPError as err:
            if err.code == 403:
                print >>sys.stderr, "Error 403, user limit probably reached. Aborting"
                sys.exit(1)
            print >>sys.stderr, "Warning: request failed on line " + str(lineid) + " (code " + str(err.code) + "), trying again..."
            sleep(5)
