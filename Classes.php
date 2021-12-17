<?php

class Usuário{

	private int $id;
	private string $nome;
	private string $perfil;
	private $db;

	public function __construct($db, string $nome, string $perfil){

		$this->db = $db;
		$this->nome = $nome;
		$this->perfil = $perfil;

	}

	//Getters & setters
	public function getNome(){
		return $this->nome;
	}

	public function getPerfil(){
		return $this->perfil;
	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}


	//Salva usuário no BD
	public function save(){

		//verifica se o usuário já existe no banco de dados,
		//insere apenas caso não exista
		//seria melhor caso fosse passado o ID pela classe e não
		//pelo BD, porém seguindo o enunciado da questão, considerei
		//um usuário existente se tiver o nome e categoria iguais

		$stmt = $this->db->prepare(
			"
			SELECT id FROM `usuário` WHERE Nome = :nome AND Perfil = :perfil;
			"
		);

		$stmt->execute([
			':nome'=>$this->getNome(),
			':perfil'=>$this->getPerfil()
		]);

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(!isset($results[0]) || count($results) == 0){

			$stmt2 = $this->db->prepare(
				"
				INSERT INTO `usuário` VALUES (null, :nome, :perfil);
				"
			);


			$stmt2->execute([
				':nome'=>$this->getNome(),
				':perfil'=>$this->getPerfil()
			]);

			$stmtID = $this->db->prepare("SELECT MAX(id) AS id FROM `usuário`;");

			$stmtID->execute();

			$resultID = $stmtID->fetchAll(PDO::FETCH_ASSOC);

			var_dump($resultID);

			$this->setId((int) $resultID[0]['id']);

			echo '<p>Usuário de id: ' .$this->getId() .' cadastrado com sucesso.</p>';

		} else {
			echo '<p>O usuário já existe no banco de dados. Nada foi alterado ou inserido no BD.</p>';
		}


	}	

}

class Equipamento{

	private int $id;
	private string $nome;
	private string $categoria;
	private $db;

	public function __construct($db, string $nome, string $categoria){

		$this->db = $db;
		$this->nome = $nome;
		$this->categoria = $categoria;

	}

	//Getters & setters
	public function getNome(){
		return $this->nome;
	}

	public function getCategoria(){
		return $this->categoria;
	}

	public function getId(){
		return $this->id;
	}

	public function setId(int $id){
		$this->id = $id;
	}

	//Salva o equipamento no BD
	public function save(){

		$stmt = $this->db->prepare(
			"
			SELECT id FROM `equipamento` WHERE Nome = :nome AND Categoria = :categoria;
			"
		);

		$stmt->execute([
			':nome'=>$this->getNome(),
			':categoria'=>$this->getCategoria()
		]);

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(!isset($results[0]) || count($results) == 0){

			$stmt2 = $this->db->prepare(
				"
				INSERT INTO `equipamento` VALUES (null, :nome, :categoria);
				"
			);


			$stmt2->execute([
				':nome'=>$this->getNome(),
				':categoria'=>$this->getCategoria()
			]);

			$stmtID = $this->db->prepare("SELECT MAX(id) AS id FROM `equipamento`;");

			$stmtID->execute();

			$resultID = $stmtID->fetchAll(PDO::FETCH_ASSOC);

			$this->setId((int) $resultID[0]['id']);

			echo '<p>Equipamento de id: ' .$this->getId() .' cadastrado com sucesso.</p>';

		} else {
			echo '<p>Equipamento com nome e categoria já existente no banco de dados. Nada foi alterado ou inserido no BD.</p>';
		}


	}

}

class Movimentação{

	private $db;
	private int $id;
	private DateTime $data;
	private int $quantidade;
	private int $Equipamento_id;
	private int $Usuário_id;

	//Como somente $db é passado na questão, este é o construtor
	//parcial apenas para a Q2
	public function __construct($db){

		$this->db = $db;
		$this->data = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));

	}


}

$db = new PDO('mysql:dbname=db_apx3;host=localhost','root','');

$u1 = new Usuário($db, "Flávio", "Professor");

$u1->save();

$e1 = new Equipamento($db, "Dell", "Laptop");

$e1->save();

$e2 = new Equipamento($db, "Kit ferro de solda", "Ferramenta");

$e2->save();

$m =  new Movimentação($db);

var_dump($m);

?>