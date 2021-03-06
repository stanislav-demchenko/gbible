<?php
	$statement_schedule = $pdo -> prepare('select id from bible_for_a_year_schedules where user_id = :user_id and b_code = :b_code');
	$result = $statement_schedule -> execute(['user_id' => $_SESSION['uid'], 'b_code' => $tt_b_code]);
	if (!$result)
	{
		$messages[] = ['type' => 'danger', 'message' => $text['schedules_selection_exception']];
		log_msg(__FILE__ . ':' . __LINE__ . ' Schedule selection exception. Info = ' . json_encode($statement_schedule -> errorInfo()) . ', $_REQUEST = ' . json_encode($_REQUEST));
	}
	if ($statement_schedule -> rowCount() === 0)
	{
		$query = 'insert into bible_for_a_year_schedules (user_id, b_code, scheduled) values (:user_id, :b_code, now());';
	}
	else
	{
		$query = 'update bible_for_a_year_schedules set scheduled = now() where user_id = :user_id and b_code = :b_code';
	}
	$statement_schedule = $pdo -> prepare($query);
	$result = $statement_schedule -> execute(['user_id' => $_SESSION['uid'], 'b_code' => $tt_b_code]);

	if (!$result)
	{
		$messages[] = ['type' => 'danger', 'message' => $text['scheduling_exception']];
		log_msg(__FILE__ . ':' . __LINE__ . ' Schedule selection exception. Info = ' . json_encode($statement_schedule -> errorInfo()) . ', $_REQUEST = ' . json_encode($_REQUEST));
	}
	else echo '<p class="alert alert-success">' . $text['schedule_ok'] . '</p>';
?>