<?php
	require 'bible_nav.php';

	// just interesting random =] 1 to 1000 that you will get 'Hallelujah!' text =]
	$b_code = $userBible -> b_code;
	$random = rand(1, 1000);
	if ($random === 1000)
		echo '<p class="alert alert-success">' . $text['hallelujah'] . '</p>';

	$statement_translation = $links['sofia']['pdo'] -> prepare(
			'select bh.b_code, bh.table_name, bh.title, bh.description, bh.copyright, 
			bh.license, l.link, bh.http_link, country, language, dialect
			from b_shelf bh 
			join licenses l on l.license = bh.license
			where b_code = :b_code'
		);


	$result_translation = $statement_translation -> execute(array('b_code' => $b_code));
	if(!$result_translation)
		log_msg(__FILE__ . ':' . __LINE__ . ' PDO translations query exception. Info = {' . json_encode($statement_translation -> errorInfo()) . '}, $_REQUEST = {' . json_encode($_REQUEST) . '}' );
	$info_row = $statement_translation -> fetch();
	$bible_title = $info_row['title'];
	$bible_description = $info_row['description'];

	if (stripos($b_code, 'http') === FALSE)
	{
?>
<form method="post">
	<input type="hidden" name="menu" value="search" />
	<input type="hidden" name="search_in" value="<?=$b_code;?>" />
	<div class="input-group input-group-sm">
		<input type="text" class="form-control" name="search_query" placeholder="<?=$text['search_in'].$info_row['title'];?>" />
		<span class="input-group-btn"><input type="submit" class="btn btn-default" name="submit" value="<?=$text['text_search'];?>"></span>
	</div>
</form>
<br />
<?php
	}
?>
	<div class="panel panel-primary">
		<div>
		<?php

		if (stripos($b_code, '_http') === FALSE)
		{
			$table_name = $info_row['table_name'];

			$statement_books = $links['sofia']['pdo'] -> prepare('
								select distinct book from ' . $table_name
							);
			$books_result = $statement_books -> execute();
			$books_rows = $statement_books -> fetchAll();
			$books_nav = '<div width="80%" align="center">';
			$books_form = '<form method="post" name="bookSelectionFormFromChosenBible">
							<input type="hidden" name="b_code" value="' . $b_code . '">
							<select name="book" class="form-control" onchange="bookSelectionFormFromChosenBible.submit()">
							'
							;


			$found_book = FALSE;
			foreach ($books_rows as $row) 
			{
				$books_nav .=  '<a href="./?b_code=' . $b_code . '&book=' . $row['book'] . '">' . $row['book'] . '</a> ';
				$selected = '';
				if (!$found_book and (strcasecmp($book, $row['book']) === 0) )
				{
					$selected = ' selected="selected"';
					$found_book = true;
				}
				$books_form .= '<option value="' . $row['book'] . '"' . $selected . '>' . $row['book'] . '</option>';
			}
				if (!$found_book)
				$book = $books_rows[0]['book'];

				$books_form .= '</select></form>';
				$books_nav .= '</div>';

		?>
			<div><center><h2 id="bibleTitle" title="<?=$bible_description;?>"><?=$bible_title;?></h2></center></div>
			<nav class="gb-books-nav"><?=$books_nav.'<br/>'.$books_form;?></nav>
		<?php
			if (!isset($book))
				$book = $books_rows[0]['book'];

			if (!isset($chapter))
				$chapter = 1;

			$statement_chapters = $links['sofia']['pdo'] -> prepare (
									'select distinct chapter from ' . $table_name 
									.' where book = :book'
								);
				$result_chapters = $statement_chapters -> execute(array('book' => $book));

				if(!$result_chapters)
					log_msg(__FILE__ . ':' . __LINE__ . ' PDO chapters query exception. Info = {' . json_encode($statement_chapters -> errorInfo()) . '}, $_REQUEST = {' . json_encode($_REQUEST) . '}, \$table_name = `' . $table_name . '`.' );

				$chapters_rows = $statement_chapters -> fetchAll();
				$chapters_links = '<div width="80%" align="center">';
				$chapter_count=0;

				$chapters_form = '<form method="post" name="chapterSelectionFormFromChosenBible">
							<input type="hidden" name="b_code" value="' . $b_code . '">
							<input type="hidden" name="book" value="' . $book . '">
							<select name="chapter" class="form-control" onchange="chapterSelectionFormFromChosenBible.submit()">
							'
							;

				foreach ($chapters_rows as $chapter_row) 
				{
					$chapter_count++;
					$chapters_links .= '<a href="./?b_code=' . $b_code . '&book=' . $book . '&chapter=' . $chapter_row['chapter'] . '">[' . $chapter_row['chapter'] . ']</a> ';
					$selected = '';
					if ($chapter == $chapter_row['chapter'])
						$selected = ' selected="selected"';
					$chapters_form .= '<option value="' . $chapter_row['chapter'] . '"' . $selected . '>' . $chapter_row['chapter'] . '</option>';
				}
				$chapters_links .= '</div>';
				$chapters_form .= '</select></form>';

				$chapter_nav = '<table width="100%"><tr><td width="50%">';
				if ($chapter > 1)
					$chapter_nav .= '<a href="./?b_code=' . $b_code . '&book=' . $book . '&chapter=' . ($chapter - 1) . '#top-anchor"><button class="btn btn-default">' . $text['previous_chapter'] . '</button></a>';
				$chapter_nav .= '</td><td width="50%" align="right">';
				if ($chapter < $chapter_count)
					$chapter_nav .= '<a href="./?b_code=' . $b_code . '&book=' . $book . '&chapter=' . ($chapter + 1) . '#top-anchor"><button class="btn btn-default">' . $text['next_chapter'] . '</button></a>';
				$chapter_nav .= '</td></tr></table>'; 

		?>
			<div id="book-title"><center><h3><?=$book;?> <?=$chapter;?></h3></center></div>
			<nav class="gb-pagination"><?=$chapters_links.'<br/>'.$chapters_form;?></nav><br />
			<nav class="gb-chapter-nav"><?=$chapter_nav;?></nav>

		</div>
		<?php

			$statement_verses = $links['sofia']['pdo'] -> prepare (
						'select * from ' . $table_name .
						' where book = :book and chapter = :chapter'
				);
			$result_verses = $statement_verses -> execute(array('book' => $book, 'chapter' => $chapter));

			if(!$result_verses)
				log_msg(__FILE__ . ':' . __LINE__ . ' ' . ' PDO verses query exception. Info = {' . json_encode($statement_verses -> errorInfo())  . '}, $_REQUEST = {' . json_encode($_REQUEST) . '}, \$table_name = `' . $table_name . '`.');

			$verses_rows = $statement_verses -> fetchAll();
			$verses = '';
			$b_start = '';
			$b_end = '';
			foreach ($verses_rows as $verse_row) 
			{
				$verses .= html_verse($verse_row);
			}
		?>
		<div class="panel-body"><?=$verses;?></div>

	<nav class="gb-chapter-nav"><?=$chapter_nav;?></nav><br />
	<nav class="gb-pagination"><?=$chapters_links;?></nav><br />
	<nav class="gb-books-nav"><?=$books_nav;?></nav>

	<?php
		}
		else
		{
	?>	
		<script type="text/javascript">$(document).ready(function (){resize();});</script>
		<div class="panel-body"><center><iframe src="<?=$info_row['http_link'];?>" width="80%" id="BibleFrame" name="BibleFrame" onresize="alert('resize');resize();"></iframe></center></div>
	<?php
		}
	?>


		<div class="panel-footer">
			<center><h5><b><?=$info_row['title'];?></b></h5><br /><?=$info_row['copyright'];?><br /><?=$text['published_under'];?> <a href="<?=$info_row['link'];?>" target="_blank"><?=$info_row['license'];?></a></center>
		</div>
	</div>
	<?php
		if (stripos($b_code, '_http') === FALSE)
		{	
			echo '<p style="font-size: 0.75em">' . $verse_paragraph_title . '</p>';
		}
	?>