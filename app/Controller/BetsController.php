<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * CakePHP BetsController
 * @author Jorge Moreno
 */
class BetsController extends AppController {
    public $components = array('RequestHandler');
    public function index($id) {
        
    }
    /**
     * Agrega una polla a la base de datos
     * direccion: bets/add.xml
     * Parametros
     * -->  nombre
     * -->  premio
     * -->  informacion
     * -->  administrador: es el id dle usuario que administra la polla
     * 
     * Respuesta
     *      datos: es el id de la polla creada
     */
    public function add()
    {
        $this->layout="webservice";
          if(!empty($this->request->data))
          {
               $resul= $this->Bet->save($this->request->data);
               $datos= $this->Bet->id;
               $this->set(array(
                    'datos' => $datos,
                    '_serialize' => array('datos')
                ));
               
          }
    }
    /**
     * Se encarga de unir un usuario a una polla
     * direccion: bets/joinbet.xml
     * Parametros:
     * -->  idBet: id de la polla
     * -->  nombreBet: nombre de la polla
     * -->  idUsuario: id del usuario a unir a la polla
     * Respuesta
     *      datos:
     *          -1: Ocurrior un error, el nombre de la polla y el id no coinciden
     *          0: Todo bien
     */
    public function joinbet()
    {
        $this->layout="webservice";
        $idBet=$this->request->data["idBet"];
        $nombreBet=$this->request->data["nombreBet"];
        $idUsuario=$this->request->data["idUsuario"];
        
        $options=Array(
            "conditions"=>array("Bet.id"=>$idBet,"Bet.nombre"=>$nombreBet)
        );
        $bet=  $this->Bet->find('first', $options);
        //Verifico si existe la llave idBet nombreBet
        if(count($bet)<1)
        {
            $datos=array("-1");
        }else{
            $sql="insert into bets_users (bet_id,user_id) values($idBet,$idUsuario);";
            $this->Bet->query($sql);
            $datos=  $this->Bet->id;
               
        }
        $this->set(array(
                    'datos' => $datos,
                    '_serialize' => array('datos')
                ));
    }
    
    /**
     * Obtiene informacion de una polla en especifico
     * direccion: bets/getinfobet.xml
     * Parametros:
     * -->  idBet
     * Respuesta:
     * -->  datos
     *      -->Bet
     *          id
     *          nombre
     *          premio
     *          informacion
     *          administrador
     */
    public function getinfobet()
    {
        $this->layout="webservice";
        $idBet=$this->request->data["idBet"];
        $options=Array(
            "conditions"=>array("Bet.id"=>$idBet),
            "recursive"=>0
        );
        $datos=  $this->Bet->find('first', $options);
        $this->set(
        array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }
    public function sendinvitation() 
    {
        $idBet=$this->request->data["idBet"];
        $nombreBet=$this->request->data["nombreBet"];
        $usuarios= $this->request->data["usuarios"];
        $correos=$this->request->data["correos"];
        
        //Recorro todos los usuario y los correos
        $users = explode("-", $usuarios);
        $emails= explode("-",$correos);
        $i=0;
        foreach($users as $user)
        {
          $email=$emails[$i];
          $Email = new CakeEmail();
          $Email->config('mandrill');
          $Email->template('default', 'default');
          $Email->emailFormat('html');
          $Email->subject("Invitacion a mi polla :P");
          $Email->viewVars(array('nombreUsuario' => $user,"nombreBet"=>$nombreBet,"idBet"=>$idBet));
          $Email->to($email);
          $Email->send();
        }
        $this->render(false);
    }

}
