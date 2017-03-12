<h1>Sign In</h1>
<br />
<?php
	if ($_POST['password'] === $_POST['password_repeat'])
	{
		$row = array(
						'nickname' => $_POST['nickname'], 
						'full_name' => $_POST['full_name'],
						'email' => $_POST['email'],
						'password' => md5($_POST['password']),
						'secret_question' => $_POST['secret_question'],
						'secret_answer' => $_POST['secret_answer'],
						'timezone' => $_POST['timezone'],
						'country' => $_POST['user_country'],
						'language' => $_POST['user_language']
						
					);
		$command = $links['sofia']['pdo'] -> prepare('insert into users (nickname, full_name, email, password, secret_question, secret_answer, inserted, timezone, country, language) 
					values (:nickname,:full_name,:email, :password,:secret_question,:secret_answer, now(), :timezone, :country, :language);');
		$links['sofia']['pdo'] -> beginTransaction();
		$result = $command -> execute($row);
		$links['sofia']['pdo'] -> commit();

		if ($result)
		{
			$msg_type = 'success';
			$message = 'You are registered user now, please <a href="./?menu=users_signIn" class="alert-link">Sign In</a>. ';
		}
		else 
		{
			$msg_type = 'danger';
			$message = "Whoops. We've got issue. Sorry. Please, contact support.";
			log_msg(__FILE__ . ':' . __LINE__ . ' Inserting new user exception. $_POST = {' . json_encode($_POST) . '}');
		}
	}
	else 
	{
		$msg_type = 'danger';
		$message = "You typed different passwords.";
	}

	if (isset($message) and isset($msg_type))
	{
		echo "<div class='alert alert-$msg_type'>$message</div>";
	}
?>