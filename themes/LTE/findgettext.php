<?php 
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/
ini_set('max_execution_time', 600);
$page_security = 'SA_OPEN';
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();
include($path_to_root . "/gl/includes/gl_db.inc");
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/includes/date_functions.inc");

//echo  serialize(array('en_US' => 'English', 'ta_IN' => 'Tamil', 'ar_EG' => 'Arabic'));
page("Translation");
if(isset($_POST['GeneratePO'])){
	if($_POST['language'] == 'C') {
		display_error("You Don't need to translate the System to english, We guess. Try some other language");
	} else {
		
	$dir = './';

echo "--------------------------------------file name----------------------------------<br>";
echo $dir."<br>";
echo "----------------------------------end file name----------------------------------<br>";

	global $results; 
	$lang = $_POST['language'];
	$results =  array();
	KvscanDir($dir);
	
	//print_r($results);
	$translate_str = 'msgid ""
msgstr ""
"Project-Id-Version: \n"
"Report-Msgid-Bugs-To: \n"
"POT-Creation-Date: '.date('Y-m-d h:i a').'\n"
"PO-Revision-Date: '.date('Y-m-d h:i a').'\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Language-Team: \n"
"X-Generator: Poedit 1.8.7\n"
"Last-Translator: Varadha <admin@kvcodes.com>\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\n"
"Language: '.$lang.'\n"

';
$dir = $path_to_root;
//$dir =  dirname(dirname(dirname(__FILE__)));

echo "------------------------------------Translate string----------------------------------<br>";
if(file_exists($path_to_root.'/lang/'.$lang.'/LC_MESSAGES/'.$lang.'.po'))
	$translate_str = $language_content = file_get_contents($path_to_root.'/lang/'.$lang.'/LC_MESSAGES/'.$lang.'.po'); 
else
	$translate_str = $language_content = '';

	foreach($results as $single){
        $start_pos = strpos($language_content, 'msgid "'.trim($single).'"'); 
        if ($start_pos !== false){
            continue;
        }
		if($single != '' && $single !=  null && (strpos($translate_str, 'msgid "'.trim($single).'"') === false)){
			$translate_str .= 'msgid "'.trim($single).'" 
msgstr "" 

';
			echo 'msgid "'.trim($single).'" ';
			echo "<br>";
			echo 'msgstr "" ';
			echo "<br>";
			echo "<br>";
		}		
	}
echo "--------------------------------end Translate string----------------------------------<br>";

	$file = $dir.'/lang/'.$lang.'/LC_MESSAGES/'.$lang.'.po';
	if(!file_exists($file)){	
		if(!is_file($file)){   
			//$ourFileHandle = fopen($file, 'w') or die("can't open file");
			if (!file_exists($dir.'/lang/')) {
				mkdir($dir.'/lang/', 0777, true);
			} 
			if (!file_exists($dir.'/lang/'.$lang.'/')) {
				mkdir($dir.'/lang/'.$lang.'/', 0777, true);
			}
			if (!file_exists($dir.'/lang/'.$lang.'/LC_MESSAGES/')) {
				mkdir($dir.'/lang/'.$lang.'/LC_MESSAGES/', 0777, true);
			}
			file_put_contents($file, $translate_str);
			display_notification("file created successfully");
		}
	}else {
		file_put_contents($file, "");
		file_put_contents($file, $translate_str);
		display_notification("file updated successfully");
		display_notification("You have created the PO file. Inorder to translate the system download the PO file from <a href='".$file."' > Here </a>. 
		And Use Translating softwares such as PoEdit or some other online software which ever is convinent and compile Mo file from that software or application itself. 
		And than access the same location ".$file." And copy the mo file here. Now you can refresh few times to take this change effect");
	}
	}
}

function KvscanDir($dir) { 
		$ffs = scandir($dir);
		$skip_dir =array('findgettext.php','company','install','js','lang','prchat','sql','css');
		$skip_exts =array('css','js','jpg','JPG','jpeg','JPEG','gif','GIF','xls','pdf','PDF','po','mo');

		unset($ffs[array_search('.', $ffs, true)]);
		unset($ffs[array_search('..', $ffs, true)]);

		foreach ($ffs as $file) { 
			if(!in_array(basename($file), $skip_dir) && !is_dir($dir.'/'.$file)){
				$file_ext =pathinfo(basename($file), PATHINFO_EXTENSION);

				if(in_array($file_ext, $skip_exts))
					continue;

				$content = file_get_contents($dir.'/'.$file); 
					GetAllStrings($content);
				//echo $file.'<br>';
			}elseif(!in_array(basename($file), $skip_dir)){
				//echo $file.'<br>';
				KvscanDir($dir.'/'.$file);
			}	
			//echo $file.'<br>';
		}
	}

function GetAllStrings($content, $start_pos=true,$quote=false){
	global $results; 

	if($quote ===false || $quote =='"'){
		if($start_pos){
			$string = '_("';
			$start_pos = strpos($content, $string); 
		}
		if ($start_pos !== false){ 
			$content = substr($content, $start_pos+3);
			$string2 = '")'; 
			$end_pos = strpos($content, $string2); 
			if($end_pos !== false) {
				$new_word = substr($content, 0, $end_pos);
				if(!in_array($new_word, $results))
					$results[] = $new_word;
			}
			$string = '_("';
			$next_start_pos = strpos($content, $string);		
			if ($next_start_pos !== false)
				GetAllStrings($content, $next_start_pos,'"');
		}
	}

	if($quote ===false || $quote =="'"){
		if($start_pos){
			$string = "_('";
			$start_pos = strpos($content, $string); 
		}
		if ($start_pos !== false){ 
			$content = substr($content, $start_pos+3);
			$string2 = "')"; 
			$end_pos = strpos($content, $string2); 
			if($end_pos !== false) {
				$new_word = substr($content, 0, $end_pos);
				if(!in_array($new_word, $results))
					$results[] = $new_word;
			}
			$string = "_('";
			$next_start_pos = strpos($content, $string);		
			if ($next_start_pos !== false)
				GetAllStrings($content, $next_start_pos );
		}
	}
} 

if (!isset($_POST['language']))
	$_POST['language'] = $_SESSION['language']->code;

start_form();

	start_table(TABLESTYLE2);
		table_section_title(_("Language"));

		languages_list_row(_("Language:"), 'language', $_POST['language']);		
		
	end_table(); 
	br();
	submit_center('GeneratePO', 'Generate');
	br();
	end_form();
end_page();