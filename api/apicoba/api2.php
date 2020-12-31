<?php
/**
 * API class
 * @author Rob
 * @version 2015-06-22
 */

class api
{
	private $db;

	/**
	 * Constructor - open DB connection
	 *
	 * @param none
	 * @return database
	 */
	function __construct()
	{
		$conf = json_decode(file_get_contents('configuration.json'), TRUE);
		$this->db = new mysqli($conf["host"], $conf["user"], $conf["password"], $conf["database"]);
	}

	/**
	 * Destructor - close DB connection
	 *
	 * @param none
	 * @return none
	 */
	function __destruct()
	{
		$this->db->close();
	}

	/**
	 * Get the list of users
	 *
	 * @param none or user id
	 * @return list of data on JSON format
	 */
	function get($params)
	{
		$query = 'SELECT u.member_id AS id'
		. ', u.loan_date AS loan_date'
		. ', u.due_date AS due_date'
		. ', u.item_code AS item_code'
        . ', b.title AS judul'
        . ', b.image AS image'
		. ', u.is_return AS is_return'
		. ' FROM loan AS u'
		. ' left outer join item i on i.item_code = u.item_code'
		. ' left outer join biblio b on b.biblio_id = i.biblio_id'
		. ($params['id'] == ''? '' : ' WHERE is_return = 0 and u.member_id = \'' . $this->db->real_escape_string($params['id']) . '\'')
		. ' ORDER BY u.due_date'
		;
		// var_dump($query);
		// die();
		$list = array();
		$result = $this->db->query($query);
		while ($row = $result->fetch_assoc())
		{
			$list[] = $row;
		}
		return $list;
    }
}

class sejarah
{
	private $db;

	/**
	 * Constructor - open DB connection
	 *
	 * @param none
	 * @return database
	 */
	function __construct()
	{
		$conf = json_decode(file_get_contents('configuration.json'), TRUE);
		$this->db = new mysqli($conf["host"], $conf["user"], $conf["password"], $conf["database"]);
	}

	/**
	 * Destructor - close DB connection
	 *
	 * @param none
	 * @return none
	 */
	function __destruct()
	{
		$this->db->close();
	}

	/**
	 * Get the list of users
	 *
	 * @param none or user id
	 * @return list of data on JSON format
	 */
	function get($params)
	{
		$query = 'SELECT u.member_id AS id'
		. ', u.loan_date AS loan_date'
		. ', u.due_date AS due_date'
		. ', u.item_code AS item_code'
        . ', b.title AS judul'
        . ', b.image AS image'
		. ', u.is_return AS is_return'
		. ' FROM loan AS u'
		. ' left outer join item i on i.item_code = u.item_code'
		. ' left outer join biblio b on b.biblio_id = i.biblio_id'
		. ($params['id'] == ''? '' : ' WHERE u.member_id = \'' . $this->db->real_escape_string($params['id']) . '\'')
		. ' ORDER BY u.is_return'
		;
		// var_dump($query);
		// die();
		$list = array();
		$result = $this->db->query($query);
		while ($row = $result->fetch_assoc())
		{
			$list[] = $row;
		}
		return $list;
    }
}

