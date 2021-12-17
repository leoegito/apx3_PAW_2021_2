<?php

class Usuário{



}

class Equipamento{

}

class Movimentação{

}

$db = new PDO('mysql:dbname=db_apx3;host=localhost','root','');

$u1 = new Usuário($db, "Flávio", "Professor");

$e1 = new Equipamento($db, "Dell", "Laptop");

$e2 = new Equipamento($db, "Kit ferro de solda", "Ferramenta");

$m =  new Movimentação($db);

?>