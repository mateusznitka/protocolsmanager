<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="charset=utf-8" />
<style type="text/css">
body
{
	font-size: <?php echo $fontsize; ?>;
	font-family: '<?php echo $font; ?>';
}
footer 
{ 
	position: fixed; bottom: -10px; left: 0px; right: 0px; height: 50px; text-align: center; font-size: 8pt; color: gray;
}
#items
{
	border: solid 0.5px black; width:100%; position: relative; table-layout: <?php echo ($breakword == 1) ? 'fixed' : 'auto'; ?>;
}

#items th
{
	border: solid 0.5px black; padding: 2px; background-color: <?php echo $header_color; ?>;
}

#items td
{
	border: solid 0.5px black; padding: 2px;
	<?php
	if ($breakword == 1) {
		echo 'word-wrap: break-word; overflow-wrap: break-word;';
	}
	?>
}

</style>
</head>
<body>
<?php 
	if ($islogo == 1) {
		$logo_height = $logo_height ?? 20;
		$logo_align  = $logo_align  ?? 'left';
		echo '<div style="text-align: ' . htmlspecialchars($logo_align) . ';">';
		echo '<img src="' . $logo . '" style="height: ' . (int)$logo_height . 'mm; width: auto; max-width: 100%;">';
		echo '</div>';
	}
?>
	<table style="border: none; width: 100%;">
		<td style="height: 8mm; width: 70%;"><?php echo $prot_num; echo "-"; echo date('dmY'); ?></td>
		<td style="height: 8mm; width: 30%; text-align: right;"><?php echo $city." "; $date=date('d.m.Y'); echo $date; ?></td>
	</table>
	
	<table style="border:none; width: 100%;">
		<tr>
			<td style="border:none; text-align: center; width: 100%; font-size: 15pt;  height: 15mm;">
			<?php echo $title; ?>
			</td>
		</tr>
	</table>
<br>
<table>
	<tr>
		<td style="weight:100%;">
<?php echo $upper_content; ?>
		</td>
	</tr>
</table>
<br>
<table id="items" cellspacing="0">
<?php
	$man_mode    = $man_mode    ?? 1;
	$show_state  = $show_state  ?? 0;
	$lp_th_attr  = ($breakword == 1) ? ' style="width:5%"' : '';
	$has_comments = !empty(array_filter($comments));

	if (!empty($number)) :

	// Build header
	echo '<tr><th' . $lp_th_attr . '></th>';
	echo '<th>' . __('Type') . '</th>';
	if ($man_mode == 2) {
		echo '<th>' . __('Manufacturer') . '</th>';
		echo '<th>' . __('Model') . '</th>';
	} else {
		echo '<th>' . __('Model') . '</th>';
	}
	echo '<th>' . __('Name') . '</th>';
	if ($serial_mode == 1) {
		echo '<th>' . __('Serial number') . '</th>';
		echo '<th>' . __('Inventory number') . '</th>';
	} else {
		echo '<th>' . __('Serial number') . '</th>';
	}
	if ($show_state) {
		echo '<th>' . __('Status') . '</th>';
	}
	if ($has_comments) {
		echo '<th>' . __('Comments') . '</th>';
	}
	echo '</tr>';

	// Build rows
	$lp = 1;
	foreach ($number as $key) {
		if (!isset($type_name[$key])) { $lp++; continue; }

		$serial_val = $serial[$key] ?? '';
		if ($serial_mode == 2 && empty($serial_val)) {
			$serial_val = $otherserial[$key] ?? '';
		}

		echo '<tr><td>' . $lp . '</td>';
		echo '<td>' . ($type_name[$key] ?? '') . '</td>';
		if ($man_mode == 2) {
			echo '<td>' . ($man_name[$key] ?? '') . '</td>';
			echo '<td>' . ($mod_name[$key] ?? '') . '</td>';
		} else {
			echo '<td>' . ($man_name[$key] ?? '') . ' ' . ($mod_name[$key] ?? '') . '</td>';
		}
		echo '<td>' . ($item_name[$key] ?? '') . '</td>';
		if ($serial_mode == 1) {
			echo '<td>' . ($serial[$key] ?? '') . '</td>';
			echo '<td>' . ($otherserial[$key] ?? '') . '</td>';
		} else {
			echo '<td>' . $serial_val . '</td>';
		}
		if ($show_state) {
			echo '<td>' . htmlspecialchars($state_name[$key] ?? '') . '</td>';
		}
		if ($has_comments) {
			echo '<td>' . ($comments[$key] ?? '') . '</td>';
		}
		echo '</tr>';
		$lp++;
	}

	endif;
?>
</table>

<br>

<table>
	<tr>
		<td style="height: 10mm;"></td>
	</tr>
</table>

<table>
	<tr>
		<td style="weight:100%;">
<?php echo $content; ?>
		</td>
	</tr>
</table>

<table>
	<tr>
		<td style="height: 20mm;"></td>
	</tr>
</table>

<table style="border-collapse: collapse; width: 100%;">
	<tr>
		<td style="width:50%; border-bottom: 1px solid black;"><strong><?php echo __('Administrator').":"; ?></strong></td>
		<td style="width:50%; border-bottom: 1px solid black;"><strong><?php echo __('User').":"; ?></strong></td>
	</tr>
	<tr>
		<td style="border: 1px solid black; width:50%; vertical-align:top; height: 20mm">
			<?php echo $author; ?>
		</td>
		<td style="border: 1px solid black; width:50%; vertical-align:top; height: 20mm">
			<?php echo $owner; ?>
		</td>
	</tr>
</table>

<footer>
<?php echo $footer; ?>
</footer>
</body>
</html>