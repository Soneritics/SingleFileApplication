<?php
/**
 * Script class.
 *
 * @author Jordi Jolink
 * @date 31-7-2015
 */
class Script
{
    private $view;

    public function __construct()
    {
        $this->view = new View;

        $page = !empty($_GET['page']) ? $_GET['page'] : '1';
        $this->view->set('page', $page);

        $this->view->render('index');
    }
}

new Script;
