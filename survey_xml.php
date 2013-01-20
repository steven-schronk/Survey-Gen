<?php

if (!$_GET["survey_id"])  { die("No Survey ID Sent"); }
if (file_exists($_GET["survey_id"])) {
	$xml = simplexml_load_file($_GET["survey_id"]);
	print_r($xml);
} else {
    exit('Failed to open survey data.');
}

$q_list = "";
$q_count = 1;
//foreach($xml['question'] as $q)
foreach($xml as $key0 => $value){
	if($q_count == 1)
		$q_list .= '<div class="question" id="q_'.$q_count.'">';
	else
		$q_list .= '<div class="question" id="q_'.$q_count.'" style="display: none">';
	switch($value['type']){
		case "multichoice": $q_list .=	multiplechoice($value, $q_count); break;
		case "truefalse": $q_list .= 	truefalse($value, $q_count); break;
		case "numerical": $q_list .= 	numerical($value, $q_count); break;
		case "essay": $q_list .= 	essay($value, $q_count); break;
		case "scale": $q_list .= 	scale($value, $q_count); break;
		case "shortanswer":$q_list .= 	shortanswer($value, $q_count); break;
	}
	$q_count++;
	$q_list .= "</div>\n";
}

//if($q_count == 1){ exit("This survey does not contain any questions."); }

function scale($value, $q_count){
	$col_count = 0;
	$q = "\n";
	$q .= "<h1>".$value->name->text."</h1>\n";
	$q .= "\t<h2>".$value->questiontext->text."</h2>\n";
	$q .= "\t\t<table class='survey'>\n";
	$q .= "\t\t\t<tr>\n";
	echo $value;
	foreach($value->headings->text as $heading){
		$q .= "\t\t\t\t<td>".$heading."</td>\n";
		$col_count++;
	}
	$q .= "\t\t\t</tr>\n";	
	$q .= "\t\t</table>\n";
	return $q;
}

function multiplechoice($value, $q_count){
	$q = "\n";
	//print_r($value);
	$q .= "<h1>".$value->name->text."</h1>\n";
	$q .= "\t<h2>".$value->questiontext->text."</h2>\n";
	$i = 0;
	foreach($value->answer as $ans){
		$q .= "\t\t<p><input name=q_".$q_count." type='radio'>";
		$q .= $ans->text."\n";
		$i++;
	}
	return $q;
}

function truefalse($value, $q_count){
	$q = "\n";
	$q .= "<h1>".$value->name->text."</h1>\n";
	$q .= "\t<h2>".$value->questiontext->text."</h2>\n";
	$q .= "\t\t<select>\n";
	$q .= "\t\t\t<option></option>\n";
	$q .= "\t\t\t<option value='true'>True</option>\n";
	$q .= "\t\t\t<option value='false'>False</option>\n";
	$q .= "\t\t</select>";
	return $q;
}

function numerical($value, $q_count){
	$q = "\n";
	$q .= "<h1>".$value->name->text."</h1>\n";
	$q .= "\t<h2>".$value->questiontext->text."</h2>\n";
	$q .= "\t\t<input type='text'>";
	$q .= "";
	return $q;
}

function shortanswer($value, $q_count){
	$q = "\n";
	$q .= "<h1>".$value->name->text."</h1>\n";
	$q .= "\t<h2>".$value->questiontext->text."</h2>\n";
	$q .= "\t\t<input type='text'>";
	$q .= "";
	return $q;
}

function essay($value, $q_count){
	$q = "\n";
	$q .= "<h1>".$value->name->text."</h1>\n";
	$q .= "\t<h2>".$value->questiontext->text."</h2>\n";
	$q .= "\t\t<textarea></textarea>";
	$q .= "";
	return $q;
}

?>

<html>
<script>

var current_q = 1;
var total_q = <?php echo $q_count-1; ?>;

function update_progress(){
	document.getElementById("progress").innerHTML = current_q + " of " + total_q;
}

function next_q(){
	//alert(current_q);
	document.getElementById("q_"+current_q).style.display = "none";
	current_q++;
	document.getElementById("q_"+current_q).style.display = "block";

        if(current_q < total_q){
                document.getElementById('next_q_btn').disabled = false;
                document.getElementById('next_q_btn').innerHTML = 'Next<img src="./gfx/resultset_next.png">';
        } else {
                document.getElementById('next_q_btn').disabled = true;
                document.getElementById('next_q_btn').innerHTML = 'Next<img src="./gfx/resultset_next_disabled.png">';
        }
        document.getElementById('prev_q_btn').disabled = false;
        document.getElementById('prev_q_btn').innerHTML = '<img src="./gfx/resultset_previous.png">Back';
	update_progress();
}

function prev_q(){
	//alert(current_q);
	document.getElementById("q_"+current_q).style.display = "none";
	current_q--;
	document.getElementById("q_"+current_q).style.display = "block";

        if(current_q >= 1){
                document.getElementById('prev_q_btn').disabled = false;
                document.getElementById('prev_q_btn').innerHTML = '<img src="./gfx/resultset_previous.png">Back';
        } else {
                document.getElementById('prev_q_btn').disabled = true;
                document.getElementById('prev_q_btn').innerHTML = '<img src="./gfx/resultset_previous_disabled.png">Back';
        }
        document.getElementById('next_q_btn').disabled = false;
        document.getElementById('next_q_btn').innerHTML = 'Next<img src="./gfx/resultset_next.png">';
	update_progress();
}

</script>

<style>
.question {
	background-color: #ccc;
	margin: 10px;
	padding: 10px;
}


</style>
<body>
<h1>Digital Science Academy Survey</h1>

<div id=progress>1 of <?php echo $q_count-1; ?></div>
<button id="prev_q_btn" onClick="prev_q();" disabled><img src="./gfx/resultset_previous_disabled.png">Back</button>
<button id="next_q_btn" onClick="next_q();">Next<img src="./gfx/resultset_next.png"></button>
<br><br>

<form id="form_1" action="send.html" method="get">

<?php echo $q_list; ?>



