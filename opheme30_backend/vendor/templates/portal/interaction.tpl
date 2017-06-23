<div id="smInteraction-container" class="container">
	<div id="smInteraction" class="col-md-12 col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title">
					<div class="row">
						<div class="col-md-8">
							<h3>Social Media Interaction history</h3>
						</div>
						<div class="col-md-4">
							<div class="input-group" style="margin-top: 15px;">
  								<span class="input-group-addon"><i class="fa fa-lg fa-search"></i></span>
  								<input type="text" id="interactionsSearchBox" class="form-control" placeholder="Search through these Interactions">
							</div>
      					</div>
      				</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
      				<div class="col-md-12">
				  		<h4 class="text-center">Checking for new interactions in:</h4>
				      		<div class="radial-progress" data-progress="30">
								<div class="circle">
									<div class="mask full">
										<div class="fill"></div>
									</div>
									<div class="mask half">
										<div class="fill"></div>
										<div class="fill fix"></div>
									</div>
									<div class="shadow"></div>
								</div>
								<div class="inset">
									<div class="percentage">
										<div class="numbers"><span>-</span><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span><span>10</span><span>11</span><span>12</span><span>13</span><span>14</span><span>15</span><span>16</span><span>17</span><span>18</span><span>19</span><span>20</span><span>21</span><span>22</span><span>23</span><span>24</span><span>25</span><span>26</span><span>27</span><span>28</span><span>29</span><span>30</span></div>
									</div>
								</div>
							</div>
				      	</div>
				  	</div>
				<div class="row">
					<div class="col-md-12">
						{if count($Data.moduleData.smInteraction.history)}
							<div id="interactions"></div>
							<div class="col-md-12 text-center"> 
								<button type="button" class="btn btn-primary" id="loadMoreRows">Load older Interaction data</button>
							</div>
							<script>
								var intResInitial = $.parseJSON('{json_encode($Data.moduleData.smInteraction.history, $smarty.const.JSON_HEX_APOS)}');
							</script>
						{else}
							No interaction data is exists at the moment.
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>