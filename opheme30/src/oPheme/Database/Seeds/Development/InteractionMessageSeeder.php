<?php

namespace oPheme\Database\Seeds\Development;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use oPheme\Models\Discover;
use oPheme\Models\DiscoverMessage;
use oPheme\Models\Message;
use oPheme\Models\Interaction;
use oPheme\Models\InteractionMessage;

class InteractionMessageSeeder
	extends Seeder
{

	public function run ()
	{
		// to use non Eloquent-functions we need to unguard
		Eloquent::unguard();
		
		$this->command->info( 'fake interaction messages being seeded, Please wait' );
		
		// Get the links data (test discover)
		$discover = Discover::where( "name", "=", "Cambridge, Any Books?" )->first();
		
		$filecontents = File::get(base_path() . '/src/oPheme/Database/Seeds/Development/TestInteractionMessages.json');
		$messagesData = json_decode($filecontents, true);
		
		foreach($messagesData as $messageData) {
			
			// Create message instance
			$message = new Message($messageData);
			
			// If the message is in the discover table
			if( !is_null(
				$discoverMessage = DiscoverMessage::where("discover_id", "=", $discover->id)
				->whereHas( 'socialMediaPlatform', function($q) use ($message)
					{
						$q->where("name", "=", $message->opheme_backend['social_media_platform']);
					})
				->where("socialmediaplatform_message_id", "=", $message->in_reply_to_status_id)
				->first()
			) ) {
				// New interaction for a discover message
				
				// Save the message into mongo
				$message->save();
				
				// Save details into interaction table
				$interaction = new Interaction;
				$interaction->discoverMessage()->associate($discoverMessage);
				$interaction->save();
				
				// Save details into interactionmessage table
				$interactionMessage = new InteractionMessage;
				$interactionMessage->backend_message_id = $message->_id;
				$interactionMessage->socialmediaplatform_message_id = $message->id;
				$interactionMessage->socialmediaplatform_user_id = $message->user['id_str'];
				$interactionMessage->socialmediaplatform_user_screen_name = $message->user['screen_name'];
				$interactionMessage->message_datestamp = $message->opheme_backend['created_timestamp'];
				$interactionMessage->interaction()->associate($interaction);
				$interactionMessage->socialMediaPlatform()->associate($discoverMessage->socialMediaPlatform);
				$interactionMessage->save();
				
			} 
			// else if the message is in the interaction table 
			elseif( !is_null(
				$parentInteractionMessage = InteractionMessage::where("socialmediaplatform_message_id", "=", $message->in_reply_to_status_id)
					->whereHas("socialmediaplatform", function($q) use ($message)
					{
						$q->where("name", "=",  $message->opheme_backend['social_media_platform']);
					})
				->first()
			) ) {
				// Reply to existing interaction for a discover message
				
				// Save the message into mongo
				$message->save();
				
				// Save details into interactionmessage table
				$interactionMessage = new InteractionMessage;
				$interactionMessage->backend_message_id = $message->_id;
				$interactionMessage->socialmediaplatform_message_id = $message->id;
				$interactionMessage->socialmediaplatform_user_id = $message->user['id_str'];
				$interactionMessage->socialmediaplatform_user_screen_name = $message->user['screen_name'];
				$interactionMessage->message_datestamp = $message->opheme_backend['created_timestamp'];
				$interactionMessage->interaction()->associate($parentInteractionMessage->interaction);
				$interactionMessage->socialMediaPlatform()->associate($parentInteractionMessage->socialmediaplatform);
				$interactionMessage->save();
			}
		}

	}
}
