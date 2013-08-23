MediaWiki-SumTimes
==================

MediaWiki extension to easily perform addition of times in the form hh:mm or hh:mm:ss (not supported initially) using a parser function in the form:

{{#sumtimes: 12:34, 43:23, 23:24, 05:23, 2:23 }}

hh:mm:ss could be supported (or even ddd:hh:mm:ss, yy:ddd:hh:mm:ss, etc) by adding a parameter "format" specifying the components of the date/time string using the [PHP date standards](http://php.net/manual/en/function.date.php):

{{#sumtimes: 12:34, 43:23, 23:24, 05:23, 2:23 | format = h:i }} (see value for "i" in PHP date standards)

{{#sumtimes: 12:34, 43:23, 23:24, 05:23, 2:23 | format = i:s }}
