<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppModel', 'Model');

/**
 * CakePHP bet_user
 * @author Jorge Moreno
 */
class BetsUser extends AppModel {
    public $belongsTo=array("Bet","User");
}
