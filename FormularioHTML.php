<?php
//Q5 - sem utilizar as classes criadas para a EXIBIÇÃO dos dados na tela

$db = new PDO('mysql:dbname=db_apx3;host=localhost','root','');

function listAllUsuarios($db){

	$stmt = $db->prepare(
		"SELECT id, Nome FROM `usuário`;"
	);

	$stmt->execute();

	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $results;

}

function getUsuarioName($db, $id){

	$stmt = $db->prepare(
		"SELECT Nome FROM `usuário` WHERE id = :id;"
	);

	$stmt->execute([
		':id'=>$id
	]);

	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $results[0]['Nome'];

}

function getMovimentacao($db, $Usuario_id){

	$stmt = $db->prepare("
		SELECT b.Nome, b.Categoria, SUM(Quantidade) AS Quantidade FROM movimentação AS a JOIN equipamento as b WHERE a.Equipamento_id = b.id AND a.Usuário_id = :Usuario_id GROUP BY a.Equipamento_id;
	");

	$stmt->execute([
		':Usuario_id'=>$Usuario_id
	]);

	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $results;

}

?>

<html lang="pt-BR">
<head>
	<meta charset="utf-8">
	<title>Q5 APX3 2021.2 - PAW</title>
	<style>
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
			padding: 3px;
		}

		p{
			padding: 5px;
		}

	</style>
</head>
<body>

	<form action="<?php $_SERVER['PHP_SELF']?>" method="POST">

		<?php 

			$db = new PDO('mysql:dbname=db_apx3;host=localhost','root','');

			$usuarios = listAllUsuarios($db);
				
			echo "<label for='usuario'>1.</label>";
			echo "<select id='usuario' name='usuario'>";
			for($i = 0; $i<sizeof($usuarios); $i++){
				$option = "<option default='' value='";
				$option .= $usuarios[$i]['id'] ."'>";
				$option .= $usuarios[$i]['Nome'];
				$option .= "</option>";
				echo $option;
			}
				
			echo "</select>";
			echo "<button type='submit' value='Enviar'>Enviar</button>";
			echo "</form>";

			if(isset($_POST['usuario'])){

				$emprestimos = getMovimentacao($db, (int) $_POST['usuario']);

				$nome = getUsuarioName($db, (int) $_POST['usuario']);

				echo "<p>Exibindo equipamentos emprestados (<strong>saldo</strong>) com o usuário " .$nome .":</p>";

				echo "<table style='padding: 5px; border: 3px solid black;'>";

					echo "<tr>";
						echo "<th>Equipamento</th>";
						echo "<th>Categoria</th>";
						echo "<th>Quantidade</th>";
					echo "</tr>";
					foreach($emprestimos as $row => $value){
						echo "<tr>";
						foreach($emprestimos[$row] as $col){
							echo "<td>" .$col ."</td>";
						}
						echo "</tr>";
					}

				echo "</table";
			}

		?>
		
</body>
</html>