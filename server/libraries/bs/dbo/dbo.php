<?php
namespace bs\libraries\dbo;
defined('EXE') or die('Access');

class DBO
{
	private $query;
	public $connection = null;

	private $host;
	private $user;
	private $password;
	private $database;

	private $prefix;

	public function __construct($name, $type = 'a')
	{
		$name = $name ? $name : \F::getApp()->getSpace()->getPrefix();
		$this->prefix = $name.$type;

		$this->host = 'localhost';
		$this->user = 'ershandr_base2';
		$this->password = 'iK883&n8';
		$this->database = 'ershandr_base2';

		$this->connect();
	}

	public function escape($value)
	{
		return mysqli_real_escape_string($this->connection, $value);
	}

	private function connect()
	{
		$this->connection = \mysqli_connect($this->host, $this->user, $this->password, $this->database);

		if (!$this->connection)
			exit('Could not connect to MySQL server. '.mysqli_connect_error());

		mysqli_set_charset($this->connection, 'utf8');
		$this->setQuery('SET SQL_MODE="ALLOW_INVALID_DATES"')->execute();
		$this->setQuery('SET wait_timeout=100')->execute();
	}

	public function setQuery($query)
	{
		$this->query = str_replace('&__', $this->prefix.'_', $query);
		return $this;
	}

	public function loadAssocList($key = null)
	{
		$_result = $this->fetchAssoc();

		if ($key)
		{
			$result = [];
			foreach ($_result as $row)
				$result[$row[$key]] = $row;
		}
		else
			$result = $_result;

		return $result;
	}

	public function loadAssoc()
	{
		$current = current($this->fetchAssoc());
		return ($current === false ? null : $current);
	}

	private function fetchAssoc()
	{
		$cursor = $this->execute();
		return mysqli_fetch_all($cursor, MYSQLI_ASSOC);
	}

	public function close()
	{
		mysqli_close($this->connection);
	}

	public function insertid()
	{
		return mysqli_insert_id($this->connection);
	}

	public function q($text)
	{
		return '\''. str_replace('\\', '\\\\', $text) .'\'';
	}

	public function execute()
	{
		$result = mysqli_query($this->connection, $this->query);
		
		if (!$result)
		{
			$data = [
				'type' => 'sql',
				'msg' => mysqli_error($this->connection),
				'query' => $this->query
			];

			\F::getApp()->error(500, $data);
		}

		return $result;
	}
}