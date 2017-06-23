<?php

namespace oPheme\Transformer;

use oPheme\Models\DiscoverMessage;

class DiscoverMessageTransformer
	extends BaseTransformer
{
	// Overriding variable in parent
	protected $type = 'discovermessage';

	/**
	 * Turn this item object into a generic array
	 *
	 * @return array
	 */
	public function transform ( DiscoverMessage $discoverMessage )
	{
		// might as well re-use this rather than call upon relationship data
		$platform = $discoverMessage->message->opheme_backend['social_media_platform'];
		
		switch($platform) {
			case "twitter":
				$transform = [
					'message_id'				 => (string) $discoverMessage->message->id,
					'backend_message_id'		 => (string) $discoverMessage->message->_id,
					'user'				 => [
						'id'				 => (string) $discoverMessage->message->user['id'],
						'screen_name'		 => $discoverMessage->message->user['screen_name'],
						'profile_image_url'	 => $discoverMessage->message->user['profile_image_url'],
					],
					'text'				 => $discoverMessage->message->text,
					'images'			 => $discoverMessage->message->opheme_backend['images'],
					'videos'			 => $discoverMessage->message->opheme_backend['videos'],
					'timestamp'			 => $discoverMessage->message_datestamp,
					'timestamp_server'			 => $discoverMessage->created_at,
					'coords'			 => $discoverMessage->message->opheme_backend['coords'],
					'sentiment'			 => $discoverMessage->message->opheme_backend['sentiment'],
					'klout_score'		 => $discoverMessage->message->opheme_backend['klout_score'],
					'social_media_type'	 => $platform,
					'source_link'	=> 'https://twitter.com/' . $discoverMessage->message->user['screen_name'] . '/status/' . ( (string) $discoverMessage->message->id )
				];
				break;
			case 'instagram':
				$transform = [
					'message_id'				 => (string) $discoverMessage->message->id,
					'backend_message_id'		 => (string) $discoverMessage->message->_id,
					'user'				 => [
						'id'				 => (string) $discoverMessage->message->user['id'],
						'screen_name'		 => $discoverMessage->message->user['username'],
						'profile_image_url'	 => $discoverMessage->message->user['profile_picture'],
					],
					'text'				 => isset($discoverMessage->message->caption['text']) ? $discoverMessage->message->caption['text'] : '',
					'images'			 => $discoverMessage->message->opheme_backend['images'],
					'videos'			 => $discoverMessage->message->opheme_backend['videos'],
					'timestamp'			 => $discoverMessage->message_datestamp,
					'timestamp_server'			 => $discoverMessage->created_at,
					'coords'			 => $discoverMessage->message->opheme_backend['coords'],
					'sentiment'			 => $discoverMessage->message->opheme_backend['sentiment'],
					'klout_score'		 => $discoverMessage->message->opheme_backend['klout_score'],
					'social_media_type'	 => $platform,
					'source_link' => $discoverMessage->message->link
				];
				break;
			default:
				break;
		}
	
		$transform = $this->removeDisallowedItems($transform);
		
		return $transform;
	}

}
