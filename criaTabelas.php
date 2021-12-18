<?php

$db = new PDO('mysql:dbname=db_apx3;host=localhost','root','');
createTable($db);

function createTable($db){
	
	$stmt = $db->prepare(
		"
		CREATE TABLE IF NOT EXISTS `db_apx3`.`Equipamento` (
			id INT auto_increment PRIMARY KEY,
			Nome varchar(45) NOT NULL,
			Categoria varchar(10) NOT NULL
		);

		CREATE TABLE IF NOT EXISTS `db_apx3`.`Usuário` (
			id INT auto_increment PRIMARY KEY,
			Nome varchar(45) NOT NULL,
			Perfil varchar(10) NOT NULL
		);

		CREATE TABLE IF NOT EXISTS `db_apx3`.`Movimentação` (
			id INT auto_increment PRIMARY KEY,
			Data DATETIME NOT NULL,
			Quantidade INT NOT NULL,
			Equipamento_id INT NOT NULL,
			Usuário_id INT NOT NULL,
			FOREIGN KEY (`Equipamento_id`)
			REFERENCES `db_apx3`.`Equipamento` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			FOREIGN KEY (`Usuário_id`)
			REFERENCES `db_apx3`.`Usuário` (`id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION
		);
		"
	);

	if(!$stmt->execute()){
		print_r($stmt->errorInfo());
	} else {
		echo 'Tabelas criadas com sucesso!';
	}

}
