<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppModel', 'Model');

/**
 * CakePHP Stadistic
 * @author Jorge Moreno
 */
class Stadistic extends AppModel {
    public $belongsTo=array("Game");
}
