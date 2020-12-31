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
	function data($params)
	{
		$query = 'SELECT u.member_id AS id'
		. ', u.loan_date AS loanDate'
		. ', u.due_date AS dueDate'
		. ', u.item_code AS itemCode'
		. ', b.title AS judul'
		. ', u.is_return AS isReturn'
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

	function history($params)
	{
		$query = 'SELECT u.member_id AS id'
		. ', u.loan_date AS loanDate'
		. ', u.due_date AS dueDate'
		. ', u.item_code AS itemCode'
		. ', b.title AS judul'
		. ', u.is_return AS isReturn'
		. ' FROM loan AS u'
		. ' left outer join item i on i.item_code = u.item_code'
		. ' left outer join biblio b on b.biblio_id = i.biblio_id'
		. ($params['id'] == ''? '' : ' WHERE is_return = 1 and u.member_id = \'' . $this->db->real_escape_string($params['id']) . '\'')
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

	function filter($params)
	{	
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/perpus/images/docs/";
		$query = 'SELECT b.title'
		.', ma.author_name'
		.', IFNULL(ma.author_year, "") author_year'
		.', IFNULL(b.edition, "") edition, IFNULL(b.isbn_issn, "") isbn_issn'
		.', IFNULL(mb.publisher_name, "") publisher_name, IFNULL(b.publish_year, "") publish_year, IFNULL(b.collation, "") collation'
		.', IFNULL(b.series_title, "") series_title, IFNULL(b.call_number, "") call_number, IFNULL(ml.language_name, "") language_name'
		.', IFNULL(mp.place_name, "") place_name, IFNULL(b.classification, "") classification, IFNULL(b.notes, "") notes, IFNULL(b.image, "") image'
		.', IFNULL(b.file_att, "") file_att'
		.' FROM biblio AS b '
		.'left outer join biblio_author ba on ba.biblio_id = b.biblio_id '
		.'left outer join mst_author ma on ma.author_id = ba.author_id '
		.'left outer join mst_publisher mb on mb.publisher_id = b.publisher_id '
		.'left outer join mst_language ml on ml.language_id = b.language_id '
		.'left outer join mst_place mp on mp.place_id = b.publish_place_id '
		. ($params['search'] == ''? '' : ' WHERE b.title LIKE \'%' . $this->db->real_escape_string($params['search']) . '%\'')
		;
		// var_dump($query);
		// die();
		$list = array();
		$result = $this->db->query($query);
		while ($row = $result->fetch_assoc())
		{
			$row["image"] = "$actual_link".$row["image"];
			$list[] = $row;
		}
		return $list;
	}

	function login($params)
	{	
		$query1 = 'SELECT mpasswd FROM member'
		. ($params['id'] == ''? '' : ' WHERE member_id = \'' . $this->db->real_escape_string($params['id']) . '\'')
		;
		$query = 'SELECT member_name AS memberName'
		. ', gender AS gender'
		. ', birth_date AS birthDate'
		. ', member_address AS memberAddress'
		. ', inst_name AS instName'
		. ', member_phone AS memberPhone'
		. ' FROM member'
		. ($params['id'] == ''? '' : ' WHERE member_id = \'' . $this->db->real_escape_string($params['id']) . '\'')
		;
		// var_dump($query);
		// die();
		$resultPass = $this->db->query($query1);
		
		if (password_verify($params['password'], $resultPass->fetch_assoc()["mpasswd"])) {
			$result = $this->db->query($query);
			return $result->fetch_assoc();
		} else {
			return "Login Failed. Id or password not found.";
		}
		return null;
	}
}
