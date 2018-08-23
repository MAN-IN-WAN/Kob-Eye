<?php

if(isset($_GET["f"])){
    $f = filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING);
    if($f = "loadImages") {
        loadImages();
    }
}

function loadImages() {
    require(__DIR__ . '/pluginconfig.php');
    
    if(file_exists($useruploadpath)){

        $filesizefinal = 0;
        $count = 0;
        
        $dir = $useruploadpath;
        $files = array();
        //$files = glob("$dir*.{jpg,jpe,jpeg,png,gif,ico}", GLOB_BRACE);
        //if(empty($files)) {
            findImages($dir,$files);
        //}
        sort($files,SORT_STRING);


        //usort($files, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));
        $folder ='';
        for($i=count($files)-1; $i >= 0; $i--):
            $image = $files[$i];
            $image_pathinfo = pathinfo($image);

            $image_directory = $image_pathinfo['dirname'];
            $image_directory = substr($image_directory,strpos($image_directory,$useruploadfolder));
            $image_extension = $image_pathinfo['extension'];
            $image_filename = $image_pathinfo['filename'];
            $image_basename = $image_pathinfo['basename'];
        
            // image src/url
            $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $site = $protocol. $_SERVER['SERVER_NAME'] .'/';
            $image_url = '/'.$image_directory."/".$image_basename;
        
            $size = getimagesize($image);
            $image_height = $size[0];
            $file_size_byte = filesize($image);
            $file_size_kilobyte = ($file_size_byte/1024);
            $file_size_kilobyte_rounded = round($file_size_kilobyte,1);
            $filesizetemp = $file_size_kilobyte_rounded;
            $filesizefinal = round($filesizefinal + $filesizetemp) . " KB";
            $calcsize = round($filesizefinal + $filesizetemp);
            $count = ++$count;


            if($folder != str_replace($useruploadfolder,'',$image_directory)) {
                $folder = str_replace($useruploadfolder, '', $image_directory);
                ?>
                <div style="clear: both;"></div>
                <div class="folder_sep" style="background-color:#ffffdd">
                    <hr>
                    <h3><?php echo $folder? $folder:'/'; ?></h3>
                    <hr>
                </div>
                <?php
            }
            if($file_style == "block") { ?>
                <div class="fileDiv"
                     onclick="showEditBar('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $count; ?>','<?php echo ltrim($folder,'/').'/'.$image_basename; ?>',<?php echo$_SESSION['CKEditorFuncNum']; ?>);"
                     ondblclick="showImage('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo ltrim($folder,'/').'/'.$image_basename; ?>');"
                     data-imgid="<?php echo $count; ?>">
                    <div class="imgDiv"><img class="fileImg lazy" data-original="<?php echo $image_url; ?>"></div>
                    <p class="fileDescription"><span class="fileMime"><?php echo $image_extension; ?></span> <?php echo $image_filename; ?><?php if($file_extens == "yes"){echo ".$image_extension";} ?></p>
                    <p class="fileTime"><?php echo date ("F d Y H:i", filemtime($image)); ?></p>
                    <p class="fileTime"><?php echo $filesizetemp; ?> KB</p>
                </div>
            <?php } elseif($file_style == "list") { ?>
                <div class="fullWidthFileDiv"
                     onclick="showEditBar('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $count; ?>','<?php echo ltrim($folder,'/').'/'.$image_basename; ?>',<?php echo$_SESSION['CKEditorFuncNum']; ?>);"
                     ondblclick="showImage('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo ltrim($folder,'/').'/'.$image_basename; ?>');"
                     data-imgid="<?php echo $count; ?>">
                    <div class="fullWidthimgDiv"><img class="fullWidthfileImg lazy" data-original="<?php echo $image_url; ?>"></div>
                    <p class="fullWidthfileDescription"><?php echo $image_filename; ?><?php if($file_extens == "yes"){echo ".$image_extension";} ?></p>
                    
                    <div class="qEditIconsDiv">
                        <img title="Delete File" src="img/cd-icon-qtrash.png" class="qEditIconsImg" onclick="window.location.href = 'imgdelete.php?img=<?php echo ltrim($folder,'/').'/'.$image_basename; ?>'">
                    </div>
                    
                    <p class="fullWidthfileTime fullWidthfileMime fullWidthlastChild"><?php echo $image_extension; ?></p>
                    <p class="fullWidthfileTime"><?php echo $filesizetemp; ?> KB</p>
                    <p class="fullWidthfileTime fullWidth30percent"><?php echo date ("F d Y H:i", filemtime($image)); ?></p>
                </div>
            <?php }

        endfor;
        if($count == 0){
            echo "<div class='fileDiv' style='display:none;'></div>";
            $calcsize = 0;
        }
        if($calcsize == 0){
            $filesizefinal = "0 KB";
        }
        if($calcsize >= 1024){
            $filesizefinal = round($filesizefinal/1024,1) . " MB";
        }
        
        echo "
        <script>
            $( '#finalsize' ).html('$filesizefinal');
            $( '#finalcount' ).html('$count');
        </script>
        ";
    } else {
        echo '<div id="folderError">'.$alerts9.' <b>'.$useruploadfolder.'</b> '.$alerts10;
    } 
}

function findImages($dir,&$array){
    $dir = rtrim($dir,'/');
    if ($handle = opendir($dir)) {
        // iterate over the directory entries
        while (false !== ($entry = readdir($handle))) {
            if($entry == "." || $entry == '..') continue;
            // match on extension
            if (preg_match('/\.jpg|\.jpe|\.jpeg|\.png|\.gif|\.ico/i', $entry)) {
                array_push($array, $dir.'/'.$entry);
            } else {
                if(is_dir($dir.'/'.$entry)){
                    findImages($dir.'/'.$entry,$array);
                }
            }
        }
        // close the directory
        closedir($handle);
    }else{
        echo 'error opening directory';
    }
}

function pathHistory() {
    require(__DIR__ . '/pluginconfig.php');
    $latestpathes = array_slice($foldershistory, -3);
    $latestpathes = array_reverse($latestpathes);
    foreach($latestpathes as $folder) {
        echo '<p class="pathHistory" onclick="useHistoryPath(\''.$folder.'\');">'.$folder.'</p>';
    }
}