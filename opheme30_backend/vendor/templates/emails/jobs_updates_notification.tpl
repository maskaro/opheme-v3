<p style="color: #606060;font-family: Helvetica;font-size: 15px;line-height: 150%;text-align: left;">
	Hi, {$Data.email.firstName}!
	<br>
	<br>
	Here's what {$Data.email.brand}'s been up to recently on your behalf:
	<br>
</p>
{if isset($Data.email.jobs)}
	<table style="color: #000000; margin-left: auto; margin-right: auto; border: 0px; font-family: Helvetica; font-size: 15px; line-height: 150%; text-align: center;">
		<thead>
			<tr>
				<th style="padding: 5px;">Task Type</th>
				<th style="padding: 5px;">Task Name</th>
				<th style="padding: 5px;">Number of New Messages</th>
			</tr>
		</thead>
		<tbody>
			{foreach $Data.email.jobs as $jobsType => $jobs}
				{foreach $jobs as $job}
					<tr>
						<td style="padding: 5px;">{ucfirst($jobsType)}</td>
						<td style="padding: 5px;">{$job.name}</td>
						<td style="padding: 5px;">{$job.messages}</td>
					</tr>
				{/foreach}
			{/foreach}
		</tbody>
	</table>
{/if}
{if isset($Data.email.followInfo)}
	<table style="color: #000000; margin-left: auto; margin-right: auto; border: 0px; font-family: Helvetica; font-size: 15px; line-height: 150%; text-align: center;">
		<thead>
			<tr>
				<th style="padding: 5px;">On</th>
				<th style="padding: 5px;">These people Followed you since you Interacted with them</th>
			</tr>
		</thead>
		<tbody>
			{foreach $Data.email.followInfo as $follow}
				<tr>
					<td style="padding: 5px;">{ucfirst($follow.authKeyType)}</td>
					<td style="padding: 5px;">@{$follow.sm_user_screen_name}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{/if}
{if isset($Data.email.replyInfo)}
	<table style="color: #000000; margin-left: auto; margin-right: auto; border: 0px; font-family: Helvetica; font-size: 15px; line-height: 150%; text-align: center;">
		<thead>
			<tr>
				<th style="padding: 5px;">On</th>
				<th style="padding: 5px;">To your message</th>
				<th style="padding: 5px;">This Social Media user replied</th>
				<th style="padding: 5px;">At</th>
			</tr>
		</thead>
		<tbody>
			{foreach $Data.email.replyInfo as $reply}
				<tr>
					<td style="padding: 5px">{ucfirst($reply.authKeyType)}</td>
					<td style="padding: 5px">{$reply.original_message}</td>
					<td style="padding: 5px;">@{$reply.sm_user_screen_name} said: {$reply.message}</td>
					<td style="padding: 5px;">{$reply.added_at|date_format:"%H:%M:%S on %A, %B %e, %Y"}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{/if}
{if isset($Data.email.favouritedInfo)}
	<table style="color: #000000; margin-left: auto; margin-right: auto; border: 0px; font-family: Helvetica; font-size: 15px; line-height: 150%; text-align: center;">
		<thead>
			<tr>
				<th style="padding: 5px;">On</th>
				<th style="padding: 5px;">Your message was Favourited</th>
			</tr>
		</thead>
		<tbody>
			{foreach $Data.email.favouritedInfo as $favourite}
				<tr>
					<td style="padding: 5px">{ucfirst($favourite.authKeyType)}</td>
					<td style="padding: 5px">{$favourite.message}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{/if}
<p style="color: #606060;font-family: Helvetica;font-size: 15px;line-height: 150%;text-align: left;">
	<br>
	Find out much more by logging into your {$Data.email.brand} account at <a href="{$Data.email.loginUrl}" target="_blank">{$Data.email.loginUrl}</a>.
	<br>
	<br>
	<br>
	Until next time,
	<br>
	The {$Data.email.brand} Team
</p>
<p style="color: #606060;font-family: Helvetica;font-size: 15px;line-height: 150%;text-align: left;">
	<br>
	PS: You can control the frequency of this email notification in your {$Data.email.brand} Dashboard area.
</p>