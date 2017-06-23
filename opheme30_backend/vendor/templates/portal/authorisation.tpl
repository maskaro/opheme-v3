<div id="authorisation-status" class="col-sm-6 col-md-6">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<h3>Social Media Authorisation</h3>
			</div>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<div id="sma-container">
						{foreach $Data.user.authorisation as $authModule => $authKeys}
							<h4>{ucfirst($authModule)} Accounts</h4>
							<div class="row">
								<div class="col-md-12">
									<form id="new-sma-account-{$authModule}" class="new-sma-account" action="/callback/{$authModule}/authorise-request" method="post">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													{*<label for="token-name">New Account Name</label>*}
													<div class="input-group">
														{*<input class="form-control" type="text" name="token-name" value="" required="required">*}
														<span class="input-group-btn">
															<button {if $authModule eq "instagram"}style="background-color: rgb(50, 85, 130)" {/if}type="button" class="btn btn-primary" onclick="{literal}${/literal}('form#new-sma-account-{$authModule}').submit();" >
																<i class="fa fa-{$authModule} fa-lg"></i> Authorise with <strong>{ucfirst($authModule)}</strong>
															</button>
														</span>
													</div>
												</div>
											</div>
										</div>
									</form>
									<div class="row">
										<div class="col-md-12">
											{foreach $authKeys as $auth}
												<div class="panel panel-default">
													<div class="panel-heading">
														<div class="panel-title">
															Account Name: @<strong>{$auth.screen_name}</strong>
														</div>
													</div>
													<div class="panel-body">
														<div class="row">
															<div class="col-md-8">
																<label>Date Authorised:</label> {$auth.added}
															</div>
															<div class="col-md-4">
																<form action="/authorisation/{$authModule}/remove" method="post" onsubmit="return confirm('Are you sure you want to remove this {ucfirst($authModule)} account?');">
																	<input type="hidden" name="token-id" value="{$auth.id}">
																	<input class="btn btn-xs btn-danger pull-right" type="submit" value="Remove">
																</form>
															</div>
														</div>
													</div>
												</div>
											{foreachelse}
												<div class="alert alert-warning">You have not authorised any {ucfirst($authModule)} accounts with {$Data.company.brand} yet.</div>
											{/foreach}
										</div>
									</div>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>