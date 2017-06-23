<html>
	<head>
		{{ HTML::style('css/internal/global/bootstrap.min.css') }}
	</head>
	<body style='background-color:#FFFFFF'>
		<?php //var_dump($tables); ?>
		<div class="container">
			<div class="row">
				<?php $rowNumber = 0; ?>
				@foreach ($tables as $table)
				
					@if ($rowNumber % 2 == 0) <? // If it is even ?>
						<?php $backgroundColour = "#FFFFFF"; ?>
					@else
						<?php $backgroundColour = "#F0F0F0"; ?>
					@endif
					
					<?php $rowNumber++; ?>
					<div class="col-md-12" style='background-color: {{$backgroundColour}}'>
						<div style="font-size: 24px; font-weight: bold">{{$table['name']}}</div>
						<table class="table table-bordered table-hover">
							<caption>Columns</caption>
							<thead>
								<tr>
									<th>Field</th>
									<th>Type</th>
									<th>Null</th>
									<th>Key</th>
									<th>Default</th>
									<th>Extra</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($table['columns'] as $row)
									<tr>
										<td>{{$row->Field}}</td>
										<td>{{$row->Type}}</td>
										<td>{{$row->Null}}</td>
										<td>{{$row->Key}}</td>
										<td>{{$row->Default}}</td>
										<td>{{$row->Extra}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<table class="table table-bordered table-hover">
							<caption>Foriegn Keys</caption>
							<thead>
								<tr>
									<th>COLUMN_NAME</th>
									<th>TABLE_NAME</th>
									<th>REFERENCED_COLUMN_NAME</th>
									<th>REFERENCED_TABLE_NAME</th>
									<th>CONSTRAINT_NAME</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($table['foreignKeys'] as $row)
									<tr>
										<td>{{$row->COLUMN_NAME}}</td>
										<td>{{$row->TABLE_NAME}}</td>
										<td>{{$row->REFERENCED_COLUMN_NAME}}</td>
										<td>{{$row->REFERENCED_TABLE_NAME}}</td>
										<td>{{$row->CONSTRAINT_NAME}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						
					</div>
				@endforeach
			</div>
		</div>
		
	</body>
</html>