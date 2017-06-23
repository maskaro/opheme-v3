<?php

namespace oPheme\Transformer;

use oPheme\Models\InteractionMessage;

class InteractionMessageTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'interactionmessage';

	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( InteractionMessage $interactionMessage )
	{
		$platform = $interactionMessage->message->opheme_backend['social_media_platform'];
		
		switch($platform) {
			case 'twitter':
				$transform = [
					'interaction_id'			 => (string) $interactionMessage->interaction->id,
					'root_message_id'			 => (string) $interactionMessage->interaction->discoverMessage->socialmediaplatform_message_id,
					'root_backend_message_id'	 => (string) $interactionMessage->interaction->discoverMessage->backend_message_id,
					'message_id'				 => (string) $interactionMessage->socialmediaplatform_message_id,
					'backend_message_id'		 => (string) $interactionMessage->backend_message_id,
					'user'				 => [
						'id'				 => (string) $interactionMessage->message->user['id_str'],
						'screen_name'		 => $interactionMessage->message->user['screen_name'],
						'profile_image_url'	 => $interactionMessage->message->user['profile_image_url'],
					],
					'text'				 => $interactionMessage->message->text,
					'timestamp'			 => $interactionMessage->message_datestamp,
					'timestamp_server'			 => $interactionMessage->created_at,
					'sentiment'			 => $interactionMessage->message->opheme_backend['sentiment'],
					'klout_score'		 => $interactionMessage->message->opheme_backend['klout_score'],
					'social_media_type'	 => $platform,
					'source_link'	=> 'https://twitter.com/' . $interactionMessage->message->user['screen_name'] . '/status/' . ( (string) $interactionMessage->socialmediaplatform_message_id )
				];
				break;
			
			case "instagram":
				$transform = [
					'interaction_id'			 => (string) $interactionMessage->interaction->id,
					'root_message_id'			 => (string) $interactionMessage->interaction->discoverMessage->socialmediaplatform_message_id,
					'root_backend_message_id'	 => (string) $interactionMessage->interaction->discoverMessage->backend_message_id,
					'message_id'				 => (string) $interactionMessage->socialmediaplatform_message_id,
					'backend_message_id'		 => (string) $interactionMessage->backend_message_id,
					'user'				 => [
						'id'				 => (string) $interactionMessage->message->from['id'],
						'screen_name'		 => $interactionMessage->message->from['username'],
						'profile_image_url'	 => $interactionMessage->message->from['profile_picture'],
					],
					'text'				 => $interactionMessage->message->text,
					'timestamp'			 => $interactionMessage->message_datestamp,
					'timestamp_server'			 => $interactionMessage->created_at,
					'sentiment'			 => $interactionMessage->message->opheme_backend['sentiment'],
					'klout_score'		 => $interactionMessage->message->opheme_backend['klout_score'],
					'social_media_type'	 => $platform,
					'source_link' => (string) $interactionMessage->interaction->discoverMessage->message->link
				];
				break;
			default:
				break;
		}
	
		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
