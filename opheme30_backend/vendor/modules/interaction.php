<?php
	
	$allOfThem = $site->socialMedia->getInteraction($site->user->get('account', 'id'), null, null, 0, 'added_at', 'desc');
	
	$moduleData['smInteraction']['history'] = $allOfThem;