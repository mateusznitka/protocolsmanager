<style type="text/css">
	
</style>
<page backtop="<?php echo $backtop; ?>" backleft="8mm" backright="8mm" backbottom="30mm">
	<page_header style="font-family:<?php echo $font; ?>">
		<?php 
		if ($islogo == 1) {
			
			echo '<table style="border: none; width: 100%; ">
					<tr><td style="height: 25mm;"><img src="';
			echo $logo;
			echo '" style="display: block; width: 100%; height: 20mm;">
					</td></tr>
				</table>';
		}
		?>
		<table style="border: none; width: 100%; font-size: 10pt;">
			<td style="height: 8mm; width: 70%;"><?php echo $prot_num; echo "-"; echo date('dmY'); ?></td>
			<td style="height: 8mm; width: 30%; text-align: right;"><?php echo $city; ?> 
				<?php $date=date('d.m.Y'); echo $date; ?>
			</td>
		</table>
	</page_header>
<table style="border:none; width: 100%;">
	<tr>
		<td style="border:none; text-align: center; width: 100%; font-size: 15pt;  height: 15mm;">
			<?php echo $title; ?>
		</td>
	</tr>
</table>
<br>
<table id="items" style="border: solid 0.5px black; width:100%; font-size: 10pt; position: relative; word-wrap: break-word; word-break: break-all; table-layout: fixed;" cellspacing="0">
<?php 
	//if no comments, there is no comments column
	if (empty(array_filter($comments))) {
		
		//if serial and inventory in different columns
		if ($serial_mode == 1) {
			
			echo "<tr>
				<th style='width: 3%; border: solid 0.5px black;'></th>
				<th style='width: 13%; border: solid 0.5px black;'>"; 
				echo __('Type');
				echo "</th><th style='width: 19%; border: solid 0.5px black;'>"; 
				echo __('Model'); 
				echo "</th><th style='width: 25%; border: solid 0.5px black;'>"; 
				echo __('Serial number'); 
				echo "</th><th style='width: 20%; border: solid 0.5px black;'>";
				echo __('Inventory number'); 
				echo "</th><th style='width: 20%; border: solid 0.5px black;'>";
				echo __('Name'); 
				echo "</th>
			</tr>";
			
			$lp = 1;
			foreach ($number as $key) {
				if (isset($type_name[$key])) {
				echo '<tr><td style="width: 3%; border: solid 0.5px black;">';
				echo $lp . '</td><td style="width: 13%; border: solid 0.5px black;">' . $type_name[$key];
				echo '</td><td style="width: 19%; border: solid 0.5px black;">';
				echo $man_name[$key] .' '. $mod_name[$key]. '</td><td style="width: 25%; border: solid 0.5px black;">' . $serial[$key];
				echo '</td><td style="width: 20%; border: solid 0.5px black;">' . $otherserial[$key] .'</td><td style="width: 20%; border: solid 0.5px black;">';
				echo $item_name[$key];
				echo '</td></tr>';
				}
				$lp++;
			}
		}

		//if serial and inventory in one column
		if ($serial_mode == 2) {
			
			echo "<tr>
				<th style='width: 3%; border: solid 0.5px black;'></th>
				<th style='width: 16%; border: solid 0.5px black;'>"; 
				echo __('Type');
				echo "</th><th style='width: 25%; border: solid 0.5px black;'>"; 
				echo __('Model'); 
				echo "</th><th style='width: 30%; border: solid 0.5px black;'>"; 
				echo __('Serial number'); 
				echo "</th><th style='width: 26%; border: solid 0.5px black;'>";
				echo __('Name');
				echo "</th>
			</tr>";
			
			$lp = 1;
			foreach ($number as $key) {
				if (empty($serial[$key])) {
					$serial[$key]=$otherserial[$key];
				} //if no serial, get inventory number
				if (isset($type_name[$key])){
				echo '<tr><td style="width: 3%; border: solid 0.5px black;">';
				echo $lp . '</td><td style="width: 16%; border: solid 0.5px black;">' . $type_name[$key];
				echo '</td><td style="width: 25%; border: solid 0.5px black;">';
				echo $man_name[$key] .' '. $mod_name[$key]. '</td><td style="width: 30%; border: solid 0.5px black;">' . $serial[$key];
				echo '</td><td style="width: 26%; border: solid 0.5px black;">';
				echo $item_name[$key];
				echo '</td></tr>';
				}
				$lp++;
			}
		}

	}
	else {
		//if at least one comment, there will be comment column
		if ($serial_mode == 1) {
			
			echo "<tr>
				<th style='width: 3%; border: solid 0.5px black;'></th>
				<th style='width: 12%; border: solid 0.5px black;'>"; 
				echo __('Type');
				echo "</th><th style='width: 14%; border: solid 0.5px black;'>"; 
				echo __('Model'); 
				echo "</th><th style='width: 19%; border: solid 0.5px black;'>"; 
				echo __('Serial number'); 
				echo "</th><th style='width: 19%; border: solid 0.5px black;'>"; 
				echo __('Inventory number'); 
				echo "</th><th style='width: 17%; border: solid 0.5px black;'>";
				echo __('Name'); 
				echo "</th><th style='width: 16%; border: solid 0.5px black;'>";
				echo __('Comments'); 
				echo "</th>
			</tr>";
			
			$lp = 1;
			foreach ($number as $key){
				if (isset($type_name[$key])){
				echo '<tr><td style="width: 3%; border: solid 0.5px black;">';
				echo $lp . '</td><td style="width: 12%; border: solid 0.5px black;">' . $type_name[$key];
				echo '</td><td style="width: 14%; border: solid 0.5px black;">';
				echo $man_name[$key] .' '. $mod_name[$key]. '</td><td style="width: 19%; border: solid 0.5px black;">' . $serial[$key];
				echo '</td><td style="width: 19%; border: solid 0.5px black;">';
				echo $otherserial[$key];
				echo '</td><td style="width: 17%; border: solid 0.5px black;">';
				echo $item_name[$key];
				echo '</td><td style="width: 16%; border: solid 0.5px black; word-wrap:break-word;">';
				echo $comments[$key];
				echo "</td></tr>";
				}
				$lp++;
			}
		}

		if ($serial_mode == 2) {
			
			echo "<tr>
				<th style='width: 3%; border: solid 0.5px black;'></th>
				<th style='width: 13%; border: solid 0.5px black;'>"; 
				echo __('Type');
				echo "</th><th style='width: 19%; border: solid 0.5px black;'>"; 
				echo __('Model'); 
				echo "</th><th style='width: 25%; border: solid 0.5px black;'>"; 
				echo __('Serial number'); 
				echo "</th><th style='width: 20%; border: solid 0.5px black;'>";
				echo __('Name'); 
				echo "</th><th style='width: 20%; border: solid 0.5px black;'>";
				echo __('Comments'); 
				echo "</th>
			</tr>";
			
			$lp = 1;
			foreach ($number as $key){
				if (empty($serial[$key])) {
					$serial[$key]=$otherserial[$key];
				} //if no serial, get inventory number
				if (isset($type_name[$key])){
				echo '<tr><td style="width: 3%; border: solid 0.5px black;">';
				echo $lp . '</td><td style="width: 13%; border: solid 0.5px black;">' . $type_name[$key];
				echo '</td><td style="width: 19%; border: solid 0.5px black;">';
				echo $man_name[$key] .' '. $mod_name[$key]. '</td><td style="width: 25%; border: solid 0.5px black;">' . $serial[$key];
				echo '</td><td style="width: 20%; border: solid 0.5px black;">';
				echo $item_name[$key];
				echo '</td><td style="width: 20%; border: solid 0.5px black;">';
				echo $comments[$key];
				echo "</td></tr>";
				}
				$lp++;
			}
		}

	}
		

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
<page_footer style="text-align: center; font-size: 8pt; color: gray; width: 100%; font-family: <?php echo $font; ?>">
<?php echo $footer; ?>
</page_footer>
</page>