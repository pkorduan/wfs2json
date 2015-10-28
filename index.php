<?php
  $config = parse_ini_file('constant.ini', true);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>WFS to JSON Converter</title>
</head>
<body>
<h1>Hello, welcome to the WFS to JSON Converter.</h1>
<h2>Content of ini file.</h2>
<pre>
<?php
  print_r($config);
?>
</pre>
<h2>Request WFS</h2>
<?php
  $get_feature_request =
    $config['wfs']['url'] .
    'SERVICE=WFS&' .
    'REQUEST=GetFeature&' .
    'VERSION=1.0.0&' .
    'TYPENAME=' . $config['wfs']['featureType'];
?>
Read GML-Data from WFS with URL<br><a href="<?php echo $get_feature_request; ?>" target="_blank"><?php echo $get_feature_request; ?></a><br>and store the content on the servers filesystem at<br><?php echo getcwd() . '/' . $config['wfs']['localPath'] . $config['wfs']['fileName']; ?><br>
<?php
/*
  $wfs_file = file_get_contents(
    $get_feature_request
  );
  file_put_contents(
    getcwd() .
    '/' .
    $config['wfs']['localPath'] .
    $config['wfs']['fileName'],
    $wfs_file
  );
*/
?>
This file is now available for download <a href="<?php echo $config['wfs']['webPath'] . $config['wfs']['fileName']; ?>" target="_blank">here</a>.
<h2>Convert and Save JSON</h2>
Lese Datei <?php
echo getcwd() . '/' . $config['wfs']['localPath'] . $config['wfs']['fileName'];
?><br>
<?php
  $wfs_text = file_get_contents(
    getcwd() .
    '/' .
    $config['wfs']['localPath'] .
    $config['wfs']['fileName']
  );
  
  $sxe = simplexml_load_string($wfs_text);

  ?><pre><?php
  #echo $sxe->asXml();
  $featureMembers = $sxe->children('gml', true);
  foreach($featureMembers AS $featureMember) {
    if ($featureMember->getName() == 'featureMember') {
      echo '<hr>';
      $feature = $featureMember->children('pflegeportal', true);
      echo 'kategorie: ' . $feature->getName() . '<br>';
      $attributes = $feature->children('pflegeportal', true);
      foreach($attributes AS $attribute) {
        if ($attribute->getName() == 'msGeometry') {
          $point = $attribute->children('gml', true);
          $coordinates= $point->children('gml', true);
          $latlngs = explode(',', (String)$coordinates);
          echo 'lat: ' . $latlngs[0] . '<br>';
          echo 'lon: ' . $latlngs[1];
        }
        else {
          echo $attribute->getName() . ' = ' . (String)$attribute;
        }
        echo '<br>';
      }
    }
  }
  ?></pre><?php
  /*
  $p = xml_parser_create();
  xml_parse_into_struct($p, $xml, $vals, $index);
  xml_parser_free($p);
  ?><pre><?php
  echo "Index array\n";
  print_r($index);
  echo "\nVals array\n";
  print_r($vals);
  ?></pre><?php
  */
  
/*  
$xml = '<?xml version="1.0" encoding="UTF-8" ?>
<wfs:FeatureCollection
   xmlns:pflegeportal="http://geoportal.kreis-lup.de/regismv/"
   xmlns:wfs="http://www.opengis.net/wfs"
   xmlns:gml="http://www.opengis.net/gml"
   xmlns:ogc="http://www.opengis.net/ogc"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd 
                       http://geoportal.kreis-lup.de/regismv/ http://geoportal.kreis-lup.de/wfs/pflegeportal/sp&amp;SERVICE=WFS?SERVICE=WFS&amp;VERSION=1.0.0&amp;REQUEST=DescribeFeatureType&amp;TYPENAME=pflegeportal:Sozialpflege&amp;OUTPUTFORMAT=XMLSCHEMA">
  <gml:featureMember>
    <pflegeportal:Sozialpflege fid="3">Chapter 2</pflegeportal:Sozialpflege>
  </gml:featureMember>
  <gml:featureMember>
    <pflegeportal:Sozialpflege fid="4">
      <pflegeportal:id>3</pflegeportal:id>
      <pflegeportal:versorgungsart>Gesundheit</pflegeportal:versorgungsart>
    </pflegeportal:Sozialpflege>
  </gml:featureMember>
</wfs:FeatureCollection>';

$sxe = new SimpleXMLElement($xml);

#$sxe->registerXPathNamespace('c', 'http://www.opengis.net/gml');
$features = $sxe->xpath('//pflegeportal:Sozialpflege');
foreach ($features as $feature) {
  print_r($feature);
  echo "<br>";
  $children = $feature->children();
  echo 'children: ';
  print_r($children);
  echo "<br>";
}*/
?>
</body>
</html>