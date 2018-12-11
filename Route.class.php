<?php
namespace framework;

use framework\bdd\DbConnection;

/**
 * Classe facilitant les redirection ainsi que les créations d'url
 * 
 * @author Vince
 *
 */
class Route {
    
	/**
	 * Retourne un code HTTP 404
	 */
	public static function notFound(){
		self::closeBdd();
		header("HTTP/1.1 404 Not Found", true, 404);
		exit();
	}
	
    /**
     * Retourne un code HTTP 401
     */
    public static function unauthorized(){
    	self::closeBdd();
        header("HTTP/1.1 401 Unauthorized", true, 401);
        exit();
    }

    /**
     * Retourne un code HTTP 400
     */
    public static function badRequest(){
    	self::closeBdd();
        header("HTTP/1.1 400 Bad request", true, 400);
        exit();
    }
    
    /**
     * Redirection vers une route
     * 
     * @param string $route     route vers laquelle rediriger
     */
    public static function redirection(string $route) {
    	self::closeBdd();
        header("Location:" . $route);
        exit();
    }
    
    /**
     * Redirection vers une route
     *
     * @param mixed $objectToSend objet à transformer en json et à envoyer au client
     */
    public static function sendJson($objectToSend) {
    	self::closeBdd();
    	echo json_encode($objectToSend);
    	exit();
    }
    
    /**
     * Crée une url depuis une route
     * 
     * @param string $route     route à transformer
     * 
     * @return string           l'url correspondante
     */
    public static function createRoute(string $route) : string {
        return self::createParameterizedRoute($route);
    }
    
    /**
     * Crée une url avec des paramètres depuis une route
     * 
     * @param string $route     route à transformer
     * @param array $params     paramètres à incorporer
     * 
     * @return string           l'url correspondante
     */
    public static function createParameterizedRoute(string $route, array $params=array()) : string {
        $urlParams = "";
        foreach ($params as $key => $value) {
            $urlParams .= $key . "=" . $value . "&";
        }
        return  self::createUrlFromRoot($route . ($urlParams != "" ? "?" . substr($urlParams, 0, strlen($urlParams) - 1) : ""));
    }
    
    /**
     * Permet de rajouter le domaine de site web à une url 
     * 
     * @param string $url
     * 
     * @return string
     */
    public static function createUrlFromRoot(string $url) : string {
        return Config::get(Config::ROOT_URL) . "/" . $url;
    }
    
    /**
     * Permet de fermer les connexions à la bdd avant de terminer le programme
     */
    public static function closeBdd() {
    	if(DbConnection::hasInstance()) {
    		$bddExecutor = DbConnection::getInstance();
    		if($bddExecutor->isAlive()) {
    			$bddExecutor->closeConnection();
    		}
    	}
    }
}
