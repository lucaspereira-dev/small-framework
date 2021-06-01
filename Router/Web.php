<?php

namespace Router;

use \Closure;
use \Exception;
use Resources\View;


class Web{

    private $routes = []; // Rotas armazenadas
    private $method, $path, $params; // Metodo, URL e parametros da requisição atual

    /**
     * Construtor recebe o tipo de requisição HTTP e no segundo parametro recebe o endereço da URL
     */
    public function __construct($method, $path)
    {
        $this->method = $method;
        $this->path = $path;
    }

    private function getAction($action){
        if(is_callable($action)){
            return $action;
        }elseif(is_string($action)){
            $function = explode("@", $action);
            return $action;
        }
    }

    /**
     * Adiciona rota e função para as requisições do tipo GET
     */
    public function get(string $route, $action)
    {
        $this->add('GET', $route, $action);
    }

    /**
     * Adiciona rota e função para as requisições do tipo POST
     */
    public function post(string $route, $action)
    {
        $this->add('POST', $route, $action);
    }

    /**
     * Adiciona as novas rotas
     */
    private function add(string $method, string $route, $action)
    {
        $this->routes[$method][$route] = $action;
    }

    private function getParams()
    {
        return $this->params;
    }

    private function handler(){

        // Verifica se existe o metodo acessado nas rotas
        if (empty($this->routes[$this->method])) {
            return false;
        }

        // Return POST or Text
        if (isset($this->routes[$this->method][$this->path])) {

            if($this->method == "POST" && !empty($_POST)){

                $this->params = $_POST;
    
            }

            return $this->routes[$this->method][$this->path];
        }

        // Return GET and parametros
        foreach ($this->routes[$this->method] as $route=>$action) {
            $result = $this->checkUrl($route, $this->path);
            if ($result >= 1) {
                return $action;
            }
        }

        return false;
    }

    /**
     * Retorna a rota acessada pelo usuário
     */
    public function run()
    {
        $result = $this->handler();

        if($result instanceof Closure){

            $result($this->getParams());
        
        }elseif(is_string($result)){
        
            try{
                $result = explode("@", $result);
        
                $class = new $result[0]();
                $action = $result[1];
            
                $class->$action($this->getParams());

            }catch(Exception $e){
                die($e->getMessage());
            }
        
        }else{
            throw new Exception("Não existe rota para este endereço!");
        }
    }

    private function checkUrl(string $route, $path)
    {
        preg_match_all('/\{([^\}]*)\}/', $route, $keys);

        $regex = str_replace('/', '\/', $route);

        foreach ($keys[0] as $variable){
            $replacement = '([a-zA-Z0-9\-\_\ ]+)';
            $regex = str_replace($variable, $replacement, $regex);
        }

        $result = preg_match('/^' . $regex . '$/', $path, $values);

        array_shift($values);
        array_shift($keys);

        if(count($keys[0]) == count($values)){
            
            $this->params = array_combine($keys[0], $values);

        }

        return $result;
    }

    public static function View($dir = "folder@file", Array $params = array()){
        return new View($dir, $params);
    }

}