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
     * direccion: 
     * Parametros
     * -->  idBet:Id del usuario
     * -->  idUsuario: Id de la polla
     * Respuesta
     * -->  datos
     *      -->  Game
     *          fecha
     *          goles_local
     *          goles_visitante
     *      -->Forecast
     *          marcador_local
     *          marcador_visitante
     *          puntuacion
     *      -->Bet
     *          id
     *          nombre
     *      -->Local
     *          nombre
     *      -->Visitante
     *          nombre
     */
    public function getscoresbyuser() 
    {
        $idUsuario=  $this->request->data["idUsuario"];
        $idBet=  $this->request->data["idBet"];
        $this->layout="webservice";
        $options=array(
            "fields"=>array(
                "Game.fecha",
                "Game.goles_local",
                "Game.goles_visitante",
                "Forecast.marcador_local",
                "Forecast.marcador_visitante",
                "Forecast.puntuacion",
                "Bet.id",
                "Bet.nombre",
                "Local.nombre",
                "Visitante.nombre"
            ),
            "conditions"=>array(
                "Forecast.user_id"=>$idUsuario,
                "Forecast.bet_id"=>$idBet),
            "joins"=>array(
                array(
                    "table"=>"bets",
                    "alias"=>"Bet",
                    "type"=>"left",
                    "fields"=>array(
                        "Bet.id",
                        "Bet.nombre",
                    ),
                    "conditions"=>array(
                        "Bet.id=Forecast.bet_id"
                    )
                ),
                array(
                    "table"=>"games",
                    "alias"=>"Game",
                    "type"=>"left",
                    "fields"=>array(
                        "Game.fecha",
                        "Game.local",
                        "Game.visitante",
                        "Game.goles_local",
                        "Game.goles_visitante",
                    ),
                    "conditions"=>array(
                        "Game.id=Forecast.game_id"
                    )
                ),
                array(
                    "table"=>"teams",
                    "alias"=>"Local",
                    "type"=>"left",
                    "fields"=>array(
                        "Local.nombre"
                    ),
                    "conditions"=>array(
                        "Local.id=Game.local"
                    )
                ),
                array(
                    "table"=>"teams",
                    "alias"=>"Visitante",
                    "type"=>"left",
                    "fields"=>array(
                        "Visitante.nombre"
                    ),
                    "conditions"=>array(
                        "Visitante.id=Game.visitante"
                    )
                )
            ),
            "recursive"=>-1
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
     *      -->b_u
     *          user_id
     *          bet_id
     *      -->u
     *          nick
     *      -->Forecast
     *          puntaje
     */
    public function getscorebybet() 
    {
        $idBet=  $this->request->data["idBet"];
        $this->layout="webservice";
        //$this->Forecast->virtualFields['puntaje'] = 0;
        $this->Forecast->virtualFields['puntaje'] = 0;
        $sql="  select
                        b_u.user_id,
                        b_u.bet_id,
                        u.nick,
                        COALESCE(a.puntaje,0) as Forecast__puntaje
                from
                        users u,
                        bets_users b_u
                        
                left join
                        (
                        select
                                b_u.user_id,
                                sum(f.puntuacion) as puntaje
                        from
                                forecasts f,
                                bets_users b_u
                        where
                                b_u.bet_id=$idBet and
                                b_u.bet_id=f.bet_id and
                                b_u.user_id=f.user_id
                         group by
                                b_u.user_id
                    ) a
                on
                        a.user_id=b_u.user_id
                where
                        b_u.bet_id=$idBet and
                        b_u.user_id=u.id
                order by 
                        puntaje DESC
                ";
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
    /**
     * Se encarga de mostrar la siguiente informacion:
     * Puntaje del usuario actual en la polla.
     * Posicion actual del usuario en la polla, dicha posicion, se obtiene organizando
     * las puntuaciones de mayor a menor.
     * Total de usuario en la polla
     * direccion:
     *  forecasts/getinformacion.xml
     * parametros:
     *  -->idBet
     *  -->idUsuario
     * respuesta:
     *  -->Forecast
     *      puntaje
     *      posicion
     *      total
     */
    public function getinformacion()
    {
        //Primero calculo el puntaje de todos los usuarios de la polla
        $this->layout="webservice";
        $idPolla=  $this->request->data["idBet"];
        $idUsuario=  $this->request->data["idUsuario"];
        $this->recalcular($idPolla);
        $posicio=  $this->getPositionInBet($idPolla, $idUsuario);
        $this->Forecast->virtualFields['puntaje'] = 0;
        $this->Forecast->virtualFields['posicion'] = $posicio["posicion"];
        $this->Forecast->virtualFields['total'] = $posicio["total"];
        $parametros=array(
            'fields'=>array(
              'sum(Forecast.puntuacion) as Forecast__puntaje',
              'Forecast.posicion',
              'Forecast.total'
            ),
            'conditions'=>array(
                'Forecast.bet_id'=>$idPolla,
                'Forecast.user_id'=>$idUsuario
            )
        );
        $datos=  $this->Forecast->find('all',$parametros);
        $log = $this->Forecast->getDataSource()->getLog(false, false);
        
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }
    
    private function recalcular($idBet)
    {
        
        $options=array(
            'fields'=>array(
                'Forecast.id',
                'Forecast.game_id',
                'Forecast.marcador_local',
                'Forecast.marcador_visitante'
            ),
            'conditions'=>array(
                'Forecast.bet_id'=>$idBet
            )
            
        );
        //Obtengo las predicicones de todos los uaurios
        $predicciones= $this->Forecast->find('all', $options);
        //Obtengo todos los partidos y sus resultados
        $this->loadModel('Game');
        $options=array(
            'fields'=>array(
                'Game.id',
                'Game.fecha',
                'Game.goles_local',
                'Game.goles_visitante',
                'Game.finalizo',
            ),
            'conditions'=>array(
                'Game.finalizo'=>1
            )
        );
        $partidos=$this->Game->find('all',$options);
        foreach ($partidos as $partido) 
        {
            $partido=$partido["Game"];
            //Obtengo el id del este partido
            $idGame=$partido["id"];
            $goles_local=$partido["goles_local"];
            $goles_visitante=$partido["goles_visitante"];
            //Busco todas las predicciones de este partido
            foreach ($predicciones as $prediccion) 
            {
                //Obtengo los datos
                $prediccion=$prediccion["Forecast"];
                $idG=$prediccion["game_id"];
                $marcador_local=$prediccion["marcador_local"];
                $marcador_visitante=$prediccion["marcador_visitante"];
                if($idG==$idGame)
                {
                    if($marcador_local==$goles_local && $marcador_visitante==$goles_visitante)
                    {
                        $prediccion["puntuacion"]=6;
                    }else if($marcador_local>$marcador_visitante && $goles_local>$goles_visitante)
                    {
                        $prediccion["puntuacion"]=2;
                    }else if($marcador_local<$marcador_visitante && $goles_local<$goles_visitante)
                    {
                        $prediccion["puntuacion"]=2;
                    }else if($marcador_local==$marcador_visitante && $goles_local==$goles_visitante){
                        $prediccion["puntuacion"]=2;
                    }else{
                        $prediccion["puntuacion"]=0;
                    }
                    
                    $this->Forecast->save($prediccion);
                }

            }
            
            
            
           
        }
    }

    private function getPositionInBet($idBet,$idUsuario)
    {
        $sql="  select
                        b_u.user_id,
                        b_u.bet_id,
                        COALESCE(a.puntaje,0) as puntaje
                from
                        bets_users b_u
                left join
                        (
                        select
                                b_u.user_id,
                                sum(f.puntuacion) as puntaje
                        from
                                forecasts f,
                                bets_users b_u
                        where
                                b_u.bet_id=$idBet and
                                b_u.bet_id=f.bet_id and
                                b_u.user_id=f.user_id
                         group by
                               b_u.user_id
                    ) a
                on
                        a.user_id=b_u.user_id
                where
                        b_u.bet_id=$idBet
                order by 
                        puntaje DESC
                ";
        $datos=$this->Forecast->query($sql);
        $total=  sizeof($datos);
        $posicion=0;
        $i=1;
        foreach ($datos as $dato) 
        {
            $dato=$dato["b_u"];
            if($dato["user_id"]==$idUsuario)
            {
                $posicion=$i;
                break;
            }
            $i++;
        }
        $datos=array(
            "total"=>$total,
            "posicion"=>$posicion
        );
        //debug(print_r($datos));
        return $datos;
        
    
    }

}
