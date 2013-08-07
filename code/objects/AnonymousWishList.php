<?php

	class AnonymousWishList extends DataObject{
		
		static $db = array(
			'Key' => 'Varchar(255)');
		
		static $many_many = array(
			'Products' => 'ProductPage');
		
	}