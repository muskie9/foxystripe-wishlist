<?php

	if(class_exists('ProductPage')){
		Object::add_extension('Member', 'FoxyListMemberExtension');
		Object::add_extension('ProductPage', 'FoxyListProductPageExtension');
	}