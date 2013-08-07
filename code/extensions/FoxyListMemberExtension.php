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
				$this->owner->WishListKey = WishList::randomPassword();
			}
			parent::onBeforeWrite();
		}

	}