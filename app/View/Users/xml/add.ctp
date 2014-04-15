$xml = Xml::fromArray(array('response' => $datos));
echo $xml->asXML();