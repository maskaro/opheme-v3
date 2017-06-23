<script>window["moduleName"] = "{$Data.moduleName}";</script>

<div class="panel panel-default">
	<div id="new-account-toggler" data-toggle="collapse" data-target="#new-account-container" class="panel-heading">
		<div class="panel-title">
			Generate a new Account <i class="fa fa-angle-down fa-lg pull-right"></i>
		</div>
	</div>
	<div id="new-account-container" class="collapse">
		<div class="panel-body">
			<div class="row new-account-form-container">
				<div class="col-md-12">
					<form id="new-reg-token" action="/{$Data.moduleName}/process/token" method="post">
						<div class="form-group">
							<input type="hidden" name="action" value="create">
							<div class="input-group">
								<input class="form-control" type="text" name="email" placeholder="email@service.com" required="required">
								<span class="input-group-btn">
									<input class="btn btn-primary" type="submit" value="Create New Account">
								</span>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default hidden-xs">
	<div id="unused-accounts-toggler" data-toggle="collapse" data-target="#unused-accounts-container" class="panel-heading">
		<div class="panel-title">
			Unused Generated Accounts <i class="fa fa-angle-down fa-lg pull-right"></i>
		</div>
	</div>
	<div id="unused-accounts-container" class="collapse">
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<div style="max-height: 800px; overflow: auto;">
						<table class="table" id="tokensTable">
							<thead>
								<tr>
									<th>Email</th>
									<th>Token (Client Initial Password)</th>
									<th>Tasks</th>
								</tr>
							</thead>
							<tbody>
								{foreach $Data.moduleData.tokens as $regToken}
									<tr>
										<td>{$regToken.email}</td>
										<td>{$regToken.token}</td>
										<td>
											<form action="/{$Data.moduleName}/process/token" method="post">
												<input type="hidden" name="id" value="{$regToken.id}">
												<div class="btn-group">
													<button class="btn" type="submit" name="action" value="delete" style="display: inline">Delete</button>
												</div>
											</form>
										</td>
									</tr>
								{foreachelse}
									<tr>
										<td colspan="3">
											<span class="input-block-level">No Unused Generated Accounts on system.</span>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default hidden-xs">
	<div id="clients-overview-toggler" data-toggle="collapse" data-target="#clients-overview-container" class="panel-heading">
		<div class="panel-title">
			Clients Overview <i class="fa fa-angle-down fa-lg pull-right"></i>
		</div>
	</div>
	<div id="clients-overview-container" class="collapse">
		<div class="panel-body">
			<div class="row" style="max-height: 800px; overflow: auto;">
				<div class="col-md-12">
					<table class="table" id="clientsTable">
						<thead>
							<tr>
								<th>Active</th>
								<th>Last Login</th>
								<th>Email</th>
								<th>Name</th>
								<th>Phone</th>
								<th>Business</th>
								<th>Joined</th>
								<th>Subscription</th>
								<th>Discovers</th>
								<th>Campaigns</th>
								<th>Tasks</th>
							</tr>
						</thead>
						<tbody>
							{foreach $Data.moduleData.users as $user}
								<tr>
									<td><span style="color: {if $user.suspended eq 0}green">Yes{else}red">No{/if}</span></td>
									<td>{if $user.last_login eq "0000-00-00 00:00:00"}Never{else}{$user.last_login}{/if}</td>
									<td{if $Data.moduleName eq "admin"} onclick="$('#new-company input[name=users]').val($('#new-company input[name=users]').val() + ($('#new-company input[name=users]').val().length > 0?',':'') + $(this).html());" style="color: #00B8FF;"{/if}>{$user.email}</td>
									<td>{$user.firstname} {$user.lastname}</td>
									<td>{$user.phone}</td>
									<td><a href="{$user.business_www}" target="_blank">{$user.business_type}</a></td>
									<td>{$user.created}</td>
									<td>
										<form action="/{$Data.moduleName}/process/user" method="post">
											<input type="hidden" name="id" value="{$user.id}">
											<input type="hidden" name="action" value="changeSub">
											<select name="sub_id" onchange="this.form.submit();" style="width: 110px">
												{foreach $Data.moduleData.subs as $sub}
													<option value="{$sub.id}"{if $sub.id eq $user.subscription} selected{/if}>{$sub.name}</option>
												{/foreach}
											</select>
										</form>
									</td>
									{foreach $user.jobs as $jobs}
										<td onclick="hideAllBut('jobs_', 'jobs_{$jobs@key}_', '{str_replace(array('@', '.', '-', '+', '_'), '', $user.email)}');">
											<a href="" onclick="return false;">{count($jobs)}</a>
										</td>
									{/foreach}
									<td>
										<form id="user-action-form-{$user.id}" class="user-action-form" action="/{$Data.moduleName}/process/user" method="post">
											<input type="hidden" name="action" value="">
											<input type="hidden" name="id" value="{$user.id}">
											{if $user.id eq $Data.user.account.id}
												<strong>*YOU*</strong>
											{else}
												<div class="btn-group">
													<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
														Action <span class="caret"></span>
													</button>
													<ul class="dropdown-menu" role="menu">
														{if $user.id neq $Data.user.account.id}
															{if $user.suspended eq 1}
																<li><a href="" onclick="{literal}$('#user-action-form-{/literal}{$user.id}{literal} input[name=action]').val(this.innerHTML.toLowerCase()); $('#user-action-form-{/literal}{$user.id}{literal}').submit(); return false;{/literal}">Resume</a></li>
															{else}
																<li><a href="" onclick="{literal}$('#user-action-form-{/literal}{$user.id}{literal} input[name=action]').val(this.innerHTML.toLowerCase()); $('#user-action-form-{/literal}{$user.id}{literal}').submit(); return false;{/literal}">Suspend</a></li>
															{/if}
														{/if}
														{if $user.subscription eq 0}{* TRIAL subscription *}
															<li><a href="" onclick="{literal}$('#user-action-form-{/literal}{$user.id}{literal} input[name=action]').val('resetTrial'); $('#user-action-form-{/literal}{$user.id}{literal}').submit(); return false;{/literal}">Reset Trial</a></li>
														{/if}
														{if $user.code neq "0"}
															<li><a href="" onclick="{literal}$('#user-action-form-{/literal}{$user.id}{literal} input[name=action]').val(this.innerHTML.toLowerCase()); $('#user-action-form-{/literal}{$user.id}{literal}').submit(); return false;{/literal}">Activate</a></li>
														{/if}
														{if $user.id neq $Data.user.account.id}
															<li><a href="" onclick="{literal}$('#user-action-form-{/literal}{$user.id}{literal} input[name=action]').val('resetPassword'); $('#user-action-form-{/literal}{$user.id}{literal}').submit(); return false;{/literal}">Reset Password</a></li>
															<li><a href="" onclick="{literal}$('#user-action-form-{/literal}{$user.id}{literal} input[name=action]').val(this.innerHTML.toLowerCase()); $('#user-action-form-{/literal}{$user.id}{literal}').submit(); return false;{/literal}">Delete</a></li>
														{/if}
													</ul>
												</div>
											{/if}
										</form>
									</td>
								</tr>
							{foreachelse}
								<tr><td colspan="11">No Cients currently on the System.</td></tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<div id="jobsContainer">
				{foreach $Data.moduleData.users as $user}
					{foreach $user.jobs as $jobsType => $jobs}
						<div class="jobs_{$jobsType}_{str_replace(array('@', '.', '-', '+', '_'), '', $user.email)} modal modal-wide fade" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title">{$user.firstname} {$user.lastname} ({$user.email}) - {ucfirst($jobsType)}s</h4>
									</div>
									<div class="modal-body">
										<table class="table" style="margin-left: 10px; border: 2px solid;">
											<thead>
												<tr>
													<th>Active</th>
													<th>Created</th>
													<th>Name</th>
													<th>Keywords</th>
													<th>Address of Centre</th>
													<th>Radius</th>
													<th>Messages</th>
													<th>Allowance</th>
													<th>Tasks</th>
												</tr>
											</thead>
											<tbody>
												{foreach $jobs as $job}
													<tr>
														<td><span style="color: {if $job.suspended eq 0}green">Yes{else}red">No{/if}</span></td>
														<td>{$job.added}</td>
														<td>{$job.name}</td>
														<td>
															{if strlen($job.filter) > 0}<strong>Inclusion:</strong> {$job.filter}
															{else}No Inclusion Filter{/if}
															,&nbsp;
															{if strlen($job.filter_ex) > 0}<strong>Exclusion:</strong> {$job.filter_ex}
															{else}No Exclusion Filter{/if}
														</td>
														<td><span id="{$jobs@key}_address_{$job.id}"></span></td>
														<td>{$job.radius} miles</td>
														<td>{$job.message_count}</td>
														<td>{$job.messages_limit} messages{if intval($job.time_limit) > 0}, {$job.time_limit}{/if}</td>
														<td>
															<form id="job-action-form-{$jobs@key}-{$job.id}" action="/{$Data.moduleName}/process/{$jobs@key}" method="post">
																<input type="hidden" name="action" value="">
																<input type="hidden" name="id" value="{$job.id}">
																<div class="btn-group">
																	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
																		Action <span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu" role="menu">
																		{if $job.suspended eq 1}
																			<li><a href="" onclick="{literal}$('#job-action-form-{/literal}{$jobs@key}-{$job.id}{literal} input[name=action]').val(this.innerHTML.toLowerCase()); $('#job-action-form-{/literal}{$jobs@key}-{$job.id}{literal}').submit(); return false;{/literal}">Resume</a></li>
																		{else}
																			<li><a href="" onclick="{literal}$('#job-action-form-{/literal}{$jobs@key}-{$job.id}{literal} input[name=action]').val(this.innerHTML.toLowerCase()); $('#job-action-form-{/literal}{$jobs@key}-{$job.id}{literal}').submit(); return false;{/literal}">Suspend</a></li>
																		{/if}
																		<li><a href="" onclick="{literal}$('#job-action-form-{/literal}{$jobs@key}-{$job.id}{literal} input[name=action]').val(this.innerHTML.toLowerCase()); $('#job-action-form-{/literal}{$jobs@key}-{$job.id}{literal}').submit(); return false;{/literal}">Delete</a></li>
																	</ul>
																</div>
															</form>
															<script type="text/javascript">codeLatLng("{$job.centre_lat}", "{$job.centre_lng}", "#{$jobs@key}_address_{$job.id}");</script>
														</td>
													</tr>
												{foreachelse}
													<tr><td colspan="9">No {ucfirst($jobsType)}s.</td></tr>
												{/foreach}
											</tbody>
										</table>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
					{/foreach}
				{/foreach}
			</div>
		</div>
	</div>
</div>
			