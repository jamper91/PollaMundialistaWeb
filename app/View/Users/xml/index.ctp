$xml = Xml::fromArray(array('response' => $recipes));
echo $xml->asXML();