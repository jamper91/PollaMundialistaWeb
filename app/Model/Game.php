<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppModel', 'Model');

/**
 * CakePHP game
 * @author Jorge Moreno
 */
class Game extends AppModel {
    public $hasMany=array("Stadistic");
    public $belongsTo = array(
        'Local' => array(
            'className' => 'Team',
            'foreignKey' => 'local'
        ),
        'Visitante' => array(
            'className' => 'Team',
            'foreignKey' => 'visitante'
        )
    );
}
