<?php
/**
 * Script class.
 * This is just an example class. You can use any class name and any file name
 * as long as it is placed in the root of the src directory.
 * Also, the script must initiate itself
 *
 * @author Jordi Jolink
 * @date 31-7-2015
 */
class Script
{
    /**
     * @var View
     */
    private $view;

    /**
     * The constructor of this example class starts the single file application.
     */
    public function __construct()
    {
        $this->view = new View;

        $page = !empty($_GET['page']) ? $_GET['page'] : '1';
        $this->view->set('page', $page);

        $this->view->render('index');
    }
}

// Create the script object so that the application starts automatically.
new Script;
