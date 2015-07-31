<?php
/**
 * View class.
 *
 * @author Jordi Jolink
 * @date 31-7-2015
 */
class View
{
    /**
     * @var array
     */
    private $variables = array();

    /**
     * Set a parameter for use in the view.
     * @param $variable
     * @param $value
     * @return $this
     */
    public function set($variable, $value)
    {
        $this->variables[$variable] = $value;
        return $this;
    }

    /**
     * Render the view.
     * @param string $view
     */
    public function render($view)
    {
        $template = new Template;
        $templateFile = $template->getTemplateFilename($view);

        if (!empty($this->variables)) {
            foreach ($this->variables as $key => $value) {
                $$key = $value;
            }
        }

        ob_start();
        include $templateFile;
        $content = ob_get_clean();

        include $template->getTemplateFilename('layout');
    }
}