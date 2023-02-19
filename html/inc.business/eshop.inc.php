<?php

function businessEshopAddToCart($id, $count, $username){	
	smart_mysql_query($sql = 'insert into businessEshopcart (businessEshopproductid, count, username) values ("' . $id . '", "' . $count . '", "' . $username . '")', false);
}

function businessEshopGetCartCount(){
	$count = sqlGetRow($sql = 'select count(*) as cnt FROM businessEshopcart WHERE username = "'.($_COOKIE['shop_username']).'"');
	return $count['cnt'];
}

function businessEshopGetCartItems(){
	$items = sqlGetRows('select *, count(p.id) as cnt FROM businessEshopcart c LEFT JOIN businessEshopproduct p ON c.businessEshopproductid = p.id WHERE username = "'.($_COOKIE['shop_username']).'" GROUP BY p.id');
	return $items;
}

function businessEshopEchoCartItems(){
	$items = businessEshopGetCartItems();
	foreach ($items as $item){
		echo '<tr><td>'.$item['name'].'</td></td><td>'.$item['cnt'].'</td><td>'.($item['price']*$item['cnt']).' €</td></tr>';
	}
}
