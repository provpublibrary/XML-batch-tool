<?php
if(isset($_FILES)): 
	$pid_file = $_FILES['pid_file']['name'];
	$dir_name = str_replace('.txt', '',$pid_file);
	$mods_dir = $_POST['pid_dir'];
	//var_dump($mods_dir);

	if(!file_exists($pid_file)):
		move_uploaded_file($_FILES['pid_file']['tmp_name'], $pid_file);

		$fh = fopen($pid_file, 'r');
		
		$pids;

		if(!file_exists($dir_name)) {
			mkdir($dir_name, 0777, true);

		}

		while($line = fgets($fh)) {
			if(strpos($line, 'has child: ')):
				$line = explode('has child: ', $line);
				$parent = $line[0];
				$child = $line[1];
				$pids[] = array(
						'parent' => trim($parent),
						'child' => trim($child),
				);
			endif;
		}

		foreach($pids as $pid) {
			$parent_file = str_replace(':', '_', $pid['parent']);
			$parent_file = $parent_file.'_MODS.xml';
			$child_file = str_replace(':', '_', $pid['child']) .'_MODS.xml';

			if(file_exists($mods_dir  . '/' . $parent_file)) {
				copy($mods_dir .'/'.$parent_file, $dir_name . '/' . $child_file);
			} else {
				print $parent_file . ' does not exist';
			}
		}

		unlink($pid_file);

		
		
	endif;

else:
	echo 'You did not select a file.';
endif;

?>


<form action="" name="upload_form" method="POST" enctype="multipart/form-data">
		<fieldset>
			<lable>PID file</lable>
			<input type="file" name="pid_file" />
		</fieldset>
		<fieldset>
			<lable>Mods directory</lable>
			<input type="text" name="pid_dir" />
		</fieldset>
		<input type="submit" />
</form>

