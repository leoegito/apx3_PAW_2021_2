<?php 

class Movimentação{

	private $db;
	private int $id;
	private DateTime $data;
	private int $quantidade;
	private int $Equipamento_id;
	private int $Usuário_id;

	//Como somente $db é passado na questão, este é o construtor
	//parcial apenas para a Q2
	public function __construct($db, int $quantidade, int $Equipamento_id, int $Usuário_id){

		$this->db = $db;
		// $this->data = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
		$this->quantidade = $quantidade;
		$this->Equipamento_id = $Equipamento_id;
		$this->Usuário_id = $Usuário_id;

	}

	//devem retornar o ID do registro recém-inserido
	public function registra_empréstimo(){
		//

	}

	public function registra_devolução(){
		//deve verificar:
			//qtd_devolvida > qtd_emprestada, entao return -1;
				//return -1 == erro
			//

	}



}

$m->registra_empréstimo();

?>