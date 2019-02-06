<?php
/**
 * WTFPL License (http://www.wtfpl.net/) - https://gitlab.com/CrBast/php-sql_simplelife/blob/master/LICENSE
 * 
 * Class that simply makes life easier
 * 
 * #Example 1 (full)
 *      $db = new slsql();
 *      $db->connect();
 *      $db->send($request, $arraySettings)
 * 
 * #Example 2 (lite)
 *      $db = new slsql();
 *      $db->send($request, $arraySettings)
 * 
 * Return Type array([value], [status], [message])
 */
class slsql{
    private $_dsn = 'host=127.0.0.1:33066';
    private $_user = 'root';
    private $_password = '';
    private $_db;
    private $_dbName;
    private $isConnected = false;

    public function __construct($dbName){
        $this->_dbName = $dbName;
    }
    
    /**
     * !! Not mandatory. During "send()" method the object creates the connection if it does not exist.
     * Connect to database. 
     * Return : 
     *      [status] = 1(OK)/0(Problem),
     *      [message] = Exception message => if [status] = 0
     */
    public function connect(){
        try {
            $this->_db = new PDO('mysql:dbname='.$this->_dbName . ';' . $this->_dsn, $this->_user, $this->_password); 
            $this->isConnected = true;
            return $this->createMessage('', 1, '');
        } catch ( Exception $e ) 
        {  
            return $this->createMessage('', 0, $e->getMessage());
        }
    }

    private function connectDB(){
        try{
            $this->_db = new PDO('mysql:dbname='.$this->_dbName . ';' . $this->_dsn, $this->_user, $this->_password);
        } catch( Exception $e ){

        }
    }

    /**
     * Send Request.
     * Params => send($request, $array) :
     *      Request : sql request
     *      Array: Array with data insertion
     * 
     * Example : send('SELECT * FROM user WHERE user.id = ?', array(12))
     * 
     * Return : 
     *      [value] = result,
     *      [status] = 1(OK)/0(Problem),
     *      [message] = Exception message => if [status] = 0
     */
    public function send($request, $array){
        if(!$this->isConnected){
            $this::connectDB();
            $this->isConnected = true;
        }
        try {
            $stmt = $this->_db->prepare($request); 
            $stmt->execute($array); 
            return  $this->createMessage($stmt, 1, '');
        } catch (Exception $e) {
            return  $this->createMessage('', 0, $e->getMessage());
        }
    }

    /**
     * Status : 1 = OK | 0 = problem
     */
    private function createMessage($value, $status, $message){
        return array('value' => $value, 'status' => $status, 'message' => $message);
    }
}