<?php

	/**
	 * WishList class.
	 *
	 * @extends Page
	 */
	class WishList extends Page{

		public static $singular_name = 'Wish List';
		public static $plural_name = 'Wish Lists';
		public static $description = 'Wish List page to track individual user\'s favorite items';

		public static $allowed_children = array();
		public static $default_child = '';
		public static $default_parent = null;
		public static $can_be_root = true;

		public static $db = array();
		public static $has_one = array();
		public static $has_many = array();
		public static $many_many = array();
		public static $many_many_extraFields = array();
		public static $beongs_many_many = array();

		public static $casting = array();
		public static $indexes = array();
		public static $defaults = array();
		public static $default_records = array();

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

		static function randomPassword() {
		    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		    $pass = array(); //remember to declare $pass as an array
		    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		    for ($i = 0; $i < 255; $i++) {
		        $n = rand(0, $alphaLength);
		        $pass[] = $alphabet[$n];
		    }
		    return implode($pass); //turn the array into a string
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

		//Only allow SuperAdmins to remove this page
		function canDeleteFromLive($member = null){
			if(Permission::check('ADMIN')){
				return true;
			}
			return false;
		}

	}

	class WishList_Controller extends Page_Controller{

		static $allowed_actions = array(
			'index',
			'add',
			'remove',
			'view',
			'magiclist');

		public function index(){
			if($member = Member::currentUser()){
				return $this->customise(array(
					'MergeLists' => $this->ListCheck(),
					'WishList' => $member->WishListItems()));
			}elseif($Anonymous = AnonymousWishList::get()->filter(array('Key' => Cookie::get('WishList')))->first()){
				return $this->customise(array(
					'MergeLists' => false,
					'WishList' => $Anonymous->Products()));
			}else{
				$products = ProductHolder::get()->first();
				return $this->customise(array(
					'MergeLists' => false,
					'Content' => '<a href="/Security/login?BackURL='.$this->Link().'">Login</a> to view your wish list, or <a href="/'.$products->URLSegment.'/">Browse Products</a> to get started.',
					'WishList' => false));
			}
		}

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

				if($member = $this->getCurrentMember()){
					$wishList = $member->WishListItems();
					$wishList->add($product, array('Quantity' => $quantity));
					$this->redirect($this->Link());
				}else{
					if($key = Cookie::get('WishList')){
						$anonymous = AnonymousWishList::get()->filter(array('Key' => $key))->first();
					}else{
						$key = WishList::randomPassword();
						Cookie::set('WishList',$key);
						$anonymous = AnonymousWishList::create();
						$anonymous->Key = $key;
						$anonymous->write();
						$anonymous = AnonymousWishList::get()->filter(array('Key' => $key))->first();
					}
					$wishes = $anonymous->Products();
					$wishes->add($product);
					$this->redirect($this->Link());
				}
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
				if($wishList->count()<1){
					$content = 'No items in this wish list';
					$wishList = false;
				}else{
					$content = false;
				}
				return $this->customise(array(
					'Title' => $this->Data()->Title." - ".$member->FirstName." ".$member->Surname,
					'Content' => $content,
					'WishList' => $wishList));
			}
			return $this->customise(array(
				'Title' => 'Wish List Permissions Error',
				'Content' => '<p>You don\'t have permission to view this wish list.</p>',
				'WishList' => false));
		}

		public function magiclist(){
			$cookieKey = Cookie::get('WishList');
			$anonymousList = AnonymousWishList::get()->filter(array('Key' => $cookieKey))->first();
			$anonymousWishList = $anonymousList->Products();
			$member = Member::currentUser();
			$memberWishList = $member->WishListItems();
			$memberWishList->addMany($anonymousWishList);
			Cookie::forceExpiry('WishList');
			return $this->redirect($this->Link());

		}

		public function ListCheck(){
			if($cookie = Cookie::get('WishList')&&$member = Member::currentUser()){
				if($cookie == $member->WishListKey){
					return true;
				}else{
					return false;
				}
			}
			return false;
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