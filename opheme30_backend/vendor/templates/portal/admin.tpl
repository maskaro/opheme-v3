<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">

							<ul class="nav nav-tabs">
								<li class="active"><a href="#admin-container" data-toggle="tab">Overview</a></li>
								<li><a href="#sendEmail-container" data-toggle="tab">Email Users</a></li>
							</ul>

							<div class="tab-content">
								<div id="admin-container" class="tab-pane active">
									<br>
									<div class="row">
										<div id="system-overview" class="col-md-12">
											<div class="panel panel-default">
												<div class="panel-heading">
													<div class="panel-title">
														<h3><i class="fa fa-gears fa-lg"></i> System Overview</h3>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="system-counter" class="row">
										{foreach $Data.moduleData.system.job_stats as $jobType => $jobStats}
											<div class="col-md-{(12 / count($Data.moduleData.system.job_stats))}">
												<div class="panel panel-default">
													<div class="panel-heading">
														<div class="panel-title">
															<h4><i class="fa fa-search"></i> {ucfirst($jobType)}s</h4>
														</div>
													</div>
													<div class="panel-body">
														<div class="row">
															<div class="counter-container col-md-6 col-xs-6">
																<h1 class="counter">{$jobStats.job_count}</h1>
																<h4>{ucfirst($jobType)}s</h4>
															</div>
															<div class="counter-container col-md-6 col-xs-6">
																<h1 class="counter">{$jobStats.message_count}</h1>
																<h4>{ucfirst($jobType)} Messages</h4>
															</div>
														</div>
													</div>
												</div>
											</div>
										{/foreach}
									</div>
									<div id="system-performance" class="row">
										<div class="col-md-6">
											<div class="panel panel-default">
												<div class="panel-heading">
													<div class="panel-title">
														<h4>Last CPU check: <span class="pull-right">{$Data.moduleData.system.loads.lastCheck}</span></h4>
													</div>
												</div>
												<div class="panel-body">
													<div class="row">
														<div class="graph col-md-4 col-xs-4">
															<h3>{$Data.moduleData.system.loads.one}%</h3>
															<h4>last minute</h4>
														</div>
														<div class="graph col-md-4 col-xs-4">
															<h3>{$Data.moduleData.system.loads.five}%</h3>
															<h4>last 5 minutes</h4>
														</div>
														<div class="graph col-md-4 col-xs-4">
															<h3>{$Data.moduleData.system.loads.onefive}%</h3>
															<h4>last 15 minutes</h4>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div id="ram-check" class="panel panel-default">
												<div class="panel-heading">
													<div class="panel-title">
														<h4>Last RAM check <span class="pull-right">{$Data.moduleData.system.memory.lastCheck}</span></h4>
													</div>
												</div>
												<div class="panel-body">
													<div class="row">
														<div class="graph col-md-4 col-xs-4">
															<h3 data-toggle="tooltip" data-placement="top" title="{$Data.moduleData.system.memory.total.mb}MB">{$Data.moduleData.system.memory.total.gb}GB</h3>
															<h4>RAM Total</h4>
														</div>
														<div class="graph col-md-4 col-xs-4">
															<h3 data-toggle="tooltip" data-placement="top" title="{$Data.moduleData.system.memory.used.mb}MB">{$Data.moduleData.system.memory.used.gb}GB</h3>
															<h4>RAM Used</h4>
														</div>
														<div class="graph col-md-4 col-xs-4">
															<h3 data-toggle="tooltip" data-placement="top" title="{$Data.moduleData.system.memory.free.mb}MB">{$Data.moduleData.system.memory.free.gb}GB</h3>
															<h4>RAM Free</h4>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div id="companies-overview" class="row">
										<div class="col-md-12">
											<div class="panel panel-default hidden-xs">
												<div id="companies-overview-toggler" data-toggle="collapse" data-target="#companies-overview-container" class="panel-heading">
													<div class="panel-title">
														Companies Overview <i class="fa fa-angle-down fa-lg pull-right"></i>
													</div>
												</div>
												<div id="companies-overview-container" class="collapse">
														<div class="panel-body">
															<div class="row">
																<div class="col-md-12">
																	<div class="panel panel-default">
																		<div id="new-company-account-toggler" data-toggle="collapse" data-target="#new-company-account-container" class="panel-heading">
																			<div class="panel-title">
																				Create a New Company Profile <i class="fa fa-angle-down fa-lg pull-right"></i>
																			</div>
																		</div>
																		<div id="new-company-account-container" class="collapse">
																			<div class="panel-body">
																				<div class="row new-account-form-container">
																					<div class="col-md-12">
																						<form id="new-company" action="/{$Data.moduleName}/company" method="post">
																							<input type="hidden" name="action" value="create">
																							<div class="row">
																								<div class="col-md-6">
																									<div class="form-group">
																										<label for="compId" class="control-label">Company identifier</label>
																										<input class="form-control" type="text" name="compId" placeholder="company" required="required">
																									</div>
																								</div>
																								<div class="col-md-6">
																									<div class="form-group">
																										<label for="users" class="control-label">Company Representatives (emails of existing accounts, comma separated)</label>
																										<input type="text" class="form-control" name="users" placeholder="user@email.com,user2@email.com" required="required">
																										<p class="help-block">
																											Filter Clients and click on their Email to add it here
																										</p>
																									</div>
																								</div>
																							</div>
																							<div class="form-group">
																								<div class="notouch alert alert-info">Available Modules: {$Data.moduleData.availableModules}</div>
																								<label for="modules" class="control-label">Allowed Modules (separated by commas)</label>
																								<input type="text" class="form-control" name="modules" placeholder="module1,module2,module3" required="required">
																							</div>
																							<input class="btn btn-large btn-block btn-primary" type="submit" value="Create New Profile" />
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<table class="table" id="companiesTable">
																			<thead>
																				<tr>
																					<th>Company ID</th>
																					<th>Representatives</th>
																					<th>Enabled Modules</th>
																					<th>Available Tasks</th>
																				</tr>
																			</thead>
																			<tbody>
																				{foreach $Data.moduleData.companies as $company}
																					<tr>
																						<td>{$company.company_id}</td>
																						<td>
																							<form action="/{$Data.moduleName}/company" method="post">
																								<div class="form-group">
																									<input type="hidden" name="action" value="edit-representative">
																									<input type="hidden" name="id" value="{$company.id}">
																									<input type="text" class="form-control" name="users" value="{$company.representatives}">
																								</div>
																							</form>
																						</td>
																						<td>
																							<form action="/{$Data.moduleName}/company" method="post">
																								<div class="form-group">
																									<input type="hidden" name="action" value="edit-modules">
																									<input type="hidden" name="id" value="{$company.id}">
																									<input type="text" class="form-control" name="modules" value="{$company.modules}">
																								</div>
																							</form>
																						</td>
																						<td>
																							<form action="/{$Data.moduleName}/company" method="post">
																								<input type="hidden" name="id" value="{$company.id}">
																								<button class="btn btn-danger" type="submit" name="action" value="delete">Delete</button>
																							</form>
																						</td>
																					</tr>
																				{foreachelse}
																					<tr>
																						<td colspan="2">
																							<span class="input-block-level">No Companies on the System.</span>
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
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="panel panel-default">
												<div class="panel-heading">
													Reseller Admin
												</div>
												<div class="panel-body">
													{include "reseller-admin.tpl"}
												</div>
											</div>
										</div>
									</div>
								</div>

								<div id="sendEmail-container" class="tab-pane">
									Placeholder
								</div>

							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>