<div id="postToSM" class="modal" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form>
				<div class="modal-header">
					<div class="form-horizontal">
						<div class="form-group form-group-sm">
							<label class="col-sm-3 control-label" for="formGroupInputSmall">Share this from: </label>
							<div class="col-sm-6">
								<input type="hidden" name="authKeyType" value="">
								<select name="authKeyId" class="form-control" onchange="this.form.authKeyType.value=($('#postToSM [name=authKeyId]')[0].options[$('#postToSM [name=authKeyId]')[0].selectedIndex].text.split('-')[0].toLowerCase().trim());">
									{foreach $Data.user.authorisation as $authModule => $authKeys}
										{if count($authKeys) gt 0}
											{foreach $authKeys as $auth}
												<option value="{$auth.id}">{ucfirst($authModule)} - @{$auth.screen_name}</option>
											{/foreach}
										{/if}
									{/foreach}
								</select>
							</div>
							<div class="col-sm-3">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<textarea type="text" class="form-control" rows="3" name="msg" placeholder="Message to Share"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="pull-right">
						Characters left: <span class="count-message-length">Unlimited</span>
						<input type="submit" value="Share" class="btn btn-sm btn-primary">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>