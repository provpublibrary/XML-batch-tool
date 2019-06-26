<?php
namespace getCSV;

class getFile
{
	protected $_file;

	protected $_directory;

	public function __construct($file, $directory)
	{
		$file = $this->_file;
		$directory = $this->_directory;
	}

	public static function makeDirectory($file, $directory)
	{
		if(!file_exists($directory)):
			mkdir($directory, 0777, true);
		else:

		endif;

		$uploaded_file = $directory.'/'.$file;

		if(!file_exists($uploaded_file)):

			move_uploaded_file($_FILES['csv_file']['tmp_name'], $directory  .'/' . $file);

		elseif(file_exists($uploaded_file)):

			//echo 'The file '. $uploaded_file .' already exists';

		endif;
	}

	public static function getHeaders($file, $directory)
	{
		global $metadata_headers;
		$row = 0;
		$counter = 0;
		$flag = true;
		$schema_uri = 'http://www.loc.gov/mods/v3';

		ini_set('auto_detect_line_endings', TRUE);

		if( ($handle = fopen($directory .'/' . $file, 'r')) !== FALSE) {
			while( ($data = fgetcsv($handle, 0, ',', '"', '"')) !== FALSE) {

				if($counter == 0) {

					$metadata_headers = $data;
					$counter ++;
				}
			}
		}
	}

	public static function mapHeaders($file, $directory, $metadata_headers)
	{
		global $data;

		$data = array();

		if( ($handle = fopen( $directory .'/' . $file, 'r')) !== FALSE) {

			while( $row = fgetcsv($handle) ) {
				$data[] = array_combine($metadata_headers, $row);
			}

		}
	}

	public static function makeXML($data, $directory, $batch_type, $number_children, $number_pages, $book_title)
	{

	   $schema_uri = 'http://www.loc.gov/mods/v3';
	   ini_set('auto_detect_line_endings', TRUE);

		for( $i = 0; $i < count($data); $i++ ) {

			if($i > 0):
				$mods = new \SimpleXMLElement('<mods:mods xmlns:mods="http://www.loc.gov/mods/v3" xmlns="http://www.loc.gov/mods/v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink"></mods:mods>');


				// Create XML elements in variable $mods
				if($data[$i]['Title'] != ''):
					$title = $mods->addChild('mods:titleInfo', null, $schema_uri);
					$title->addChild('mods:title', htmlspecialchars($data[$i]['Title']), $schema_uri);
				endif;

				if($data[$i]['Identifier'] != ''):
					$identifier = $mods->addChild('mods:identifier', htmlspecialchars($data[$i]['Identifier']), $schema_uri);
					$identifier->addAttribute('type', 'local');
				endif;

				if($data[$i]['Description'] != ''):
					$abstract = $mods->addChild('mods:abstract', htmlspecialchars($data[$i]['Description']), $schema_uri);
				endif;

				if($data[$i]['Creator'] != ''):
					$creators = explode(';', $data[$i]['Creator']);

					foreach($creators as $c) {
						$creator_node = $mods->addChild('mods:name', null, $schema_uri);
						$creator_node->addChild('namePart', trim($c), $schema_uri);
						$role = $creator_node->addChild('mods:role', null, $schema_uri);
						$role_term = $role->addChild('mods:roleTerm', 'creator', $schema_uri);
						$role_term->addAttribute('type', 'text');
						$role_term->addAttribute('authority', 'marcrelator');
					}
				endif;

				if($data[$i]['Contributor'] != ''):
					$contributors = explode(';', $data[$i]['Contributor']);
					foreach($contributors as $con) {
						$contributor_node = $mods->addChild('mods:name', null, $schema_uri);
						$contributor_node->addChild('namePart', trim($con), $schema_uri);
						$contributor_role = $contributor_node->addChild('mods:role', null, $schema_uri);
						$role_term = $contributor_role->addChild('mods:roleTerm', 'contributor', $schema_uri);
						$contributor_role->addAttribute('type', 'text');
						$contributor_role->addAttribute('authority', 'marcrelator');
					}
				endif;

				$mods_subject = $mods->addChild('mods:subject', null, $schema_uri);

				if($data[$i]['Subject'] != ''):
					$subjects = explode(';', $data[$i]['Subject']);
					foreach($subjects as $s):
						$trimmed_subject = trim($s);
						$topic = $mods_subject->addChild('mods:topic', htmlspecialchars($trimmed_subject), $schema_uri);
					endforeach;
				endif;

				if($data[$i]['Subject Name'] != ''):
					$mods_subject->addChild('mods:name', htmlspecialchars($data[$i]['Subject Name']), $schema_uri);
				endif;

				if($data[$i]['Subject Geographic'] != ''):
					$geographic_subjects = explode(';', $data[$i]['Subject Geographic']);
					foreach($geographic_subjects as $geo) {
						$trimmed_geo = trim($geo);
						$spatial_coverage = $mods_subject->addChild('mods:geographic', htmlspecialchars($trimmed_geo), $schema_uri);
					}
				endif;

				if($data[$i]['geolocation:address'] != ''):
					$spatial_coverage->addChild('mods:cartographics', null, $schema_uri)->addChild('mods:coordinates', $data[$i]['geolocation:address'], $schema_uri);
				endif;

				$origin_info = $mods->addChild('mods:originInfo', null, $schema_uri);

				if($data[$i]['Publisher'] != ''):
					$publisher = $origin_info->addChild('mods:publisher', htmlspecialchars($data[$i]['Publisher']), $schema_uri);
				endif;

				if($data[$i]['Date Original'] != ''):
					$origin_info->addChild('mods:dateCreated', htmlspecialchars($data[$i]['Date Original']), $schema_uri);
				endif;

				if($data[$i]['Date Created'] != ''):
					$origin_info->addChild('mods:dateCaptured', htmlspecialchars($data[$i]['Date Created']), $schema_uri);
				endif;

				if($data[$i]['Date Qualifier'] != ''):
					$dateOther = $origin_info->addChild('mods:dateOther', htmlspecialchars($data[$i]['Date Qualifier']), $schema_uri);
				endif;

				$physical_description = $mods->addChild('mods:physicalDescription', null, $schema_uri);

				if($data[$i]['Extent'] != ''):
					$physical_description->addChild('mods:extent', htmlspecialchars($data[$i]['Extent']), $schema_uri);
				endif;

				if($data[$i]['Type'] != ''):
					$physical_description->addChild('mods:typeOfResource', htmlspecialchars($data[$i]['Type']), $schema_uri);
				endif;

				if($data[$i]['Format'] != ''):
					$physical_description->addChild('mods:internetMediaType', htmlspecialchars($data[$i]['Format']), $schema_uri);
				endif;

				if($data[$i]['Item Type Metadata:Caption'] != ''):
					$note = $physical_description->addChild('mods:note', htmlspecialchars($data[$i]['Item Type Metadata:Caption']), $schema_uri);
					$note->addAttribute('displayLabel', 'caption');
				endif;

				if($data[$i]['Item Type Metadata:Transcription'] != ''):
					$transcription = $physical_description->addChild('mods:note', htmlspecialchars($data[$i]['Item Type Metadata:Transcription']), $schema_uri);
					$transcription->addAttribute('displayLabel', 'transcription');
				endif;


				if($data[$i]['Language'] != ''):
					$languages = explode(';', $data[$i]['Language']);
					$lang_node = $mods->addChild('mods:language', null, $schema_uri);
					foreach($languages as $l) {
						$lang_term = $lang_node->addChild('mods:languageTerm', trim($l), $schema_uri);
						$lang_term->addAttribute('type', 'text');
					}
				endif;

				if($data[$i]['Physical Location'] != ''):
					$mods->addChild('mods:location', null, $schema_uri)->addChild('mods:physicalLocation', htmlspecialchars($data[$i]['Physical Location']), $schema_uri);
				endif;

				if($data[$i]['Collection'] != '') {
						$relatedItem = $mods->addChild('mods:relatedItem', null, $schema_uri);
						$relatedItem->addAttribute('type', 'host');
						$collectionTitle = $relatedItem->addChild('mods:titleInfo', null, $schema_uri);
						$collectionTitle->addChild('title', htmlspecialchars($data[$i]['Collection']), $schema_uri);
				}

				if($data[$i]['Rights'] != ''):
					$rights_statement = 'The Providence Public Library encourages the use of all items in the Providence Public Library digital collections. It is solely the patron\'s obligation to determine and ensure that use of material fully complies with copyright law and other possible restrictions on use. This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.';

					$mods->addChild('mods:accessCondition', htmlspecialchars($rights_statement), $schema_uri);
				endif;

			endif;

			$xml_file = $directory . '/' . $data[$i]['Identifier'] . '.xml';

			if($i > 0):
				$mods->asXML($xml_file);
			endif;
		}
	}
}
