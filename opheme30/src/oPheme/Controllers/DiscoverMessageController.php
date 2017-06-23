<?php

namespace oPheme\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use oPheme\Models\DiscoverMessage;
use oPheme\Transformer\DiscoverMessageTransformer;
use oPheme\Classes\Traits\DiscoverTrait;
use oPheme\Models\Traits\ValidationTrait;

class DiscoverMessageController extends BaseController
{
	use ValidationTrait, DiscoverTrait;
	
	public function __construct()
    {
		parent::__construct();
    }
	
	public function showTestMessage()
	{
		$sample = DB::connection('mongodb')->collection('message')->first();
		dd($sample);
	}
	
	// List By Discover
	public function adminListByDiscover ( $discoverId )
	{
		$before = Input::get('before', '9999-12-31 23:59:59');
		$after = Input::get('after', '0000-01-01 00:00:00');
		
		// Validate the input
		$this->validationRules = array( 'before' => array('date_format:Y-m-d H:i:s'), 'after' => array('date_format:Y-m-d H:i:s') );
		$this->validate(array('before' => $before, 'after' => $after));
		
		// Uses strtotime to normalise the given date (example 2015-06-1015:00:00 - will filter correctly)
		$before = date("Y-m-d H:i:s",  strtotime($before));
		$after = date("Y-m-d H:i:s",  strtotime($after));
		
		
		list( $per_page, $current ) = $this->getPaginationDetails();
		
		$discoverMessages = DiscoverMessage::with('socialMediaPlatform')
								->with('message')
								->where('discover_id', '=', $discoverId)
								// it needs to be created_at because the UI check will be happening in real time
								// and the Instagram messages will normally be processed a bit later than their original post time
								->whereBetween('created_at', array($after, $before))
								->orderBy('message_datestamp', 'desc')
								->take($per_page)
								->skip($current)
								->get();

		$cursor = $this->getCursor($discoverMessages, $per_page, $current);
		return $this->respondWithCursor( $discoverMessages, new DiscoverMessageTransformer, $cursor );
	}
	
	// List By Discover (By Current User)
	public function meListByDiscover ( $discoverId )
	{
		$user = $this->getCurrentUser();
		$this->checkUserDiscover( $user, $discoverId );
		return $this->adminListByDiscover( $discoverId );
	}
	
	// Count By Discover
	public function adminCountByDiscover ( $discoverId )
	{
		$before = Input::get('before', '9999-12-31 23:59:59');
		$after = Input::get('after', '0000-01-01 00:00:00');
		
		// Validate the input
		$this->validationRules = array( 'before' => array('date_format:Y-m-d H:i:s'), 'after' => array('date_format:Y-m-d H:i:s') );
		$this->validate(array('before' => $before, 'after' => $after));
		
		// Uses strtotime to normalise the given date (example 2015-06-1015:00:00 - will filter correctly)
		$before = date("Y-m-d H:i:s",  strtotime($before));
		$after = date("Y-m-d H:i:s",  strtotime($after));
		
		$count = DiscoverMessage::where('discover_id', '=', $discoverId)
								// it needs to be created_at because the UI check will be happening in real time
								// and the Instagram messages will normally be processed a bit later than their original post time
								->whereBetween('created_at', array($after, $before))
								->count();
		$response = array( 'count' => $count);
		// return a code 200 header and pass back the updated item
		return $this->setHttpStatusCode('200')->respondSimpleArray( $response );
	}
	
	// Count By Discover (By Current User)
	public function meCountByDiscover ( $discoverId )
	{
		$user = $this->getCurrentUser();
		$this->checkUserDiscover( $user, $discoverId );
		return $this->adminCountByDiscover( $discoverId );
	}
}
