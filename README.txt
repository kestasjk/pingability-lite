Pingability - lite (v1)
-----------------------
chris@kuliukas.com - 2013-03-02

This is a simple, lightweight utility intended to substitute for the pingability.com functionality which 
alerts you when your website is down. At $10/mo it costs more than my non-dedicated hosting plan, which 
likely comes down to the use of text messages / automated phone service. 
Since I check my e-mail often enough I can do without these, especially since it isn't worth $10/mo.

The script requires SQLite3, cURL, PHP5.3+ (probably), and an SMTP based mail sender (e.g. GMail).


settings.php contains various settings; the URL which is to be checked, the pattern it will look for in 
the page, the code which indicates the website has no issues, the person to e-mail if there are issues, 
the e-mail server configuration, etc.

check.php is the main script. It runs a check via check.php?runCheck=on , and if run without this it will
output a log of all recent events.
Recent events are logged into a SQLite3 database which is kept in the same folder.

runCheck.sh is a sample shell script which invokes check.php?runCheck=on

See AGPL.txt for the license.