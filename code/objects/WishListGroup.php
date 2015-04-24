<?php

	class WishListGroup extends DataObject{

		private static $db = array(
			'Title' => 'Varchar(255)'
        );

		private static $has_one = array(
			'Member' => 'Member'
		);

		private static $many_many = array(
			'Products' => 'ProductPage'
        );

        public function getCMSValidator() {
            return new RequiredFields(array('Title'));
        }

		public function getCMSFields(){
			$fields = FieldList::create(
				array(
					TextField::create('Title')->setTitle('Title')
				)
			);
			return $fields;
		}

		public function getAddNewFields(){
			return FieldList::create(
				TextField::create('Title', 'Title'),
				HiddenField::create('MemberID')->setValue(Member::currentUserID())
			);
		}

	}
