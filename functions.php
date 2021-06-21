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

	public static function makeXML($data, $directory, $batch_type, $number_children, $number_pages, $book_title) {

	   $schema_uri = 'http://www.loc.gov/mods/v3';
	   ini_set('auto_detect_line_endings', TRUE);
	  
		for( $i = 0; $i < count($data); $i++ ) { 
			
			if($i > 0):
				$mods = new \SimpleXMLElement('<mods:mods xmlns:mods="http://www.loc.gov/mods/v3" xmlns="http://www.loc.gov/mods/v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink"></mods:mods>');
				
				// Create XML elements in variable $mods
				if($data[$i]['Dublin Core:Title'] != ''):
					$title = $mods->addChild('mods:titleInfo', null, $schema_uri);
					$title->addChild('mods:title', htmlspecialchars($data[$i]['Dublin Core:Title']), $schema_uri);
				endif;

				if($data[$i]['Dublin Core:Identifier'] != ''):
					$identifier = $mods->addChild('mods:identifier', htmlspecialchars($data[$i]['Dublin Core:Identifier']), $schema_uri);
					$identifier->addAttribute('type', 'local');
				endif;

				if($data[$i]['Dublin Core:Description'] != ''):
					$abstract = $mods->addChild('mods:abstract', htmlspecialchars($data[$i]['Dublin Core:Description']), $schema_uri);
				endif;
            
				if($data[$i]['Dublin Core:Creator'] != ''):
                    $creators = explode(';', $data[$i]['Dublin Core:Creator']);
                    foreach($creators as $s):
                        $mods_name = $mods->addChild('mods:name', null, $schema_uri);
                        $trimmed_creator = trim($s);
                        $creator_part = $mods_name->addChild('namePart', htmlspecialchars($trimmed_creator), $schema_uri);
					   $role = $creator_part->addChild('mods:role', null, $schema_uri);
					   $role_term = $role->addChild('mods:roleTerm', 'creator', $schema_uri);
					   $role_term->addAttribute('type', 'text');
					   $role_term->addAttribute('authority', 'marcrelator');
                    endforeach;
				endif;
            
				if($data[$i]['Dublin Core:Contributor'] != ''):
                    $contributors = explode(';', $data[$i]['Dublin Core:Contributor']);
                    foreach($contributors as $s):
                        $mods_name = $mods->addChild('mods:name', null, $schema_uri);
                        $trimmed_contributor = trim($s);
                        $contributor_part = $mods_name->addChild('namePart', htmlspecialchars($trimmed_contributor), $schema_uri);
					   $role = $contributor_part->addChild('mods:role', null, $schema_uri);
					   $role_term = $role->addChild('mods:roleTerm', 'contributor', $schema_uri);
					   $role_term->addAttribute('type', 'text');
					   $role_term->addAttribute('authority', 'marcrelator');
                    endforeach;
				endif;
            
				$mods_subject = $mods->addChild('mods:subject', null, $schema_uri);

                /*if($data[$i]['Dublin Core:Subject Local'] != ''):
					$subjects = explode(';', $data[$i]['Dublin Core:Subject Local']);

					foreach($subjects as $s):
						$trimmed_subject = trim($s);
						$topic = $mods_subject->addChild('mods:topic', htmlspecialchars($trimmed_subject), $schema_uri);
                        $topic->addAttribute('authority', $subj_auth);
                        $topic->addAttribute('authorityURI', $subj_auth_uri);
					endforeach;
				endif;*/
            
				if($data[$i]['Dublin Core:Subject'] != ''):
					$subjects = explode(';', $data[$i]['Dublin Core:Subject']);
                    $subj_auth = $data[$i]['Dublin Core:Subject Authority'];
                    $subj_auth_uri = $data[$i]['Dublin Core:Subject Authority URI'];
					foreach($subjects as $s):
						$trimmed_subject = trim($s);
						$topic = $mods_subject->addChild('mods:topic', htmlspecialchars($trimmed_subject), $schema_uri);
                        $topic->addAttribute('authority', $subj_auth);
                        $topic->addAttribute('authorityURI', $subj_auth_uri);
					endforeach;
				endif;
            
                if($data[$i]['Dublin Core:Subject Local'] != ''):
					$subjects = explode(';', $data[$i]['Dublin Core:Subject Local']);
                    $subj_auth = $data[$i]['Dublin Core:Subject Local Authority'];
                    $subj_auth_uri = $data[$i]['Dublin Core:Subject Local Authority URI'];
					foreach($subjects as $s):
						$trimmed_subject = trim($s);
						$topic = $mods_subject->addChild('mods:topic', htmlspecialchars($trimmed_subject), $schema_uri);
                        $topic->addAttribute('authority', $subj_auth);
                        $topic->addAttribute('authorityURI', $subj_auth_uri);
					endforeach;
				endif;

				if($data[$i]['Dublin Core:Subject Name'] != ''):
                    $subjectname = explode(';', $data[$i]['Dublin Core:Subject Name']);
					foreach($subjectname as $s):
						$trimmed_subjectname = trim($s);
						$topic = $mods_subject->addChild('mods:name', htmlspecialchars($trimmed_subjectname), $schema_uri);
					endforeach;
                /*$mods_subject->addChild('mods:name', htmlspecialchars($data[$i]['Dublin Core:Subject Name']), $schema_uri);*/
				endif;

				if($data[$i]['Dublin Core:Spatial Coverage'] != ''):
                    $subjectgeo = explode(';', $data[$i]['Dublin Core:Spatial Coverage']);
					foreach($subjectgeo as $s):
						$trimmed_subjectgeo = trim($s);
						$spatial_coverage = $mods_subject->addChild('mods:geographic', htmlspecialchars($trimmed_subjectgeo), $schema_uri);
					endforeach;
					/*$spatial_coverage = $mods_subject->addChild('mods:geographic', $data[$i]['Dublin Core:Spatial Coverage'], $schema_uri);*/
				endif;

				if($data[$i]['Geolocation:GIS Coordinates'] != ''):
					$spatial_coverage->addChild('mods:cartographics', null, $schema_uri)->addChild('mods:coordinates', $data[$i]['Geolocation:GIS Coordinates'], $schema_uri);
				endif;

				$origin_info = $mods->addChild('mods:originInfo', null, $schema_uri);

				if($data[$i]['Dublin Core:Publisher'] != ''):
					$publisher = $origin_info->addChild('mods:publisher', htmlspecialchars($data[$i]['Dublin Core:Publisher']), $schema_uri);
				endif;

				if($data[$i]['Dublin Core:Date'] != ''):
					$origin_info->addChild('mods:dateCreated', htmlspecialchars($data[$i]['Dublin Core:Date']), $schema_uri);
				endif;

				if($data[$i]['Dublin Core:Date Created']):
					$origin_info->addChild('mods:dateCaptured', htmlspecialchars($data[$i]['Dublin Core:Date Created']), $schema_uri);
				endif;

				if($data[$i]['MODS:Date Qualifier'] != ''):
					$dateOther = $origin_info->addChild('mods:dateOther', htmlspecialchars($data[$i]['MODS:Date Qualifier']), $schema_uri);
                    $dateOther->addAttribute('qualifier', 'approximate');
				endif;

				$physical_description = $mods->addChild('mods:physicalDescription', null, $schema_uri);

				/*if($data[$i]['Dublin Core:Extent'] != ''):
					$physical_description->addChild('mods:extent', htmlspecialchars($data[$i]['Item Type Metadata:Width'].' x '.$data[$i]['Item Type Metadata:Height'] .' inches'), $schema_uri);
				endif;*/

            if($data[$i]['Dublin Core:Extent'] != ''):
					$physical_description->addChild('mods:extent', htmlspecialchars($data[$i]['Dublin Core:Extent']), $schema_uri);
				endif;
            
				if($data[$i]['Item Type Metadata:Caption'] != ''):
					$note = $physical_description->addChild('mods:note', htmlspecialchars($data[$i]['Item Type Metadata:Caption']), $schema_uri);
					$note->addAttribute('displayLabel', 'caption');
				endif;

				if($data[$i]['Item Type Metadata:Transcription'] != ''):
					$transcription = $physical_description->addChild('mods:note', htmlspecialchars($data[$i]['Item Type Metadata:Transcription']), $schema_uri);
					$transcription->addAttribute('displayLabel', 'transcription');
				endif;

				if($data[$i]['Dublin Core:Type'] != ''):
					$physical_description->addChild('mods:typeOfResource', htmlspecialchars($data[$i]['Dublin Core:Type']), $schema_uri);
				endif;

				if($data[$i]['Dublin Core:Format'] != ''):
					$physical_description->addChild('mods:internetMediaType', htmlspecialchars($data[$i]['Dublin Core:Format']), $schema_uri);
				endif;

				/*if($data[$i]['geolocation:GIS coordinates'] != ''):
					$address = $physical_description->addChild('mods:note', htmlspecialchars($data[$i]['geolocation:GIS coordinates']), $schema_uri);
					$address->addAttribute('type', 'locality');
					$address->addAttribute('displayLabel', 'Locality');
				endif;*/
            
				if($data[$i]['MODS:Physical Location'] != ''):
					$mods->addChild('mods:location', null, $schema_uri)->addChild('mods:physicalLocation', htmlspecialchars($data[$i]['MODS:Physical Location']), $schema_uri);
				endif;

                /*	if($data[$i]['Dublin Core:Language'] != ''):
                        $mods_lang = $mods->addChild('mods:language', null, $schema_uri);
                        $language = explode(';', $data[$i]['Dublin Core:Language']);
                        foreach($language as $s):
                            $trimmed_language = trim($s);
                            $language_term = $mods_lang->addChild('languageTerm', htmlspecialchars($trimmed_language), $schema_uri);
                        endforeach;*/
            
                        
                if($data[$i]['Dublin Core:Language'] != ''):
                        $mods_lang = $mods->addChild('mods:language', null, $schema_uri);
                        $language = explode(';', $data[$i]['Dublin Core:Language']);
                        foreach($language as $s):
                            $trimmed_language = trim($s);
                            $language_term = $mods_lang->addChild('languageTerm', htmlspecialchars($trimmed_language), $schema_uri);
                            $language_term->addAttribute('authority', 'iso639-2b');
                            $language_term->addAttribute('authorityURI', 'http://id.loc.gov/vocabulary/iso639-2');
                            $language_term->addAttribute('type', 'text');
                        endforeach;
                endif;

            
                if($data[$i]['Finding Aid Location'] != ''):
					$faUrl = $mods->addChild('mods:note', htmlspecialchars($data[$i]['Finding Aid Location']), $schema_uri);
                    $faUrl->addAttribute('type', 'finding aid url');
                    //$faUrl->addAttribute('xlink', 'href="',['Finding Aid Location'],'"')
				endif;
            
				if($data[$i]['Dublin Core:Rights'] != ''):
					$rights_statement = 'The Providence Public Library encourages the use of all items in the Providence Public Library digital collections. It is solely the patron\'s obligation to determine and ensure that use of material fully complies with copyright law and other possible restrictions on use. This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.';
					
					$mods->addChild('mods:accessCondition', /*htmlspecialchars($data[$i]['Dublin Core:Rights'])*/ htmlspecialchars($rights_statement), $schema_uri);
				endif;
            
                /*if($data[$i]['Finding Aid Location'] != ''):
					$mods->addChild('mods:note', null, $schema_uri)->addChild('mods:physicalLocation', htmlspecialchars($data[$i]['MODS:Physical Location']), $schema_uri);
				endif;*/

				$mods->preserveWhiteSpace = true;
				$mods->formatOutput = true;
			
			endif;
			
			
			
			$identifier = $data[$i]['Dublin Core:Identifier'];
			
			// save each row as an xml file, if an image file is present in the images directory, copy that file then unlink the version in the images directory
			if($batch_type == 'basic'):
				$xml_file = $directory . '/' . $identifier . '.xml';
			
				if($i > 0):
					
					$mods->asXML($xml_file);
					
					if($img_file == 'undercopyright.jpg'):
						copy('images/image_under_copyright.png', $directory.'/'.$identifier.'.png');
						echo $identifier . ' is under copyright';
					endif;
					
					if($img_file != 'undercopyright.jpg'):
						if(file_exists( 'images/'.$identifier.'.jpg')):
							copy( 'images/'.$identifier.'.jpg', $directory.'/'.$identifier.'.jpg');
							unlink('images/'.$identifier.'.jpg');
						endif;
					endif;

				endif;
			endif;
			// if this is a compound batch generate the directory structure and copy the required files
			if($batch_type == 'compound'):
				if($i > 0):
					$xml_file = $directory . '/' . 'MODS.xml';

					$structure = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><islandora_compound_object/>');
					$structure->addAttribute('title', htmlspecialchars($identifier));
					
					for($c = 1; $c<=$number_children; $c++) {
						$child = $structure->addChild('child');
						$child->addAttribute('content', htmlspecialchars($identifier).'/OBJ_'.$c);
					}
				
					$parentDirectory = $directory . '/' . $identifier;

					mkdir($parentDirectory, 0777, true);

					

					if(file_exists($parentDirectory)):
						$mods->asXML($parentDirectory.'/MODS.xml');
						$structure->asXML($parentDirectory.'/structure.xml');

						for($d=1; $d<=$number_children; $d++) {
							$xml_file_path = $directory . '/' . $identifier . '/OBJ_'.$d;
							mkdir($xml_file_path, 0777, true);

							if(file_exists($directory.'/OBJ_'.$d)):
								rmdir($directory.'/OBJ_'.$d);
							endif;

							$mods->asXML($xml_file_path.'/MODS.xml');

							if(file_exists($directory.'/MODS.xml')):
								unlink($directory.'/MODS.xml');
								unlink($directory.'/structure.xml');
							endif;

							if(file_exists('img/'.trim($identifier).'-'.$d.'.jpg')):
									copy('img/'.trim($identifier).'-'.$d.'.jpg', $xml_file_path . '/OBJ.jpg');	
									unlink('img/'.trim($identifier).'-'.$d.'.jpg');
							endif;
						}
					endif; 
				endif;
			endif; // End compound batch 

			if($batch_type == 'book'):	
				
				if($i > 0):
					$parentDirectory = $directory .'/' . $book_title;
					if(!file_exists($parentDirectory)):
						mkdir($parentDirectory, 0777, true);
					endif;
					if(file_exists($parentDirectory)):
						
						$mods->asXML($parentDirectory .'/MODS.xml');

						for($b = 1; $b <= $number_pages; $b++) {
							
							mkdir($parentDirectory . '/'.$b, 0777, true);
							$xml_file_path = $parentDirectory .'/'.$b;
							$mods->asXML($xml_file_path. '/MODS.xml');

							if(file_exists('img/page_'.$b.'.jpg')):
								copy('img/page_'.$b.'.jpg', $xml_file_path . '/OBJ.jpg');
							endif;
						}
					endif;

				endif;
			endif;

		}
	}
}
