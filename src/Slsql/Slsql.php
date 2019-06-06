<?php namespace slsql\Slsql;

use slsql\Config;
use \Error;
use \PDO;

/**
 * WTFPL License (http://www.wtfpl.net/) - https: //github.com/CrBast/PHP-SQL_SimpleLife/blob/master/LICENSE
 *
 * SimpleLifeSQL
 *
 * Default configuration : XAMPP config
 * IN (array([
 *      dbName*,
 *      host,
 *      dbType,
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
class Slsql
{
    private $dsn,
    $user,
    $password,
    $db,
    $dbType,
    $dbName,
    $isConnected = false;

    public function __construct($params)
    {
        $this->dbName = $params['dbName'];
        $this->dsn = isset($params['host']) ? $params['host'] : '127.0.0.1:3306';
        $this->dbType = isset($params['dbType']) ? $params['dbType'] : 'mysql';
        $this->user = isset($params['user']) ? $params['user'] : 'root';
        $this->password = isset($params['psw']) ? $params['psw'] : '';
    }

    public function __destruct()
    {
        unset($this->dbName, $this->dsn, $this->dbType, $this->user, $this->password, $this->isConnected, $this->db);
    }

    /**
     * !! Not mandatory. During "send()" method the object creates the connection if it does not exist.
     * Connect to database.
     * Return :
     *      [status] = TRUE(OK)/FALSE(Problem),
     *      [message] = Exception message => if [status] = false
     */
    public function connect()
    {
        try {
            $this->db = $this->createDB();
            $this->isConnected = true;
            return createMessage('', true, '');
        } catch (Exception $e) {
            return createMessage('', false, $e->getMessage());
        }
    }

    private function connectDB()
    {
        try {
            $this->db = $this->createDB();
        } catch (Exception $e) {
            return createMessage('', false, $e->getMessage());
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
     *      [status] = TRUE(OK)/FALSE(Problem),
     *      [message] = Exception message => if [status] = false
     */
    public function send($request, $array)
    {
        if (!$this->isConnected) {
            $this->connectDB();
            $this->isConnected = true;
        }
        try {
            $stmt = $this->db->prepare($request);
            $stmt->execute($array);
            return createMessage($stmt, true, '');
        } catch (Exception $e) {
            return createMessage('', false, $e->getMessage());
        }
    }

    /**
     * Create DB object (PDO)
     */
    public function createDB()
    {
        return new PDO($this->dbType . ':dbname=' . $this->dbName . ';host=' . $this->dsn, $this->user, $this->password);
    }

    /**
     * Send Request.
     * the parameters are in the Config class file (slsql\Config)
     *
     * Return :
     *      [value] = result,
     *      [status] = TRUE(OK)/FALSE(Problem),
     *      [message] = Exception message => if [status] = false
     */
    public static function go($request, $array = array())
    {
        try {
            $db = Slsql::getPDO();
            $stmt = $db->prepare($request);
            $stmt->execute($array);
            //var_dump($stmt);
            return createMessage($stmt, true, '');
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
            return createMessage('', false, $e->getMessage());
        }
    }

    /**
     * Send Transaction.
     * the parameters are in the Config class file (slsql\Config)
     *
     * Return :
     *      [value] = result,
     *      [status] = TRUE(OK)/FALSE(Problem),
     *      [message] = Exception message => if [status] = false
     */
    public static function goT(SLTransaction $trans)
    {
        $db = Slsql::getPDO();
        try {
            $db->beginTransaction();
            foreach ($trans->get() as $transaction) {
                $stmt = $db->prepare($transaction['req']);
                $stmt->execute($transaction['arr']);
            }
            $db->commit();
            return createMessage($stmt, true, '');
        } catch (PDOException $e) {
            $db->rollback();
            exit(createMessage('', false, $e->getMessage()));
        }

    }

    private static function getPDO()
    {
        if (!class_exists("\slsql\Config")) {
            throw new Error("Cannot find <\slsql\Config>");
        }
        try {
            $db = new PDO(Config::dbType . ':dbname=' . Config::dbName . ';host=' . Config::host, Config::user, Config::password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (PDOException $e) {
            throw new Error('Cannot create connection to DB. Error message : ');
        }
    }
}

class SLTransaction
{
    private $allTrans;

    public function add($request, $array = array())
    {
        $this->allTrans[] = array('req' => $request, 'arr' => $array);
    }

    public function get()
    {
        return $this->allTrans;
    }

    public function go()
    {
        return Slsql::goT($this);
    }
}

/**
 * Status : true = OK | false = problem
 */
function createMessage($value, $status, $message)
{
    return array('value' => $value, 'status' => $status, 'message' => $message);
}
