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
//        $nombre=$this->request->data['nombre'];
//        $apellido=$this->request->data['apellido'];
//        $nick=$this->request->data['nick'];
//        $email=$this->request->data['email'];
//        $pass=$this->request->data['pass'];
//        $parametros=array(
//            
//        );
//            $datos= $this->Almacene->query($sql);
//            $this->set(array(
//                'datos' => $datos,
//                '_serialize' => array('datos')
//            ));
    }

}
