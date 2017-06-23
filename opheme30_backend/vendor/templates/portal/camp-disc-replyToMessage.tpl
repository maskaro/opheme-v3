<div id="replyToMsg" class="modal" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Reply to @<span></span></h4>
			</div>
			<form>
				<div class="modal-body">
					<input type="hidden" name="authKeyType" value="">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Choose an Account</label>
								<select name="authKeyId" class="form-control" onchange="this.form.authKeyType.value=($('#replyToMsg [name=authKeyId]')[0].options[$('#replyToMsg [name=authKeyId]')[0].selectedIndex].text.split('-')[0].toLowerCase().trim());">
									{foreach $Data.user.authorisation as $authModule => $authKeys}
										{if count($authKeys) gt 0}
											{foreach $authKeys as $auth}
												<option value="{$auth.id}">{ucfirst($authModule)} - @{$auth.screen_name}</option>
											{/foreach}
										{/if}
									{/foreach}
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="msg" class="control-label">Message to Send (Left: <span class="count-message-length">Unlimited</span>)</label>
								<textarea placeholder="message" type="text" class="form-control" name="msg"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="submit" value="Send Reply" class="btn btn-block btn-primary">
				</div>
			</form>
		</div>
	</div>
</div>