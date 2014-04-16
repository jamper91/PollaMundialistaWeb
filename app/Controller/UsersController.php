<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP UsersController
 * @author Jorge Moreno
 */
class UsersController extends AppController {
    public $components = array('RequestHandler');
    public function index()
    {
        $datos=  $this->User->find("all");
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }
    /**
     * Objeto devueltos
     * direccion: http://localhost/PollaMundialistaWeb/users/add.xml
     * parametros:
     * -->  nombres
     * -->  apellidos
     * -->  nick
     * -->  email
     * -->  pass
     * respuesta
     *      datos -> Id del perfil creado
     */
    public function add()
    {
          $this->layout="webservice";
          if(!empty($this->request->data))
          {
               $resul= $this->User->save($this->request->data);
               $datos= $this->User->id;
               $this->set(array(
                    'datos' => $datos,
                    '_serialize' => array('datos')
                ));
               
          }

    }
    
    /**
     * Se encarga de verificar si un usuario existe en el sistema con esos datos
     * direccion: users/login.xml
     * Parametros:
     * -->  nick
     * -->  pass
     * Respuesta
     * -->  datos
     *      -->User
     *          id
     *          nombres
     *          apellidos
     *          nick
     *          email
     *          pass
     *
     */
    public function login()
    {
        $nick=$this->request->data['nick'];
        $pass=$this->request->data['pass'];
        $parametros=array(
            'conditions'=>array("nick"=>$nick,"password"=>$pass)
        );
        $datos=  $this->User->find("all",$parametros);
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }

}
