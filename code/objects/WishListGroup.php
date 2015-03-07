<?php

	class WishListGroup extends DataObject{
		
		private static $db = array(
			'Title' => 'Varchar(255)'
        );
		
		private static $many_many = array(
			'Products' => 'ProductPage'
        );

        public function getCMSValidator() {
            return new RequiredFields(array('Title'));
        }

	}