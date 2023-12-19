# Time Zone Field

Time Zone Field (tzfield) provides a select field for storing time zones. It
could be useful if, for example, you have a node type representing a location
such as city, office, station, etc., and you wish to associate a time zone with
these nodes. Time zone data is stored in the standard [tz
database](https://en.wikipedia.org/wiki/Tz_database) format, e.g.
`Europe/London`.


## Requirements

No special requirements.


## Installation

Install as you would normally install a contributed Drupal module.


## Configuration

For each time zone field you create, you can configure any time zones to exclude
from the allowed values, choose the form widget (either time zone grouped by
region or time zone with current offset), and choose the display formatter
(either the time zone identifier, e.g. "Europe/London"; or the formatted current
date, e.g. "GMT" if the [date format
string](https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters)
is "T").


## Maintainers

This module is maintained by [mfb](https://www.drupal.org/u/mfb).

You can support development by
[contributing](https://www.drupal.org/project/issues/tzfield) or
[sponsoring](https://github.com/sponsors/mfb).
