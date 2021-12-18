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
		return (int) $this->id;
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

			$this->setId((int) $resultID[0]['id']);

			echo '<p>Usuário de id: ' .$this->getId() .' cadastrado com sucesso.</p>';

		} else {

			$this->setId( (int) $results[0]['id']);
			
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
		return (int) $this->id;
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

			$this->setId( (int) $results[0]['id']);

			echo '<p>Equipamento com nome e categoria já existente no banco de dados. Nada foi alterado ou inserido no BD.</p>';
		}


	}

}

class Movimentação{

	private $db;
	private int $id;
	public $data;
	public int $quantidade;
	public int $Equipamento_id;
	public int $Usuario_id;

	//Como somente $db é passado na questão, este é o construtor
	//parcial apenas para a Q2
	public function __construct($db){
		$this->db = $db;

	}

	//Getters & Setters
	public function setId(int $id){
		$this->id = $id;
	}

	public function getId(){
		return $this->id;
	}

	//devem retornar o ID do registro recém-inserido
	//retorna -1 quando há erro
	public function registra_empréstimo(Equipamento $equipamento,int $quantidade, Usuário $usuário){
		//
		if(!isset($equipamento) || !isset($usuário) || $quantidade < 0){
			echo '<p>Dados incorretos para o empréstimo.</p>';
			return -1;
		}

		$this->quantidade = (int) $quantidade;
		$this->Equipamento_id = $equipamento->getId();
		$this->Usuario_id = $usuário->getId();

		//Poderia alterar de outra forma, mas essa é mais simples e faz sentido alterar direto no servidor
		date_default_timezone_set('America/Sao_Paulo');
		$this->data = new DateTime('now');


		$stmt = $this->db->prepare("INSERT INTO `db_apx3`.`movimentação` (id, Data, Quantidade, Equipamento_id, Usuário_id) VALUES (null, :Data, :Quantidade, :Equipamento_id, :Usuario_id);");


		$result = $stmt->execute([
			':Data'=>$this->data->format('Y-m-d H:i:s'),
			':Quantidade'=>$this->quantidade,
			':Equipamento_id'=>$this->Equipamento_id,
			':Usuario_id'=>$this->Usuario_id
		]);

		if($result){

			$stmtID = $this->db->prepare("SELECT MAX(`id`) AS `id` FROM `db_apx3`.`movimentação`;");

			$stmtID->execute();

			$resultID = $stmtID->fetchAll(PDO::FETCH_ASSOC);

			$this->setId((int) $resultID[0]['id']);

			return $this->getId();

		} else{
			return -1;
		}

	}

	public function registra_devolução(Equipamento $equipamento,int $quantidade, Usuário $usuário){
		//deve verificar:
			//qtd_devolvida > qtd_emprestada, entao return -1;
				//return -1 == erro
			//A devolução, de acordo com o enunciado, é sempre
			//uma movimentação com número/quantidade NEGATIVO
		if(!isset($equipamento) || !isset($usuário) || $quantidade < 0){
			echo '<p>Dados incorretos para a devolução.</p>';
			return;
		}

		//Transformando a qtd da Classe em uma qtd negativa para
		//devolução
		$this->quantidade = (int) ($quantidade * -1);
		$this->Equipamento_id = $equipamento->getId();
		$this->Usuario_id = $usuário->getId();
		date_default_timezone_set('America/Sao_Paulo');
		$this->data = new DateTime('now');

		$stmtQTD = $this->db->prepare("
			SELECT SUM(Quantidade) AS Quantidade FROM `db_apx3`.`movimentação` WHERE Equipamento_id = :Equipamento_id AND Usuário_id = :Usuario_id;
		");

		$stmtQTD->execute([
			':Equipamento_id'=>$this->Equipamento_id,
			':Usuario_id'=>$this->Usuario_id
		]);

		$quantidadeEmprestada = $stmtQTD->fetchAll(PDO::FETCH_ASSOC);

		//Validação da quantidade devolvida x qtd emprestada
		if($quantidadeEmprestada[0]['Quantidade'] < $quantidade){
			echo '<p>Quantidade devolvida é maior que a quantidade emprestada.</p>';
			return -1;
		} else if (!$quantidadeEmprestada){
			echo '<p>Equipamento ou usuário não possui empréstimo registrado. Por favor tente novamente com os dados corretos.</p>';
			return;
		}

		//Caso os dados sejam válidos, não caiu em nenhum return
		$stmt = $this->db->prepare("INSERT INTO `db_apx3`.`movimentação` (id, Data, Quantidade, Equipamento_id, Usuário_id) VALUES (null, :Data, :Quantidade, :Equipamento_id, :Usuario_id);");


		$result = $stmt->execute([
			':Data'=>$this->data->format('Y-m-d H:i:s'),
			':Quantidade'=>$this->quantidade,
			':Equipamento_id'=>$this->Equipamento_id,
			':Usuario_id'=>$this->Usuario_id
		]);

		if($result){

			$stmtID = $this->db->prepare("SELECT MAX(`id`) AS `id` FROM `db_apx3`.`movimentação`;");

			$stmtID->execute();

			$resultID = $stmtID->fetchAll(PDO::FETCH_ASSOC);

			$this->setId((int) $resultID[0]['id']);

			return $this->getId();

		} else{
			echo '<p>Erro na inserção no BD.</p>';
			return;
		}		


	}

	function consulta_Empréstimo($db, Equipamento $equipamento){

		if(!$db || !$equipamento){
			return;
		}

		$this->db = $db;
		$this->Equipamento_id = $equipamento->getId();

		$stmt = $this->db->prepare("
			SELECT b.Nome, SUM(Quantidade) AS Quantidade FROM movimentação as a JOIN usuário as b WHERE a.Equipamento_id = :Equipamento_id AND a.Usuário_id = b.id GROUP BY a.Usuário_id;
		");

		$stmt->execute([
			':Equipamento_id'=>$this->Equipamento_id
		]);

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		print("<pre>" .print_r($results, true) ."</pre>");

		return $results;
	}

}//fim Class

$db = new PDO('mysql:dbname=db_apx3;host=localhost','root','');

$u1 = new Usuário($db, "Flávio", "Professor");

$u1->save();

$e1 = new Equipamento($db, "Dell", "Laptop");

$e1->save();

$e2 = new Equipamento($db, "Kit ferro de solda", "Ferramenta");

$e2->save();

$m =  new Movimentação($db);

$m->registra_empréstimo($e1, 1, $u1);

$m->registra_empréstimo($e1, 4, $u1);
$m->registra_empréstimo($e2, 2, $u1);
$m->registra_Devolução($e1, 3, $u1);

$m->consulta_Empréstimo($db, $e1);

?>