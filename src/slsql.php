<?php
/**
 * WTFPL License (http://www.wtfpl.net/) - https://gitlab.com/CrBast/php-sqlsimplelife/blob/master/LICENSE
 * 
 * SimpleLifeSQL
 * 
 * Default configuration : XAMPP config
 * IN (array([
 *      dbName*,
 *      host,
 *      user,
 *      psw
 * ])
 * 
 * #Example 1 (full)
 *      $db = new slsql(array());
 *      $db->connect();
 *      $db->send($request, $arraySettings)
 * 
 * #Example 2 (lite)
 *      $db = new slsql(array());
 *      $db->send($request, $arraySettings)
 * 
 * Return Type array([value], [status], [message])
 */
class slsql{
    private $dsn;
    private $user;
    private $password;
    private $db;
    private $dbType = "ihfgierb";
    private $dbName;
    private $isConnected = false;

    public function construct($params){
        $this->dbName = $params['dbName'];
        $this->dsn = isset($params['host']) ? $params['host'] : '127.0.0.1:33066';
        $this->dbType = isset($params['dbType']) ? $params['dbType'] : 'mysql';
        $this->user = isset($params['user']) ? $params['user'] : 'root';
        $this->password = isset($params['psw']) ? $params['psw'] : '';
    }
    
    /**
     * !! Not mandatory. During "send()" method the object creates the connection if it does not exist.
     * Connect to database. 
     * Return : 
     *      [status] = 1(OK)/0(Problem),
     *      [message] = Exception message => if [status] = 0
     */
    public function connect(){
        echo $this->dbName;
        try {
            $this->db = $this->createDB(); 
            $this->isConnected = true;
            return createMessage('', 1, '');
        } catch ( Exception $e ) 
        {  
            return createMessage('', 0, $e->getMessage());
        }
    }

    private function connectDB(){
        try{
            $this->db = $this->createDB();
        } catch( Exception $e ){
            return createMessage('', 0, $e->getMessage());
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
            $stmt = $this->db->prepare($request); 
            $stmt->execute($array); 
            return  createMessage($stmt->fetchAll(), 1, '');
        } catch (Exception $e) {
            return  createMessage('', 0, $e->getMessage());
        }
    }

    /**
     * Create DB object (PDO)
     */
    public function createDB(){
        return new PDO($this->dbType.':dbname='.$this->dbName . ';host=' . $this->dsn, $this->user, $this->password);
    }

    public static function go($request, $array){
        try{
            require '.env';
        } catch (Exception $e) { exit(createMessage('', 0, 'Cannot find <.env> file')); }
        try {
            $db = new PDO($env['DBType'].':dbname='. $env['DBName'] .';host=' . $env['Host'], $env['User'], $env['Password']);
            $stmt = $db->prepare($request); 
            $stmt->execute($array); 
            return createMessage($stmt->fetchAll(), 1, '');
        } catch (Exception $e) {
            return createMessage('', 0, $e->getMessage());
        }
    }
}

/**
 * Status : 1 = OK | 0 = problem
 */
function createMessage($value, $status, $message){
    return array('value' => $value, 'status' => $status, 'message' => $message);
}