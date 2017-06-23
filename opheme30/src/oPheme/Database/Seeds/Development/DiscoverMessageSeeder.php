<?php

namespace oPheme\Database\Seeds\Development;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use oPheme\Models\Discover;
use oPheme\Models\DiscoverMessage;
use oPheme\Models\SocialMediaPlatform;
use oPheme\Models\Message;

class DiscoverMessageSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();
		
		$this->command->info( 'fake discover messages being seeded, Please wait' );
		
		// Get the links data (test discover)
		$discover = Discover::where( "name", "=", "Cambridge, Any Books?" )->first();
		
		$filecontents = File::get(base_path() . '/src/oPheme/Database/Seeds/Development/TestMessages.json');
		$messagesData = json_decode($filecontents, true);
		
		foreach($messagesData as $messageData) {
			$message = new Message($messageData);
			$message->save();

			$discoverMessage = new DiscoverMessage;
			$discoverMessage->backend_message_id = $message->_id;
			$discoverMessage->socialmediaplatform_message_id = $message->id;
			$discoverMessage->message_datestamp = $message->opheme_backend['created_timestamp'];
			
//			// get the twitter created_at format, create a carbon obj, change timezone
//			$carbonDateObj = Carbon::createFromFormat("D M j H:i:s T Y", $message->m_created_at);
//			$carbonDateObj->setTimezone('UTC');
//			$discoverMessage->message_datestamp = $carbonDateObj->toDateTimeString();

			
			// add the links to discover / social media platform
			$discoverMessage->discover()->associate($discover);
			
			$socialMediaPlatform = SocialMediaPlatform::where( "name", "=", $message->opheme_backend['social_media_platform'] )->first();
			$discoverMessage->socialMediaPlatform()->associate($socialMediaPlatform);
			
			$discoverMessage->save();
		}
	}

}
