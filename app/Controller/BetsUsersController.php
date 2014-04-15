<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP Bets_UsersController
 * @author Jorge Moreno
 */
class BetsUsersController extends AppController {
    public $components = array('RequestHandler');
    public function index($id) {
        
    }
    /**
     * Lista las pollas a las que pertenece un usuario
     * direccion: bets_users/getbetsbyuser.xml
     * Parametros:
     * -->  idUsuario
     * Respuesta:
     * -->datos
     *      -->Bet
     *          id
     *          nombre
     */
    public function getbetsbyuser()
    {
        $this->layout="webservice";
        $idUsuario=$this->request->data["idUsuario"];
        
        $options=Array(
            "fields"=>array("Bet.id","Bet.nombre"),
            "conditions"=>array("BetsUser.user_id"=>$idUsuario)
        );
        $datos=  $this->BetsUser->find('all', $options);
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }

}
