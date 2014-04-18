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
    /**
     * Almacenas las predicciones del usuario
     * direccion: forecasts/saveforecasts.xml
     * Parametros
     * -->  idUsuario
     * -->  idBet
     * -->  idGames: Es un vector con los ids de los juegos , cada juego esta
     *              separado por un "-"
     * -->  marcadores_local: Es un vector con los marcadores del equipo local
     *                      Cada marcador esta separado con un "-"
     * -->  marcadores_visitante: Es un vector con los marcadores del equipo visitante
     *                      Cada marcador esta separado con un "-".
     * -->  idForecasts: Vector que contiene los ids de las predicciones ya realizadas
     *                  para poder actualizaras, cada prediccion esta separa 
     *                  con un "-"
     * Respuesta
     * -->  datos: Si responde ok es porque todo ha ocurrido bien
     */
    public function saveforecasts()
    {
        $this->layout="webservice";
        $idUsuario=$this->request->data["idUsuario"];
        $idBet=$this->request->data["idBet"];
        //Recibo un vector 
        $idGames=$this->request->data["idGames"];
        $idForecasts=$this->request->data["idForecasts"];
        $marcadores_local=$this->request->data["marcadores_local"];
        $marcadores_visitante=$this->request->data["marcadores_visitante"];
        
        //Recorro todos los marcadores de locales y visitantes
        $locales = explode("-", $marcadores_local);
        $visitantes= explode("-",$marcadores_visitante);
        $juegos=explode("-", $idGames);
        $predicciones=explode("-", $idForecasts);
        $i=0;
        try {
            foreach ($locales as $marcador_local)
            {
                $idGame=$juegos[$i];
                $marcador_visitante=$visitantes[$i];
                $idForecast=$predicciones[$i];
//                debug($idForecast);
                if($idForecast!="0"){
//                    debug("Entre");
                    $this->Forecast->id = $idForecast;
                }else{
                    $this->Forecast->create();
                }
                $data=array(
                    "Forecast"=>array(
                            "user_id"=>$idUsuario,
                            "game_id"=>$idGame,
                            "bet_id"=>$idBet,
                            "marcador_local"=>$marcador_local,
                            "marcador_visitante"=>$marcador_visitante
                        )
                );

                
                
                if($this->Forecast->save($data))
                {
                    
                }else{
                    debug($this->Forecast->validationErrors);
                }
                
                
                $i++;

            }
            $datos="ok";
        } catch (Exception $exc) 
        {
            $datos=$exc->getTraceAsString();
//            echo $exc->getTraceAsString();
        }
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));

        
    }


}
