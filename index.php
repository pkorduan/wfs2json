<?php
  $config_file = 'conf/' . ($_REQUEST['c'] != '' ? $_REQUEST['c'] : 'constant.ini');

  if (!file_exists($config_file)) {
    echo 'Konfigurationsdatei: ' . $config_file . ($_REQUEST['c'] != '' ? ' aus dem Parameter c' : '') . ' nicht gefunden.';
    exit;
  }

  $config = parse_ini_file($config_file, true);
  if ($_REQUEST['type_name'] != '') {
    $config['wfs']['featureType'] = $_REQUEST['type_name'];
  }

  $versorgungsart = '';
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
<h2>WFS Online-Resource</h2>
<?php
  $get_capabilities_request =
    $config['wfs']['url'] .
    'SERVICE=WFS&' .
    'REQUEST=GetCapabilities&' .
    'VERSION=1.0.0';
?>
Read the Capabilities <a href="<?php echo $get_capabilities_request; ?>" target="_blank">here</a>.
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
?>
This file is now available for download <a href="<?php echo $config['wfs']['webPath'] . $config['wfs']['fileName']; ?>" target="_blank">here</a>.
<h2>Convert and Save JSON</h2>
Lese Datei <?php echo getcwd() . '/' . $config['wfs']['localPath'] . $config['wfs']['fileName']; ?>,<br>
und speicher die konvertierte JSON Datei <?php echo getcwd() . '/' . $config['json']['localPath'] . $config['json']['fileName']; ?><br>
<?php
  $wfs_text = file_get_contents(
    getcwd() .
    '/' .
    $config['wfs']['localPath'] .
    $config['wfs']['fileName']
  );
  
  $sxe = simplexml_load_string($wfs_text);

  $output = '<pre>';
  $featureMembers = $sxe->children('gml', true);
  $items = array();
  foreach($featureMembers AS $featureMember) {
    $item = array();
    if ($featureMember->getName() == 'featureMember') {
      $output .= '<hr>';
      $feature = $featureMember->children($config['wfs']['ns'], true);
      foreach ($feature->attributes() AS $key => $value) {
        #echo '<br>Attribute: ' . $key . ' = ' . $value;
      }
      $attributes = $feature->children($config['wfs']['ns'], true);
      foreach($attributes AS $attribute) {
        switch ($attribute->getName()) {
          case "msGeometry":
            $point = $attribute->children('gml', true);
            $coordinates= $point->children('gml', true);
            $pair = explode(',', (String)$coordinates);
            $output .= 'lat: ' . $pair[0] . '<br>';
            $item['x'] = $pair[0];
            $output .= 'lon: ' . $pair[1];
            $item['y'] = $pair[1];
            break;
          case "the_geom":
            $point = $attribute->children('gml', true);
            $coordinates= $point->children('gml', true);
            $pair = explode(',', (String)$coordinates);
            $output .= 'lat: ' . $pair[0] . '<br>';
            $item['x'] = $pair[0];
            $output .= 'lon: ' . $pair[1];
            $item['y'] = $pair[1];
            break;
          case "angebot":
            if ((String)$attribute == "") {
              $angebot = "Sonstiges";
              $kategorie = "st";
            }
            else {
              $angebot = trim((String)$attribute);
              $kategorie = ($versorgungsart == 'Arzt' ? 'az' : $config['sozialpflege']['kategorie'][$angebot]);
            }
            $output .= 'angebot = ' . $angebot . '<br>';
            $output .= 'kategorie = ' . $kategorie;
            $item['angebot'] = $angebot;
            $item['kategorie'] = $kategorie;
            break;
          default:
            $value = (String)$attribute;
            if ($attribute->getName() == 'versorgungsart') {
              $versorgungsart = $value;
              if ($versorgungsart == 'Arzt') {
                $value = 'Gesundheit';
              }
            }
            $output .= $attribute->getName() . ' = ' . $value;
            $item[$attribute->getName()] = $value;
        }

        $output .= '<br>';
      }
      
/*      if ($item['einrichtung'] == "")
        if ($item['traeger'] == "")
          if ($item['eigentuemer'] == "")
            if ($item['ansprechpartner'] == "")
              $name = $item['angebot'];
            else
              $name = $item['ansprechpartner'];
          else
            $name = $item['eigentuemer'];
        else
          $name = $item['traeger'];
      else
        $name = $item['einrichtung'];
      $output .= 'name = ' . $name;
      $item['name'] = $name;
*/
      if (!is_null($item['kategorie'])) {
        array_push($items, $item);
      }
    }
  }
  $output .= '</pre>';
  $json_text = json_encode($items);
  file_put_contents(
    getcwd() .
    '/' .
    $config['json']['localPath'] .
    $config['json']['fileName'],
    $json_text
  ); ?>
  This file is now available for download <a href="<?php echo $config['json']['webPath'] . $config['json']['fileName']; ?>" target="_blank">here</a>.
  <?php #var_dump($items); ?>
  <?php echo $output; ?>
</body>
</html>