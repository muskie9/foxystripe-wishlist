<?php

	/**
	 * WishList class.
	 *
	 * @extends Page
	 */
	class WishList extends Page{

		private static $singular_name = 'Wish List';
        private static $plural_name = 'Wish Lists';
        private static $description = 'Wish List page to track individual user\'s favorite items';

        private static $allowed_children = array();
        private static $default_child = '';
        private static $default_parent = null;
        private static $can_be_root = true;

        private static $db = array();
        private static $has_one = array();
        private static $has_many = array();
        private static $many_many = array();
        private static $many_many_extraFields = array();
        private static $beongs_many_many = array();

        private static $casting = array();
        private static $indexes = array();
        private static $defaults = array();
        private static $default_records = array();

		public function requireDefaultRecords(){
			parent::requireDefaultRecords();
			if(!WishList::get()->first()){
				$wishList = WishList::create();
				$wishList->Title = 'Wish List';
				$wishList->doPublish();
			}
		}

		public function getCMSFields(){
			$fields = parent::getCMSFields();



			return $fields;
		}

		/**
		 * getWishList function.
		 *
		 * @access public
		 * @param mixed $memberID (default: null)
		 * @return mixed
		 */
		public function getWishList($memberID=null){
			if($member = Member::currentUser()){
				$wishList = $member->getManyManyComponents('WishListItems');
				return $wishList;
			}else{
				return false;
			}
		}

		public static function randomPassword() {
		    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		    $pass = array(); //remember to declare $pass as an array
		    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		    for ($i = 0; $i < 255; $i++) {
		        $n = rand(0, $alphaLength);
		        $pass[] = $alphabet[$n];
		    }
		    return implode($pass); //turn the array into a string
		}

		public function canCreate($member = null){
			if(WishList::get()->first()){
				return false;
			}
			return true;
		}

        public function canEdit($member = null){
			if(Member::currentUser()){
				return true;
			}
			return false;
		}

        public function canView($member = null){
			return true;
		}

        public function canDelete($member = null){
			if(Permission::check('ADMIN')){
				return true;
			}
			return false;
		}

		//Only allow SuperAdmins to remove this page
        public function canDeleteFromLive($member = null){
			if(Permission::check('ADMIN')){
				return true;
			}
			return false;
		}

	}

	class WishList_Controller extends Page_Controller{

		private static $allowed_actions = array();



	}
