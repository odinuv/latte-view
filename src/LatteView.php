<?php

namespace Ujpef;

use Latte\Engine;
use Latte\Macros\MacroSet;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class LatteView
 *
 * This class is a simple wrapper for Latte template engine which can be used with Slim PHP framework
 */
class LatteView
{

    private $latte;
    private $additionalParams = [];

    public function __construct(Engine $latte)
    {
        $this->latte = $latte;
    }

    /**
     * add template variables from assoc. array
     *
     * @param array $params
     */
    public function addParams(array $params)
    {
        $this->additionalParams = array_merge($this->additionalParams, $params);
    }

    /**
     * add template variable
     *
     * @param $name
     * @param $param
     */
    public function addParam($name, $param)
    {
        $this->additionalParams[$name] = $param;
    }

    /**
     * render the template
     *
     * @param Response $response
     * @param $name
     * @param array $params
     * @return Response
     */
    public function render(Response $response, $name, array $params = [])
    {
        $params = array_merge($this->additionalParams, $params);
        $output = $this->latte->renderToString($name, $params);
        $response->getBody()->write($output);
        return $response;
    }

    /**
     * add Latte macro
     *
     * @param $name
     * @param callable $callback
     */
    public function addMacro($name, callable $callback)
    {
        $set = new MacroSet($this->latte->getCompiler());
        $set->addMacro($name, $callback);
    }

    /**
     * add Latte filter
     *
     * @param $title
     * @param callable $callback
     */
    public function addFilter($title, callable $callback)
    {
        $this->latte->addFilter($title, $callback);
    }
}
