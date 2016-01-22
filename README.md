# wfs2json
PHP Script that request a Web Feature Service (WFS) and convert the received GML-Data to a JSON File.
It is designed to convert a GML-File like [this](http://geoportal.kreis-lup.de/wfs/pflegeportal/sp?SERVICE=WFS&REQUEST=GetFeature&VERSION=1.0.0&TYPENAME=pflegeportal:Sozialpflege) with information about care services in to a JSON file like [this](http://www.gdi-service.de/wfs2json/json/data.json).

It can easily be changed to convert also other simple GML application schemas.

May be it will change later to a more generic way.

To configure the output for filter or translation of values, copy constants_sample.ini to constants.ini and add what ever you whant to configure.
