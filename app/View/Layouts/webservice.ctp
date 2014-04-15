<?php
//And use the following code if you want to allow access from any host that does not belong to your rest server domain.
$this->response->header('Access-Control-Allow-Origin','*');
$this->response->header('Access-Control-Allow-Methods','POST');
$this->response->header('Access-Control-Allow-Headers','X-Requested-With');
$this->response->header('Access-Control-Max-Age','172800');
$this->response->type('xml');
$xml = Xml::fromArray(array('response' => $datos));
echo $xml->asXML();
echo $this->fetch('content');
?>