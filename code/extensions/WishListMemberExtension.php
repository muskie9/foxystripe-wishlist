<?php

	class WishListMemberExtension extends DataExtension{

		private static $many_many = array(
			'WishListGroups' => 'WishListGroup'
        );

		public function onBeforeWrite(){
			if(!$this->owner->WishListKey){
				$this->owner->WishListKey = WishList::randomPassword();
			}
			parent::onBeforeWrite();
		}

	}
