<?php

namespace PostgreSQLTutorial;

class PostgreSQLCreateTable
{
	private $pdo;
	
	public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
	
	 public function createTables()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS urls (
                   id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                   name character varying(255),
				   created_at timestamp
        );
				CREATE TABLE IF NOT EXISTS url_checks (
				id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY, 
				url_id bigint REFERENCES urls (id),
				status_code integer, 
				h1 character varying(1000), 
				title text, 
				description text, 
				created_at timestamp				
		);	
		';

        $this->pdo->exec($sql);

        return $this;
    }
	
	public function deleteTable($table){
	
	$sql = "DROP TABLE {$table};";
	$this->pdo->exec($sql);

	return $this;
	}
	
	
	public function deleteAllTable(){
	
	$sql = "DROP TABLE url_checks;";
	$this->pdo->exec($sql);
	$sql2 = "DROP TABLE urls;";
	$this->pdo->exec($sql2);
	return $this;
	}
	
	public function addUrl($url){
	$sql = 'INSERT INTO urls 
	(name, created_at) 
	VALUES (:url, :created_at)';
	
    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':url', $url['name']);
	$stmt->bindValue(':created_at', $url['created_at']);

    $stmt->execute();
    return $this->pdo->lastInsertId('urls_id_seq');
}
	public function getUrls(){
	$sql = 'SELECT * FROM urls;';
		
	$stmt = $this->pdo->query($sql);
	$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

	return $result;
	}
	
	public function getLastUrl(){
	$sql = 'SELECT * FROM urls ORDER BY id DESC, id DESC LIMIT 1;';
	$stmt = $this->pdo->query($sql);
	return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	public function getUrl($id){
	$sql = "SELECT * FROM urls WHERE id = {$id};";
	$stmt = $this->pdo->query($sql);
	$result = $stmt->fetch(\PDO::FETCH_ASSOC);
	return $result;
	}
	public function seorchUrlName($name){

	$sql = "SELECT * FROM urls WHERE name = '{$name}';";
	$stmt = $this->pdo->query($sql);
	return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	public function addChek($data){
	$sql = 'INSERT INTO url_checks 
	(url_id, created_at, h1, title, description,status_code) 
	VALUES (:url_id, :created_at,:h1, :title, :description,:status_code)';
	$stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':url_id', $data['url_id']);
	$stmt->bindValue(':created_at', $data['created_at']);
	$stmt->bindValue(':h1', $data['h1']);
	$stmt->bindValue(':title', $data['title']);
	$stmt->bindValue(':description', $data['description']);
	$stmt->bindValue(':status_code', $data['status_code']);
    $stmt->execute();
	}
	
	public function getCheckForCurrentUrl($id){

	$sql = "SELECT * FROM url_checks WHERE url_id = {$id} ORDER BY id DESC;";
		
	$stmt = $this->pdo->query($sql);
	$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
	return $result;
	}
	
	public function getLastChekTime(){
	$sql = 'SELECT DISTINCT ON (url_id) url_id, created_at, status_code
    FROM url_checks
    ORDER BY url_id, created_at DESC;'; 
	
	$stmt = $this->pdo->query($sql);
	$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

	return $result;
	}
}