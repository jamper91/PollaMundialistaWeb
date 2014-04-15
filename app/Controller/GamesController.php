<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP GamesController
 * @author Jorge Moreno
 */
class GamesController extends AppController {
    public $components = array('RequestHandler');
    public function index($id) {
        
    }
    /**
     * Retorna todos los partidos en la base de datos
     * direccion:   games/getgames.xml
     * Parametros
     *      Ninguno
     * Respuesta:
     * -->datos
     *      -->Game
     *          id
     *          local
     *          visitante
     *          goles_local
     *          goles_visitante
     *          fecha
     *          finalizo
     */
    public function getgames() 
    {
        $this->layout="webservice";
        $datos=  $this->Game->find("all");
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }

}
