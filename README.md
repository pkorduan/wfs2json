# wfs2json
PHP Script that request a Web Feature Service (WFS) and convert the received GML-Data to a JSON File.
It is designed to convert a GML-File like [this](http://geoportal.kreis-lup.de/wfs/pflegeportal/sp?SERVICE=WFS&REQUEST=GetFeature&VERSION=1.0.0&TYPENAME=pflegeportal:Sozialpflege) with information about care services in to a JSON file like [this](http://www.gdi-service.de/wfs2json/json/data.json).

It can easily be changed to convert also other simple GML application schemas.

May be it will change later to a more generic way.

To configure the output for filter or translation of values, copy constants_sample.ini to constants.ini and add what ever you whant to configure.

## Configuration
Syntax of the configuration file is [INI](https://en.wikipedia.org/wiki/INI_file).

Section wfs defines the onlineresource and featureType of the wfs, Namespace used for feature type names and information about the location to store the downloaded GML file.

Section json define the location to store the resulting JSON file.

The feature type section defines a mapping for specific attributes if neccessary. See case "angebot" in index.php to find out how the configuration is used in the converter. The default is to convert attribute name and value as key, value pairs as they are in feature type model.
