<div class="container">
	<div class="row">
		<div class="col-md-12">
			<center><a href="http://www.israelgives.org/" target="_blank">Israel Gives</a></center>
		</div>
	</div>
</div>
<br />
<!-- <content> -->
<div class="container" id="content">
	<div class="panel panel-primary">
		<div class="panel-header">
		<?php
			$statement_translation = $links['sofia']['pdo'] -> prepare(
					'select bh.table_name, bh.title, bh.description, bh.copyright, 
					bh.license, l.link, country, language, dialect
					from b_shelf bh 
					join licenses l on l.license = bh.license
					where b_code = :b_code'
				);


			$result_translation = $statement_translation -> execute(array('b_code' => $b_code));
			if(!$result_translation)
				log_msg(__FILE__ . ' ' . __LINE__ . ' ' . $statement_translation -> errorInfo());
			$info_row = $statement_translation -> fetch();
			$bible_title = $info_row['title'];
			$bible_description = $info_row['description'];
			$table_name = $info_row['table_name'];

			$statement_books = $links['sofia']['pdo'] -> prepare('
								select distinct book from ' . $table_name
							);
			$books_result = $statement_books -> execute();
			$books_rows = $statement_books -> fetchAll();
			$books_nav = '<div width="80%" align="center">';
			foreach ($books_rows as $row) 
			{
				$books_nav .=  '<a href="./?b_code=' . $b_code . '&book=' . $row['book'] . '">' . $row['book'] . '</a> ';
			}
				$books_nav .= '</div>';
		?>
			<div><center><h2 id="bibleTitle" title="<?=$bible_description;?>"><?=$bible_title;?></h2></center></div>
			<nav class="gb-books-nav"><?=$books_nav;?></nav>
		<?php
			if (!isset($book))
				$book = $books_rows[0]['book'];

			if (!isset($chapter))
				$chapter = 1;

			$statement_chapters = $links['sofia']['pdo'] -> prepare (
									'select distinct chapter from ' . $table_name 
									.' where book = :book'
								);
				$result_chapters = $statement_chapters -> execute(array('book' => $book_row['book']));

				if(!$result_chapters)
					log_msg(__FILE__ . ':' . __LINE__ . ' PDO chapters query exception.');

				$chapters_rows = $statement_chapters -> fetchAll();
				$chapters_links = '<div width="80%" align="center">';
				$chapter_count=0;
				foreach ($chapters_rows as $chapter_row) 
				{
					$chapter_count++;
					$chapters_links .= '<a href="./?b_code=' . $b_code . '&book=' . $book . '&chapter=' . $chapter_row['chapter'] . '">[' . $chapter_row['chapter'] . ']</a> ';
				}
				$chapters_links .= '</div>';

				$chapter_nav = '<table width="100%"><tr><td width="50%">';
				if ($chapter > 1)
					$chapter_nav .= '<a href="./?b_code=' . $b_code . '&book=' . $book . '&chapter=' . ($chapter - 1) . '">Previous Chapter</a>';
				$chapter_nav .= '</td><td width="50%" align="right">';
				if ($chapter < $chapter_count)
					$chapter_nav .= '<a href="./?b_code=' . $b_code . '&book=' . $book . '&chapter=' . ($chapter + 1) . '">Next Chapter</a>';
				$chapter_nav .= '</td></tr></table>'; 

		?>
			<div id="book-title"><center><h3><?=$book;?> <?=$chapter;?></h3></center></div>
			<nav class="gb-pagination"><?=$chapters_links;?></nav>
			<nav class="gb-chapter-nav"><?=$chapter_nav;?></nav>

		</div>
		<?php
			$statement_verses = $links['sofia']['pdo'] -> prepare (
						'select startVerse, verseText from ' . $table_name .
						' where book = :book and chapter = :chapter'
				);
			$result_verses = $statement_verses -> execute(array('book' => $book, 'chapter' => $chapter));

			if(!$result_verses)
				log_msg(__FILE__ . ' ' . __LINE__ . ' ' . $statement_verses -> errorInfo());

			$verses_rows = $statement_verses -> fetchAll();
			$verses = '';
			$b_start = '';
			$b_end = '';
			foreach ($verses_rows as $verse_row) 
			{
				if ($verse_row['startVerse'] == $verse)
				{
					$b_start = '<b>';
					$b_end = '</b>';
				}
				else
				{
					$b_start = '';
					$b_end = '';
				}
				$verses .= '<p onclick="clipboard.copy(window.location.origin + window.location.pathname + \'?b_code=' 
					. $b_code . '&book=' . $book . '&chapter=' . $chapter . 
					'&verse=' . $verse_row['startVerse'] . '\')"><sup>' . $verse_row['startVerse'] . '</sup> ' . $b_start . $verse_row['verseText'] . $b_end . '</p>';
			}
		?>
		<div class="panel-body"><?=$verses;?></div>

	<nav class="gb-chapter-nav"><?=$chapter_nav;?></nav><br />
	<nav class="gb-pagination"><?=$chapters_links;?></nav><br />
	<nav class="gb-books-nav"><?=$books_nav;?></nav>

		<div class="panel-footer"><center><h5><b><?=$info_row['title'];?></b></h5><br /><?=$info_row['copyright'];?><br />Published under <a href="<?=$info_row['link'];?>" target="_blank"><?=$info_row['license'];?></a></center></div>
	</div>

</div>
<br />
<!-- </content> -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<center><a href="http://www.israelgives.org/" target="_blank">Israel Gives</a></center>
		</div>
	</div>
</div>