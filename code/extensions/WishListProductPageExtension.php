<?php

	class WishListProductPageExtension extends DataExtension{

		private static $belongs_many_many = array(
			'WishListGroups' => 'WishListGroup'
        );

        //TODO find a better way to build the link
		public function AddToWishList(){
			$link = "/".WishList::get()->first()->URLSegment."/add/".$this->owner->ID;
			return $link;
		}

	}

	class WishListProductPageControllerExtension extends Extension{

		private static $allowed_actions = array(
			'WishListForm',
			'WishListAdd'
		);

		public function WishListForm()
		{

			if (Member::currentUser()) {

				$source = function () {
					return WishlistGroup::get()->filter('MemberID', Member::currentUserID())->map()->toArray();
				};

				$listSelection = DropdownField::create('WishListGroupID', 'Wish List', $source());
				$listSelection->setEmptyString('Select Wish List');

				$listSelection->useAddNew('WishListGroup', $source, null, null, true);

				$fields = FieldList::create(
					$listSelection,
					HiddenField::create('ProudctID')->setValue($this->owner->data()->ID)
				);

				$actions = FieldList::create(
					FormAction::create('WishListAdd', 'Add To Wish List')
				);

				$form = Form::create($this->owner, 'WishListAddForm', $fields, $actions);
			}else{
				Session::set('BackURL', $this->owner->Link());
				$form = MemberLoginForm::create($this->owner, 'WishListForm');
			}

			return $form;

		}

		public function WishListAdd($data, $form){

			var_dump($data);

		}

	}
