<?php

/**
 * OAuth storage handler using PHP sessions
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/dropbox
 * @package Dropbox\Oauth
 * @subpackage Storage
 */
namespace Dropbox\OAuth\Storage;

class Session implements StorageInterface
{
	/**
	 * Session namespace
	 * @var string
	 */
	private $namespace = 'dropbox_api';
	
	/**
	 * Encyption object
	 * @var Encrypter|null
	 */
	private $encrypter = null;
	
	/**
	 * Check if a session has been started and if an instance
	 * of the encrypter is passed, set the encryption object
	 * @return void
	 */
	public function __construct(Encrypter $encrypter = null)
	{
		$id = session_id();
		if(empty($id)) session_start();
		
		if($encrypter instanceof Encrypter){
			$this->encrypter = $encrypter;
		}
	}
	
	/**
	 * Set the session namespace
	 * $namespace corresponds to $_SESSION[$namespace] 
	 * @param string $namespace
	 * @return void
	 */
	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}
	
	/**
	 * Get an OAuth token from the session
	 * If the encrpytion object is set then
	 * decrypt the token before returning
	 * @return array|bool
	 */
	public function get()
	{
		if(isset($_SESSION[$this->namespace]['token'])){
			$token = $_SESSION[$this->namespace]['token'];
			if($this->encrypter instanceof Encrypter){
				return $this->encrypter->decrypt($token);
			}
			return $token;
		}
		return false;
	}
	
	/**
	 * Set an OAuth token in the session
	 * If the encryption object is set then
	 * encrypt the token before storing
	 * @return void
	 */
	public function set($token)
	{
		if($this->encrypter instanceof Encrypter){
			$token = $this->encrypter->encrypt($token);
		}
		$_SESSION[$this->namespace]['token'] = $token;
	}
}
