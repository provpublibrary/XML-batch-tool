<?php

if($batch_type == 'compound'):
          // generate structure file for compound batch
          $structure = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><islandora_compound_object/>');
          $structure->addAttribute('title', htmlspecialchars($metadata[$i]['identifier']));

          for($a=1; $a<=$children; $a++) {
            $child = $structure->addChild('child');
            $child->addAttribute('content', htmlspecialchars($metadata[$i]['identifier']).'/OBJ_'.$a);
          }
        endif;

      $identifier = $metadata[$i]['identifier'];

      // if this is a compound batch generate the directory structure and copy the required files
      if($batch_type == 'compound'):

        $parentDirectory = $directory .'/'.$identifier;
        mkdir($parentDirectory, 0777, true);

        $imgDir = 'images/';

        $c;

        if(file_exists($parentDirectory)) {

          $mods->asXML($parentDirectory.'/MODS.xml');
          $structure->asXML($parentDirectory.'/structure.xml');

          for($c=1;$c<=$children;$c++) {
            $xml_file_path = $directory.'/'.$identifier.'/OBJ_'.$c;
            mkdir($xml_file_path, 0777, true);

            if(file_exists($directory.'/OBJ_'.$c)):
              rmdir($directory.'/OBJ_'.$c);
            endif;

            $mods->asXML($xml_file_path.'/MODS.xml');

            if(file_exists($directory.'/MODS.xml')):
              unlink($directory.'/MODS.xml');
              unlink($directory.'/structure.xml');
            endif;

            if(file_exists('images/'.trim($identifier).'-'.$c.'.jpg')):
              copy('images/'.trim($identifier).'-'.$c.'jpg', $xml_file_path.'/'.$identifier.'-'.$c.'jpg');
            endif;
          }

        }

      endif;

      // if this is a basic batch save the generated xml and move the corresonding image to the collection directory

      if($batch_type == 'basic'):
          $xml_file_path = $directory.'/' . $identifier.'.xml';

          $mods->asXML($xml_file_path);

          if(file_exists('images/'.$identifier.'.jpg')):
            copy('images/'.$identifier.'.jpg', $directory.'/'.$identifier.'.jpg');
          endif;
      endif;



  }


  if(file_exists($csv_file)):
    unlink($csv_file); // Delete csv file before we make the zip folder to download.
  endif;


?>
