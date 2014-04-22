<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP StadisticsController
 * @author Jorge Moreno
 */
class StadisticsController extends AppController {
    public $components = array('RequestHandler');
    public function index($id) {
        
    }
    /**
     * Obtiene las estadisticas de un juego en especifico
     * direccion: stadistics/getstadistics.xml
     * Parametros:
     * -->  idGame
     * Respuesta:
     * -->  Stadistic:
     *          informacion
     * -->  Game:
     *      -->Local
     *          nombre
     *      -->Visitante
     *          nombre     
     */
    public function getstadistics() 
    {
        $this->layout="webservice";
        $idGame=$this->request->data["idGame"];
        $options=array(
          "conditions"=> array (
              "Stadistic.game_id"=>$idGame
          ),
          "recursive"=>2
        );
        $datos=  $this->Stadistic->find("all",$options);
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }
   

}
