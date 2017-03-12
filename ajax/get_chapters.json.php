<?php
	require "../config.php";
	if (!isset($_REQUEST['b_code'])) exit;
	if(!isset($_REQUEST['book_index'])) $book_index = 1;
	else $book_index = $_REQUEST['book_index'] - 1;

	$statement_table_name = $links['sofia']['pdo'] -> prepare(
							'select table_name from b_shelf where b_code = :b_code');
	$result_table_name = $statement_table_name -> execute(array('b_code' => $_REQUEST['b_code']));

	if (!$result_table_name)
	{
		log_msg(__FILE__ . ':' . __LINE__ . ' Table name PDO exception. Info = {' . json_encode($statement_table_name -> errorInfo()) . '}, $_REQUEST = {' . json_encode($_REQUEST) . '}');

		//echo "Whoops. We've got issue with PDO connection... Sorry. Please contact support. ";
		//print_r($links['sofia']['pdo']);

	}
	else
	{	
		$table_name_row = $statement_table_name -> fetch();
		$table_name = $table_name_row['table_name'];

		// books sometimes have different shortnames and have only shortnames in those VPL
		$statement_books = $links['sofia']['pdo'] -> prepare(
					'select distinct book from ' . $table_name
														);
		$result_books = $statement_books ->	execute();

		if(!$result_books)
			log_msg(__FILE__ . ':' . __LINE__ . ' Books PDO exception. Info = {' . json_encode($statement_books -> errorInfo()) . '}, $_REQUEST = {' . json_encode($_REQUEST) . '}');

		$books_rows = $statement_books -> fetchAll();
		$book_name = $books_rows[$book_index]['book'];

		$statement_chapters = $links['sofia']['pdo'] -> prepare(
					'select distinct chapter from ' . $table_name
					. ' where book = "' . $book_name . '"'
														);
		$result_chaters = $statement_chapters -> execute();

		if(!$result_chaters)
			log_msg(__FILE__ . ':' . __LINE__ . ' Chapters PDO query exception. Info = {' . $statement_chapters -> errorInfo() . '}, $_REQUEST = {' . json_encode($_REQUEST) . '}');

		$chapters_rows = $statement_chapters -> fetchAll();

		echo json_encode( $chapters_rows );
	}
?>