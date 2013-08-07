<?php

	class FoxyListMemberExtension extends DataExtension{

		static $db = array(
			'WishListKey' => 'Varchar(255)',
			'PublicWishList' => 'Boolean');
		
		static $many_many = array(
			'WishListItems' => 'ProductPage');

		static $many_many_extraFields = array(
			'WishListItems' => array(
				'Quantity' => 'Int'));
			
		public function onBeforeWrite(){
			if(!$this->owner->WishListKey){
				$this->owner->WishListKey = $this->randomPassword();
			}
			parent::onBeforeWrite();
		}
		
		function randomPassword() {
		    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		    $pass = array(); //remember to declare $pass as an array
		    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		    for ($i = 0; $i < 255; $i++) {
		        $n = rand(0, $alphaLength);
		        $pass[] = $alphabet[$n];
		    }
		    return implode($pass); //turn the array into a string
		}

	}