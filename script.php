<html>
	<?php if (count($_POST)) { ?>
		<head>
			<style>
				th, tr, td {
					border-style: solid;
				}
			</style>
		</head>
	<?php } ?>
	<body>
		<?php if (!count($_POST)) { ?>
			<form method="post">
				<input type="text" name="username">
				<input type="password" name="password">
				<input type="submit" value="Alakazam!">
			</form>
		<?php } else {
			if ($_POST['username'] === 'o_htst' && $_POST['password'] === 'pwd-0-h15t_135!') {
				try {
					$conn = new mysqli('localhost', 'opheme30_backend', 'backend1357!', 'opheme30');
					if($result = $conn->query('select count(*) as count from discover_user where created_at > "2015-11-04"')) {
						echo '<p>Hootsuite - Discover Count created after 2015-11-04: <strong>' . http_build_query($result->fetch_assoc(), '', ', ') . '</strong></p>';
					}
					if($result = $conn->query('select * from discover_user where created_at > "2015-11-04" order by created_at desc')) {
						echo '<table><tr><th>Discover ID</th><th>User ID</th><th>Created At</th><th>Discover Info</th></tr>';
						while ($disc = $result->fetch_assoc()) {
							echo '<tr>';
							echo '<td>' . $disc['discover_id'] . '</td><td>' . $disc['user_id'];
							if($result2 = $conn->query('select email from user where id="' . $disc['user_id'] . '"')) {
								echo '<br>' . urldecode(http_build_query($result2->fetch_assoc(), '', '<br>'));
							}
							echo '</td><td>' . $disc['updated_at'] . '</td>';
							if($result2 = $conn->query('select name,latitude,longitude,radius,running from discover where id="' . $disc['discover_id'] . '"')) {
								echo '<td>' . urldecode(http_build_query($result2->fetch_assoc(), '', '<br>')) . '</td>';
							}
							echo '</tr>' . PHP_EOL;
						}
						echo '</table>';
					}
					if($result = $conn->query('select count(*) as count from user where created_at > "2015-11-04" and email not like "%asd%" and email not like "%opheme%" and email not like "%maskaro%" and email not like "%mask1%"')) {
						echo '<p>Hootsuite - User Count created after 2015-11-04: <strong>' . http_build_query($result->fetch_assoc(), '', ', ') . '</strong></p>';
					}
					if($result = $conn->query('select id,email,suspended,created_at from user where created_at > "2015-11-04" and email not like "%asd%" and email not like "%opheme%" and email not like "%maskaro%" and email not like "%mask1%" order by created_at desc')) {
						echo '<table><tr><th>User ID</th><th>User Info</th><th>User Extra Info</th><th>Created At</th></tr>';
						while ($user = $result->fetch_assoc()) {
							echo '<tr>'; 
							echo '<td>' . $user['id'] . '</td>';
							echo '<td>' . urldecode(http_build_query($user, '', '<br>')) . '</td>';
							if($result2 = $conn->query('select first_name,last_name,last_login from userextra where user_id="' . $user['id'] . '"')) {
								echo '<td>' . urldecode(http_build_query($result2->fetch_assoc(), '', '<br>')) . '</td>';
							}
							echo '<td>' . $user['created_at'] . '</td>';
							echo '</tr>' . PHP_EOL;
						}
						echo '</table>';
					}
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			} else {
				header('Location: http://www.google.com'); exit;
			}
		} ?>
	</body>
</html>
