<?php

	class FoxyListProductPageExtension extends DataExtension{

		static $belongs_many_many = array(
			'Customers' => 'Member');
			
		public function AddToWishList(){
			$link = "/".WishList::get()->first()->URLSegment."/add/".$this->owner->ID;
			return $link;
		}

	}