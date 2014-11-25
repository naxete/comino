<?php

$page_size = 20 * 3;
$comment = new stdClass;
echo 'Parte de comentarios';
echo '<div class="comment-body high" id="cid-15829757">Linea1...<br>Linea2...  </div>';


function print_answers($id, $level, $visited = false) {
	// Print answers to the comment
	global $db, $page_size;
	if (! $visited) {
		$visited = array();
		$visited[] = $id;
	}
	$printed = array();
	$sql = "SELECT conversation_from FROM conversations, comments WHERE conversation_type='comment' and conversation_to = $id and comment_id = conversation_from ORDER BY conversation_from asc LIMIT $page_size";
	$answers = $db->get_col($sql);
	if ($answers) {
		$type = 'comment';
		echo '<div style="padding-left: 6%">';
		echo '<ol class="comments-list">';
		foreach ($answers as $dbanswer) {
			if (in_array($dbanswer, $visited)) continue;
			$answer = Comment::from_db($dbanswer);
			$answer->url = $answer->get_relative_individual_permalink();
			echo '<li>';
			$answer->print_summary($link);
			if ($level > 0) {
				$res = print_answers($answer->id, $level-1, array_merge($visited, $answers));
				$visited = array_merge($visited, $res);
			}
			$printed[] = $answer->id;
			$visited[] = $answer->id;
			echo '</li>';
		}
		echo '</ol>';
		echo '</div>';
		if ($level == 0) {
			$ids = implode(',', $printed);
			Haanga::Load('get_total_answers_by_ids.html', compact('type', 'ids'));
		}
	}
	return $printed;
}

?>
