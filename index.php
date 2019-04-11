<?php
require_once('functions.php');
$file;
$directory;
if(isset($_FILES['csv_file']) && isset($_POST['batch_type'])):
	
	$file = basename($_FILES['csv_file']['name']);

	$file_name = explode('.', $file);

	$directory = $file_name[0] .'_ingest';

	$batch_type = $_POST['batch_type'];

	$number_children = $_POST['number_children'] ? $_POST['number_children'] : '1';

	$number_pages = $_POST['number_pages'] ? $_POST['number_pages'] : NULL;

	$book_title = $_POST['book_title'] ? $_POST['book_title'] : 'book';

	getCSV\getFile::makeDirectory($file, $directory);

	getCSV\getFile::getHeaders($file, $directory);
endif;

if( !isset($_FILES['csv_file']) ):
?>

	<form action="" method="POST" enctype="multipart/form-data">
		<fieldset>
			<label for="csv_file">Metadata CSV File</label>
			<p>Select the csv metadata file to upload.</p>
			<input type="file" name="csv_file" required/>
		<fieldset>
		<fieldset>
			<label for="batch_type">Batch Type</label>
			<select name="batch_type" required>
				<option selected disabled>-- Select the type of batch ingest you are preparing</option>
				<option value="basic">Basic</option>
				<option value="compound">Compound</option>
				<option value="book">Book</option>
			</select>
		</fieldset>
		<fieldset id="image_number">
			<label>Number of images</label>
			<p>Enter the number of child objects to be ingested for each row of data.</p>
			<input type="number" name="number_children">
		</fieldset>
		<fieldset id="page_number">
			<label>Book Title</label>
			<p class="note">The title of the book that will be displayed in islandora</p>
			<input type="text" name="book_title" />
			<br><br>
			<label>Number of book pages</label>
			<input type="number" name="number_pages">
		</fieldset>
		<input type="submit" />
	</form>

<?php
endif;

if( isset($metadata_headers) ):
	getCSV\getFile::mapHeaders($file, $directory, $metadata_headers);
endif;

if( isset($data) ):
	getCSV\getFile::makeXML($data, $directory, $batch_type, $number_children, $number_pages, $book_title);
endif;
?>