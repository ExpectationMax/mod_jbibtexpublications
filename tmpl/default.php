<?php 
// No direct access
defined('_JEXEC') or die; 
print_r($publications);

if(count($publications) > 0) {
?>
<table>
<thead>
<th>Title</th><th>Authors</th>
</thead>
<tbody>
<?php
foreach($publications as $publ) {
	echo '<tr><td>'.$publ['title'].'</td><td>';
	foreach($publ['author'] as $author) {
		echo implode(' ', $author);
	}
	'</td></tr>';
}
?>
</tbody>
</table>
<?php
} else {
echo $parms->get('nonefound', '');
}

?>