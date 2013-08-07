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
				$wishList->URLSegment = 'wish-list';
				$wishList->Status = 'Published';
				$wishList->write();
				$wishList->publish('Stage', 'Live');
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
		 * @return void
		 */
		public function getWishList($memberID=null){
			if($member = Member::currentUser()){
				$wishList = $member->getManyManyComponents('WishListItems');
				return $wishList;
			}else{
				return false;
			}
		}

		function canCreate($member = null){
			if(WishList::get()->first()){
				return false;
			}
			return true;
		}

		function canEdit($member = null){
			if(Member::currentUser()){
				return true;
			}
			return false;
		}

		function canView($member = null){
			return true;
		}

		function canDelete($member = null){
			if(Permission::check('ADMIN')){
				return true;
			}
			return false;
		}

		function canDeleteFromLive($member = null){
			if(Permission::check('ADMIN')){
				return true;
			}
			return false;
		}

	}

	class WishList_Controller extends Page_Controller{

		static $allowed_actions = array(
			'add',
			'remove',
			'view');

		/**
		 * add function.
		 * 
		 * @access public
		 * @return void
		 */
		public function add(){
			$productID = $this->request->param('ID');
			if($product = $this->getProduct($productID)){
				$quantity = (int) $this->request->param('OtherID');
				if($quantity==0){ $quantity = 1; }
			
				$member = $this->getCurrentMember();
				$wishList = $member->WishListItems();
				$wishList->add($product, array('Quantity' => $quantity));
				$this->redirect($this->Link());
			}
			return $this->redirectBack();
		}
		
		/**
		 * remove function.
		 * 
		 * @access public
		 * @return void
		 */
		public function remove(){
			$productID = (int) $this->request->param('ID');
			$product = ProductPage::get()
				->byID($productID);
			$member = $this->getCurrentMember();
			$wishes = $member->WishListItems();
			$wishes->remove($product);
			$this->redirect($this->Link());
		}
		
		public function view(){
			if($member = $this->canViewWishList()){
				$wishList = $member->WishListItems();
				return $this->customise(array(
					'Title' => $this->Data()->Title." - ".$member->FirstName." ".$member->Surname,
					'WishList' => $wishList));
			}
			return $this->customise(array(
				'Title' => 'Wish List Permissions Error',
				'Content' => '<p>You don\'t have permission to view this wish list.</p>',
				'WishList' => false));
		}
		
		/**
		 * getCurrentMember function.
		 * 
		 * @access private
		 * @return void
		 */
		private function getCurrentMember(){
			return Member::currentUser();
		}
		
		/**
		 * getProduct function.
		 * 
		 * @access private
		 * @param mixed $productID
		 * @return void
		 */
		private function getProduct($productID){
			if($product = ProductPage::get()->byID($productID)){
				return $product;
			}
			return false;
		}
		
		public function canViewWishList(){
			$memberID = $this->request->param('ID');
			$wishListKey = $this->request->param('OtherID');
			if($member = Member::get()->filter(array('ID' => $memberID))->first()){
				if($member->PublicWishList==true||$member->WishListKey==$wishListKey){
					return $member;
				}
			}
			return false;
		}

	}