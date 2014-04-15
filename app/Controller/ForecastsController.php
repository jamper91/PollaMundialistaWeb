<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP ForecastsController
 * @author Jorge Moreno
 */
class ForecastsController extends AppController {
    public $components = array('RequestHandler');
    public function index($id) {
        
    }
    /**
     * Lista las puntuaciones del usuario
     * Parametros
     * -->  idBet:Id del usuario
     * -->  idUsuario: Id de la polla
     * Respuesta
     * -->  datos
     * -->  Forecast
     * -->  Game
     *          Local
     *          Visitante
     *          Stadistic
     *      User
     *      Bet
     *          
     */
    public function getscoresbyuser() 
    {
        $idUsuario=  $this->request->data["idUsuario"];
        $idBet=  $this->request->data["idBet"];
        Debugger::dump("$idBet:".$idBet);
        $this->layout="webservice";
        $options=array(
//            "fields"=>array("Game.fecha,Game.local, Game.visitante, Game.goles_local,"
//                . " Game.goles_visitante, Forecast.marcador_local, Forecast.marcador_visitante,"
//                . " Forecast.puntuacion","Bet.id","Bet.nombre","Local.nombre"),
            "conditions"=>array("Forecast.user_id"=>$idUsuario,"Forecast.bet_id"=>$idBet),
//            "contain"=>array(
//                "Game"=>array(
//                    "Local"=>array(
//                        "fields"=>array("id","nombre")
//                    )
//                )
//            )
            "recursive"=>2  
        );

        $datos=  $this->Forecast->find("all",$options);
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
        
    }
    /**
     * Lista los puntajes de cada usuario de la polla
     * direccion:forecasts/getscorebybet.xml
     * Parametros:
     * -->  idBet: Id de la polla
     * Respuesta
     * -->  datos
     *          u
     *              nick
     *          Forecast
     *              puntaje
     */
    public function getscorebybet() 
    {
        $idPolla=  $this->request->data["idBet"];
        $this->layout="webservice";
        $this->Forecast->virtualFields['puntaje'] = 0;
        $sql="select u.nick, count(f.puntuacion) as Forecast__puntaje from users u,"
                . " forecasts f where f.bet_id=$idPolla and f.user_id=u.id";
            $datos=  $this->Forecast->query($sql);
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }


}
