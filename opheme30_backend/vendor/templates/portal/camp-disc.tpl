<div id="job-container" class="container">
	<div class="row">
		<div class="col-md-12">
		  	<div class="panel panel-default">
				<div class="panel-heading">
			  		<div class="panel-title">
						<h3>{$Data.title}s <img id="loaderAnimatedIcon" src="/images/loader.gif"></h3>
			  		</div>
				</div>
				<div class="panel-body">
			  		<div class="listMapFix row">
			  			<div class="col-md-12">
				  			<div id="loader-fix">
								<div id="loader-element" class="map-loader"><div class="loader-content"><i class="fa fa-cog fa-spin fa-lg"></i>loading</div></div>
								<ul class="nav nav-tabs">
						  			<li class="active"><a href="#currentList" data-toggle="tab">Live {$Data.title}s</a></li>
									{if !isset($trialEnded) && ( ($Data.moduleName eq "discover" && !isset($noDiscs)) || ($Data.moduleName eq "campaign" && !isset($noCamps)) )}
										<li><a href="#newJobContainer" data-toggle="tab">Create New {$Data.title}</a></li>
									{/if}
								</ul>
								<div class="tab-content">
						  			<div id="currentList" class="tab-pane active">
						  				<div class="row">
							  				<div id="list-container" class="col-md-4">
												<table id="job-table" class="table table-hover">
									  				<thead>
														<tr>
														  	<th>Active</th>
														  	<th>{$Data.title} Name</th>
														  	<th>Messages</th>
														</tr>
									  				</thead>
									  				<tbody>
														{foreach $Data.moduleData.jobs as $job}
															<tr class="job" id="{$job.id}" json='{json_encode($job, $smarty.const.JSON_HEX_APOS)}'>
																<td id="job_{$job.id}_status">
																	{if $job.suspended eq 0}
																		<span style="color: green"><i class="fa fa-check fa-lg"></i></span>
																	{else}
																		<span style="color: red"><i class="fa fa-times fa-lg"></i></span>
																	{/if}
																</td>
																<td class="job-name">{$job.name}</td>
																<td class="job-responses"><span id="job_{$job.id}_count">0</span>
																	<span class="pull-right">
																		{if isset($Data.company.modules.csv)}
																			<i title="Download CSV" class="fa fa-cloud-download text-info more-info-tooltip"></i>
																		{/if}
																		<i title="Suspend" class="fa fa-pause fa-lg text-warning more-info-tooltip"{if $job.suspended eq 1} style="display: none"{/if}></i>
																		<i title="Resume" class="fa fa-play fa-lg text-success more-info-tooltip"{if $job.suspended eq 0} style="display: none"{/if}></i>
																		<i title="Edit" class="fa fa-pencil fa-lg text-success more-info-tooltip"></i> 
																		<i title="Remove" class="fa fa-times fa-lg text-danger more-info-tooltip"></i>
																	</span>
																</td>
															</tr>
														{foreachelse}
															<tr class="job">
																<td class="job-name" colspan="3" align="center">Create a new {$Data.title}!</td>
															</tr>
														{/foreach}
									  				</tbody>
												</table>
											</div>
											<div id="map-container" class="col-md-8">
												{include "camp-disc-mapLegend.tpl"}
									  			<div id="job-preview">
													{if empty($Data.moduleData.jobs)}
														<div id="loader-element">
															<div class="loader-content">
																<i class="fa fa-globe fa-spin fa-lg"></i>
															</div>
														</div>
														<script>
															{literal}$("#loader-element.map-loader").fadeOut("slow");{/literal}
														</script>
													{/if}
												  	<div class="dummy"></div>
												  	{foreach $Data.moduleData.jobs as $job}
														<div class="job-map" id="map_{$job.id}"></div>
														<script>
															{literal}oph_{/literal}{$job.id}{literal} = new $.oPhemeUI($("#map_{/literal}{$job.id}{literal}"), { timeout: {/literal}{(mt_rand(15000, 20000) + intval($job@key) * 500)}{literal}, display_freq: {/literal}{(mt_rand(15000, 20000) + intval($job@key) * 500)}{literal}, module: "{/literal}{$Data.moduleName}{literal}", id: {/literal}{$job.id}{literal}, oUserId: {/literal}{$job.user_id}{literal}, sent: {/literal}{if $Data.moduleName eq "campaign"}true{else}false{/if}{literal}, suspended: {/literal}{$job.suspended}{literal}, shared: {/literal}{$job.shared}{literal} });
															coords_{/literal}{$job.id}{literal} = { lat: parseFloat("{/literal}{$job.centre_lat}{literal}").toFixed(7), lng: parseFloat("{/literal}{$job.centre_lng}{literal}").toFixed(7) };
															map_{/literal}{$job.id}{literal} = oph_{/literal}{$job.id}{literal}.map({ api: "gmaps", settings: { options: { map_centre: coords_{/literal}{$job.id}{literal}, zoom: 9 } } });
															{/literal}oph_{$job.id}.start();
															{literal}$.extend(maps_json, { map_{/literal}{$job.id}{literal}: { handle: map_{/literal}{$job.id}{literal}, id: {/literal}{$job.id}{literal} } });{/literal}
														</script>
													{/foreach}
													{* Custom Map Controls *}
													{if !isset($trialExpired)}{include "camp-disc-mapCC.tpl"}{/if}
									  			</div>
											</div>
										</div>
						  			</div>
									{if !isset($trialEnded) && ( ($Data.moduleName eq "discover" && !isset($noDiscs)) || ($Data.moduleName eq "campaign" && !isset($noCamps)) )}
										<div id="newJobContainer" class="tab-pane">
											<div class="row">
												{if !isset($trialExpired)}
													<div class="col-md-4">
														{include "camp-disc-wizard.tpl"}
													</div>
													<div id="map-container" class="col-md-8">
														<div id="job-preview">
															<div width="100%" height="100%" id="map_preview"></div>
														</div>
													</div>
												{else}
													<div class="col-md-12">
														We're sorry, but it looks like your Subscription has expired. Please consider upgrading it to continue enjoying {$Data.company.brand}.
													</div>
												{/if}
											</div>
										</div>
									{/if}
								</div>
							</div>
						</div>
			  		</div>
				</div>
		 	</div>
		</div>
	</div>
</div>
{* Share to Social Media Form *}{* Reply to Social Media Message Form *}
{if !isset($trialExpired)}{include "camp-disc-postToSM.tpl"}{include "camp-disc-replyToMessage.tpl"}{/if}
{include "camp-disc-downloadCSVModal.tpl"}