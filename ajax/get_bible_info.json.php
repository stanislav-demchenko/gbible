<?php
	require "../config.php";
	if (!isset($_REQUEST['b_code'])) exit;
	else $b_code = $_REQUEST['b_code'];
	setcookie('b_code', $b_code, time() + 30 * 24 * 3600);
	$statement = $links['sofia']['pdo'] -> prepare(
							'select b_shelf.b_code, b_shelf.title, b_shelf.description, 
								case when b_shelf.copyright is null 
                                then b_shelf.translated_by
								else b_shelf.copyright end `copyright`,
								b_shelf.license, 
                                licenses.link `license_link` 
                            from b_shelf 
							join licenses on b_shelf.license = licenses.license
                            where b_code = :b_code');
						$result_info = $statement -> execute(array('b_code' => $b_code));

	if (!$result_info)
	{
		echo "Whoops. We've got issue with PDO connection... Sorry. Please contact support. "
		;
		print_r($links['sofia']['pdo']);

	}
	else
	{
		echo json_encode($statement -> fetchAll());
	}
?>