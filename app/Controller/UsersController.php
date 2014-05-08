<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
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
        $puedo=false;
          $this->layout="webservice";
          if(!empty($this->request->data))
          {
              try {
                  if($resul= $this->User->save($this->request->data))
                  {
                      $datos= $this->User->id;
                      $puedo=true;
                  }                
                else
                    $datos= -1;
                $this->set(array(
                     'datos' => $datos,
                     '_serialize' => array('datos')
                 ));
              } catch (Exception $exc) 
                {
                  debug($exc->getMessage());
                  $datos=-2;
                  $this->set(array(
                     'datos' => $datos,
                     '_serialize' => array('datos')
                 ));
              }

              
               
          }
          if($puedo==true)
          {
              
              $this->enviarConfirmacion($this->request->data["email"],$this->request->data["nombres"],$this->request->data["idioma"],$this->User->id);
          }

    }
    
    private function enviarConfirmacion($email,$nombre,$idioma,$id) 
    {
//        debug("Email :".$email);
//        debug("nombre :".$nombre);
//        debug("idioma :".$idioma);
          $Email = new CakeEmail();
          $Email->config('mandrill');
          switch ($idioma) {
              case "ES":
                  $Email->template('confirmarcorreo', 'confirmarcorreo');
                  $Email->subject("Confirmar correo, polla mundialista");
                  break;
              case "POR":
                  $Email->template('confirmarcorreo', 'confirmarcorreo');
                  $Email->subject("Confirmeichon correiño");
                  break;
              default:
                  $Email->template('confirmarcorreo', 'confirmarcorreo');
                  $Email->subject("Confirmar correo, polla mundialista");
                  break;
          }
          $Email->emailFormat('html');
          $Email->viewVars(array('email' => $id,"nombre"=>$nombre));
          $Email->to($email);
          $Email->send();
          $this->render(false);
        
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
     *          confirmacion
     */
    public function login()
    {
//        $this->layout="webservice";
        $nick=$this->request->data['nick'];
        $pass=$this->request->data['pass'];
        $parametros=array(
            'conditions'=>array("email"=>$nick)
        );
        $datos=  $this->User->find("all",$parametros);
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }
    /**
     * Esta funcion se encarga de retornar la cantidad de personas en la polla,
     * la posicion del usuario en entre todas las personas y la puntuacion que 
     * el usuario lleva
     */
    public function getInformacion() {
        $idBet=$this->request->data['idBet'];
        $idUsuario=$this->request->data['idUsuario'];
    }
    public function confirmarcorreo($email)
    {
        
        //Verifico que es correo este en la base de datos
        $parametros=array(
            'conditions'=>array(
                "id"=>$email)
        );
        $datos=  $this->User->find("all",$parametros);
        $mensaje="";
        
        if(count($datos)>0)
        {
            $datos=$datos[0];
            debug(print_r($datos));
            $datos["User"]["confirmado"]=1;
            $this->User->id = $datos["User"]["id"];
            if($this->User->save($datos))
            {
                $mensaje="Haz finalizado el proceso de registro, ya puedes utilizar la aplicacion";
            }else{
                $mensaje="Ha ocurrido un error al actualizar la informacion";
            }
        }else{
            $mensaje="Lo sentimos, este correo no se encuentra en nuestra base de datos";
        }
        
        $this->set("mensaje",$mensaje);
    }

}
