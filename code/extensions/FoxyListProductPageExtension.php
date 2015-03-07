<?php

	class FoxyListProductPageExtension extends DataExtension{

		private static $belongs_many_many = array(
			'WishListGroups' => 'WishListGroup'
        );

        //TODO find a better way to build the link
		public function AddToWishList(){
			$link = "/".WishList::get()->first()->URLSegment."/add/".$this->owner->ID;
			return $link;
		}

	}