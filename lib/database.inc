<?php
class CTableName {
	/**
	 * Name cache array.
	 *
	 * @var array
	 */
	private $m_Names;
	/**
	 * Database object.
	 *
	 * @var CDatabase
	 */
	private $m_Database;
	public function __construct($a_Database) {
		$this->m_Database = $a_Database;
		$this->m_Names = array ();
	}
	public function __destruct() {
		unset ($this->m_Names);
	}
	public function get($a_strName) {
		if (isset ($this->m_Names[$a_strName]))
			return $this->m_Names[$a_strName];
		$this->m_Names[$a_strName] = $this->m_Database->getTableName($a_strName);
		return $this->m_Names[$a_strName];
	}
}
abstract class CDatabase {
	protected $m_szHostname;
	protected $m_szUserId;
	protected $m_szPassword;
	protected $m_szDBName;
	protected $m_szCharset;

	protected $m_oConnection;

	public $TABLE_PREFIX;
	public $TABLE_SUFFIX;

	public function __construct($a_szHostname, $a_szUserId, $a_szPassword, $a_szDBName = "", $a_szCharset = "UTF8") {
		$this->m_szHostname = $a_szHostname;
		$this->m_szUserId = $a_szUserId;
		$this->m_szPassword = $a_szPassword;
		$this->m_szDBName = $a_szDBName;
		$this->m_szCharset = $a_szCharset;
		$this->m_oConnection = null;
		$this->TABLE_PREFIX = "";
		$this->TABLE_SUFFIX = "";
	}
	public function __destruct() {
		if($this->m_oConnection)
			$this->close();
	}
	/**
	 * get table name using TABLE_PREFIX and TABLE_SUFFIX
	 * 
	 * @param	string	$a_szName
	 * @return	string
	 */
	public function getTableName($a_szName) {
		return $this->TABLE_PREFIX.$a_szName.$this->TABLE_SUFFIX;
	}
	/**
	 * connect to database
	 * 
	 * @return	bool
	 */
	public function connect() {
		return true;
	}
	abstract public function selectDatabase($a_szDBName = "");
	/**
	 * close to database
	 *
	 * @return	bool
	 */
	public function close() {
		$this->m_oConnection = null;
		return true;
	}
	/**
	 * do database query
	 *
	 * @param	string	$a_szQuery
	 * @return	resource
	 */
	public function dbquery($a_szQuery) {
		if(!$this->m_oConnection) {
			$this->connect();
			$this->selectDatabase();
		}
	}
	/**
	 * do query and returns specific field's data
	 *
	 * @param	string	$a_szQuery
	 * @param	string	$a_szField
	 * @return	string
	 */
	public function selectField($a_szQuery, $a_szField) {
		$oResult = $this->dbquery($a_szQuery);
		if($this->numRows($oResult) == 0) return null;
		$aryData = $this->fetchArray($oResult);
		$this->freeResult($oResult);
		return $aryData[$a_szField];
	}

	/**
	 * @param	string	$a_value
	 * @return	string
	 */
	public function escapeString($a_value) {
		if(!$this->m_oConnection) {
			$this->connect();
			$this->selectDatabase();
		}
		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$a_value = stripslashes($a_value);
		}
		return $a_value;
	}
	/**
	 * @param	string	$a_value
	 * @return	string
	 */
	public function smartQuote($a_value) {
		return $this->escapeString($a_value);
	}
	/**
	 * Checks if tuple exists in query's result.
	 *
	 * @param	string	$a_szQuery
	 * @return	bool
	 */
	public function hasTuple($a_szQuery) {
		if(!$this->m_oConnection) {
			$this->connect();
			$this->selectDatabase();
		}
	}
	/**
	 *	@return	int
	 */
	abstract public function affectedRows();
	/**
	 * @param	resource	$a_oResult
	 * @return	bool
	 */
	abstract public function freeResult($a_oResult);
	/**
	 * @param	resource	$a_oResult
	 * @return	array
	 */
	abstract public function fetchArray($a_oResult);
	/**
	 * @param	resource	$a_oResult
	 * @return	int
	 */
	abstract public function numRows($a_oResult);
	/**
	 * @return	int
	 */
	abstract public function lastInsertId();

	/**
	 * @return	CStatement
	 */
	abstract public function createStatement();
}

/**
 * CStatement class
 */
class CStatement {
	/**
	 * the object of CDatabase for using smartQueue
	 *
	 * @var CDatabase
	 */
	protected $m_oDatabase;

	/**
	 * escaped query string
	 *
	 * @var string
	 */
	protected $m_szQuery;

	/**
	 * database result object
	 *
	 * @var resource
	 */
	protected $m_oResult;

	/**
	 * @param	CDatabase	$a_oDatabase
	 */
	public function __construct(&$a_oDatabase) {
		$this->m_oDatabase = $a_oDatabase;
		$this->m_szQuery = "";
		$this->m_oResult = null;
	}
	public function __destruct() {
		if ($this->m_oResult != null && is_resource($this->m_oResult))
			$this->m_oDatabase->freeResult($this->m_oResult);
		unset($this->m_szQuery);
	}
	/**
	 * set statement.
	 * "SELECT %1, %2, %3, %4 FROM %5 WHERE no = %6 AND name = %7"
	 * 
	 * @param	string	$a_szQuery
	 * @param	array	$a_aryArguments
	 * @param	string	$a_szBuiltQuery
	 * @param	bool	$a_bQuote
	 * @return	CStatement
	 */
	public function set($a_szQuery, $a_aryArguments = null, $a_szBuiltQuery = "", $a_bQuote = true) {

		// Step 1. arguments doesn't exist,
		if ($a_aryArguments == null) {
			// Step 1-1. append query string.
			$this->m_szQuery = $a_szBuiltQuery." ".$a_szQuery;

		} else {
			// Step 1-2-1. if exist, escape value
			if ($a_bQuote) {
				foreach($a_aryArguments as &$argument) {
					$argument = $this->m_oDatabase->smartQuote($argument);
				}
			} else {
				foreach($a_aryArguments as &$argument) {
					$argument = $this->m_oDatabase->escapeString($argument);
				}
			}

			// Step 1-2-2. replace all $ characters.
			foreach ($a_aryArguments as &$argument) {
				$argument = str_replace ("$", "{!DOLLAR!}", $argument);
			}

			// Step 1-2-3. create key set.
			$keys = array();
			for($i = 1; $i <= count($a_aryArguments); $i++) {
				$keys[$i] = "/%{$i}/";
			}

			// Step 1-2-4. build query string and append it.
			$szReplaced = preg_replace($keys, $a_aryArguments, $a_szQuery);

			// Step 1-2-5. restore all $ characters.
			$szReplaced = str_replace ("{!DOLLAR!}", "$", $szReplaced);

			// Step 1-2-6. build query string and append it.
			$this->m_szQuery = $a_szBuiltQuery." ".$szReplaced;
		}
		return $this;
	}
	/**
	 * append statement
	 *
	 * @param	string	$a_szQuery
	 * @param	array	$a_aryArguments
	 * @return	CStatement
	 */
	public function append($a_szQuery, $a_aryArguments = null, $a_bQuote = true) {
		return $this->set($a_szQuery, $a_aryArguments, $this->m_szQuery, $a_bQuote);
	}
	/**
	 * @return string
	 */
	public function __toString() {
		return $this->m_szQuery;
	}
	/**
	 * @return resource
	 */
	public function execute() {
		$this->m_oResult = $this->m_oDatabase->dbquery($this->m_szQuery);
		return $this->m_oResult;
	}
	/**
	 * @return bool
	 */
	public function update() {
		if ($this->m_oDatabase->dbquery($this->m_szQuery)) {
			return true;
		}
		return false;
	}
	/**
	 * @return array
	 */
	public function fetchArray() {
		if ($this->m_oResult == null) $this->execute();
		return $this->m_oDatabase->fetchArray($this->m_oResult);
	}
	/**
	 * @return int
	 */
	public function affectedRows() {
		return $this->m_oDatabase->affectedRows();
	}
	/**
	 * @return int
	 */
	public function numRows() {
		// Step 1. do query
		if ($this->m_oResult == null) $this->execute();

		// Step 2. Checks if query contains COUNT(*)
		$nums = 0;
		if (stripos($this->m_szQuery, "COUNT(*)") !== FALSE) {
			$data = $this->fetchArray();
			$nums = $data['COUNT(*)'];
		} else {
			$nums = $this->m_oDatabase->numRows($this->m_oResult);
		}
		// Step 3. returns count of rows
		return $nums;
	}
	/**
	 * @return bool
	 */
	public function hasTuple() {
		if ($this->m_oResult == null) $this->execute();
		return ($this->numRows() != 0);
	}
	public function selectField($a_szFieldName) {
		if ($this->m_oResult == null) $this->execute();
		$data = $this->fetchArray();
		$retval = $data[$a_szFieldName];
		unset($data);
		return $retval;
	}
}

/**
 * MySQL Database
 */
class CMySQLDatabase extends CDatabase {
	public function __construct($a_szHostname, $a_szUserId, $a_szPassword, $a_szDBName = "", $a_szCharset = "UTF8") {
		parent::__construct($a_szHostname, $a_szUserId, $a_szPassword, $a_szDBName, $a_szCharset);
	}
	/**
	 * @return bool
	 */
	public function connect() {
		if($this->m_oConnection) return true;
		if(!($this->m_oConnection = mysql_connect($this->m_szHostname, $this->m_szUserId, $this->m_szPassword))) {
			return false;
		}
		mysql_query("SET NAMES {$this->m_szCharset}");
		return parent::connect();
	}
	/**
	 * Select database
	 * 
	 * @param	string	$a_szDBName
	 * @return	bool
	 */
	public function selectDatabase($a_szDBName = "") {
		if(!$a_szDBName) $a_szDBName = $this->m_szDBName;
		return mysql_select_db($a_szDBName, $this->m_oConnection);
	}
	/**
	 * Close database connection
	 *
	 * @return bool
	 */
	public function close() {
		if($this->m_oConnection) {
			return mysql_close($this->m_oConnection) && parent::close();
		}
		return parent::close();
	}
	/**
	 * Execute query
	 *
	 * @param	string	$a_szQuery
	 * @return	resource
	 */
	public function dbquery($a_szQuery) {
		parent::dbquery($a_szQuery);
		//CLogger::logger ($a_szQuery);

		$result = mysql_query($a_szQuery, $this->m_oConnection)
			or die("Invalid Query: [{$a_szQuery}]<br />\n".mysql_error());
		return $result;
	}

	/**
	 * @return int
	 */
	public function affectedRows() {
		return mysql_affected_rows($this->m_oConnection);
	}
	/**
	 * @param	resource	$a_oResult
	 * @return	bool
	 */
	public function freeResult($a_oResult) {
		return mysql_free_result($a_oResult);
	}
	/**
	 * @param	resource	$a_oResult
	 * @return	array
	 */
	public function fetchArray($a_oResult) {
		return mysql_fetch_array($a_oResult, MYSQL_ASSOC);
	}
	/**
	 * @param	resource	$a_oResult
	 * @return	int
	 */
	public function numRows($a_oResult) {
		return mysql_num_rows($a_oResult);
	}
	/**
	 * @return	int
	 */
	public function lastInsertId() {
		return mysql_insert_id($this->m_oConnection);
	}

	/**
	 * @param	string	$a_value
	 * @return	string
	 */
	public function escapeString($a_value) {
		$a_value = parent::escapeString($a_value);
		$a_value = mysql_real_escape_string($a_value, $this->m_oConnection);
		$a_value = str_replace ("\\", "\\\\", $a_value);
		return $a_value;
	}
	/**
	 * @param	string	$a_value
	 * @return	string
	 */
	public function smartQuote($a_value) {
		if (!is_numeric($a_value) || $a_value[0] == "0")
			return "'".$this->escapeString($a_value)."'";

		return $a_value;
	}
	/**
	 * @param	string	$a_szQuery
	 * @return	bool
	 */
	public function hasTuple($a_szQuery) {
		parent::hasTuple($a_szQuery);

		// Step 1. do query
		$result = mysql_query($a_szQuery, $this->m_oConnection);

		// Step 2. Checks if query contains COUNT(*)
		$retval = 0;
		if (strpos($a_szQuery, "COUNT(*)") !== FALSE) {
			// Step 2-1. returns COUNT(*)
			$data = mysql_fetch_assoc($result);
			$retval = $data['COUNT(*)'];
			unset($data);

		} else {
			// Step 2-2. or, returns num-rows
			$retval = mysql_num_rows($result) != 0;
		}
		mysql_free_result($result);
		return $retval != 0;
	}
	/**
	 * Create CStatement object
	 *
	 * @return	CStatement	CStatement object
	 */
	public function createStatement() {
		return new CStatement($this);
	}
}
?>
