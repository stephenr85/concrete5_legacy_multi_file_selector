<?php

	echo '<ul class="list-docs simple">';
	foreach($fIDArray as $fID){
		$file = File::getByID($fID);
		$fileSize = ($file->getFullSize() < 1048576) ? $file->getSize() : round($file->getFullSize()/1048576, 2).t('MB');		
		echo '<li data-fileId="'.$file->getFileID().'" class="'.$file->getExtension().'"><a href="'.$file->getRelativePath().'" target="$target">';
		echo '<span class="file-title">'.$file->getTitle().'</span>';
		echo '</a> <span class="file-info"><span class="file-type '.$file->getExtension().'">'.$file->getType().'</span> <span class="file-size">'.$fileSize.'</span></span></li>';
	}
	echo '</ul>';
?>
