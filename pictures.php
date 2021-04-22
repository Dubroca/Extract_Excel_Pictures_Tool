<?php

//Verification that the user has chosen a csv file format
if(isset($_POST['csvformat']) && !empty($_POST['csvformat']) && file_exists($_FILES['mon_fichier']['tmp_name']) || is_uploaded_file($_FILES['files']['tmp_name']))
{

//Retrieving the user's separator
$delimiterpost = $_POST['csvformat'];

//Uploaded xlsx file recovery
$xlsx="C:/it/xlsx_files/".date('Y_m_d H-i-s')."_file.xlsx";
move_uploaded_file($_FILES["mon_fichier"]["tmp_name"],$xlsx);

require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
$objPHPExcel = PHPExcel_IOFactory::load($xlsx);

//Connexion BDD
// include "include/ODBCaccess.class.php";
// $connect = odbc_connect("PDM_SER","AS400","AS400");

//Unique name folder for the pictures
$dirname = uniqid();
mkdir("C:/it/pictures_folders/$dirname/");

//Reading the xlsx file
$sheet = $objPHPExcel->getActiveSheet();

foreach ($sheet->getDrawingCollection() as $drawing ) {

    if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
        ob_start();
        call_user_func(
            $drawing->getRenderingFunction(),
            $drawing->getImageResource()
        );
        $imageContents = ob_get_contents();
        ob_end_clean();
        switch ($drawing->getMimeType()) {
            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG:
                $extension = 'png'; break;
            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
                $extension = 'gif'; break;
            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG:
                $extension = 'jpg'; break;
        }
    } else {
        $zipReader = fopen($drawing->getPath(),'r');
        $imageContents = '';
        while (!feof($zipReader)) {
            $imageContents .= fread($zipReader,1024);
        }
        fclose($zipReader);
        $extension = $drawing->getExtension();     
        $chemin = "C:/it/pictures_folders/$dirname/";  
    }    
    
    //Retrieving cell values for the images name
    $row = (int) substr($drawing->getCoordinates(), 1);  
    //Condition to read merged cell
    $stylecode = $sheet->getCell('H'.$row)->getValue() ?: $stylecode;
    $colorcode = $sheet->getCell('E'.$row)->getValue();
    $finalname = $stylecode.'_'.$colorcode;
    $myFileName = $chemin.$finalname.'.'.$extension;
    file_put_contents($myFileName, $imageContents); 

}

//Unmerged the xlsx file
$row = 0;
$skipRows = [1, 3];
foreach($sheet->getMergeCells() as $range) {
    $value = $sheet->rangeToArray($range)[0][0];
    $cells = $sheet->rangeToArray($range, null, true, true, true);
    $sheet->unmergeCells($range);
	if (strpos($value, 'Data as of:') !== false) continue;
    if(in_array(++$row, $skipRows)) continue;
    if(!$value) continue;

    foreach($cells as $row => $columns) {
        foreach(array_keys($columns) as $column) {
            $sheet->setCellValue("$column$row", $value);
        }
    }
}

//Create the new xlsx unmerged file
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
$unmerged="C/it/unmerged_files/".date('Y_m_d H-i-s')."_file.xlsx";
$objWriter->save($unmerged);

                            
//Excel in CSV
$excel = PHPExcel_IOFactory::load($unmerged);
$writer = PHPExcel_IOFactory::createWriter($excel, 'CSV');
$writer->setDelimiter($delimiterpost);
$writer->setEnclosure('"');
$nomcsv = "C/it/csv/".date('Ymd_His').".csv";
$writer->save($nomcsv);


      //Function for CATLOG INDICATORS
		function multiSplit($string)
		{
			$output = array();
			$cols = explode(",", $string);

            foreach ($cols as $col)
            {
                $dashcols1 = str_replace('–', '-', $col);
                $dashcols2 = explode("-", $dashcols1); 
                $output[] = $dashcols2[0];
                
            }
		
		return $output;
	  }


//Modifications on csv file
$delimiter = $delimiterpost;
$csv_data = array();
$row = 1;
$paragraphe = "• ";

if (($handle = fopen($nomcsv, 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {

      
        //Modifications on the fourth line
        if ($row == 4) 
        {
               //All uppercase
               $data = array_map('strtoupper', $data);  
               $data = str_replace(' *', '', $data);
               $data = str_replace('/', '', $data);
               //Replacement of spaces by an underscore
               $data = str_replace(' ', '_', $data);
               $data = str_replace('__', '_', $data);
               //Add column headers
               $indice_fin_tableau= count($data);
               $data[count($data)]='PICT';
               $data[count($data)]='COLOR_DESCRIPTION';

               for ( $i=1; $i<=10; $i++ ){
                    $data[count($data)+$i-1]="PICTO$i";
				}						   		  		   
		}
        else
        {
            //Add columns at the end
            $data[$indice_fin_tableau] = (!empty($data[4]) ? ($data[7] ?: '') . "_" . $data[4] . '.jpg' : ' '); 
            $data[$indice_fin_tableau+1] = (!empty($data[3]) ? (ltrim($data[4], '0') ?: '') . "-" . $data[3] : ' ');  
    			
    		//Using the function for CATALOG INDICATORS
            $out = multiSplit($data[23]);
    		
    		$data[$indice_fin_tableau+2] = $out[0];
    		$data[$indice_fin_tableau+3] = $out[1];	
    		$data[$indice_fin_tableau+4] = $out[2];  
    		$data[$indice_fin_tableau+5] = $out[3];  
    		$data[$indice_fin_tableau+6] = $out[4]; 
    		$data[$indice_fin_tableau+7] = $out[5]; 
    		$data[$indice_fin_tableau+8] = $out[6];  
    		$data[$indice_fin_tableau+9] = $out[7];
    		$data[$indice_fin_tableau+10] = $out[8]; 
    		$data[$indice_fin_tableau+11] = $out[9];
    
             //Modifications of the column Style Feature Cats Comments
               if ($data[22])			 
    		{
                $data[22] = $paragraphe.$data[22];
                $data[22] = str_replace(',', '§• ', $data[22]);
                				
    		}
        }
            // Delete two columns at the beginning
            unset($data[1]);
            unset($data[2]);
			$csv_data[] = $data; 
		  				      
        $row++;      
    }
    fclose($handle);
}

$csv_data = array_slice($csv_data, 3); // this will remove first three elements
array_pop($csv_data);// this will remove last element from array


if (($handle = fopen($nomcsv, 'w')) !== FALSE) {
    //Added this line for reading special characters like '§'
    fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));	
    foreach ($csv_data as $data) {
        fputcsv($handle, $data, $delimiter);
    }
    fclose($handle);
}
     
// Put the xlsx file in a zip folder
$solidpepper_path = "C:/it/pictures_folders/$dirname/solidpepper_file.csv";

if (!copy($nomcsv, $solidpepper_path)) {
    echo "The copy of the file $nomcsv failed...\n";
}

//Name zip folder with date
$dirzip = date('Ymd His')."_andromeda";

//Zip creation        
$nomzip = "C:/zip/$dirzip.zip"; 
$zip = new ZipArchive;
if($zip -> open($nomzip, ZipArchive::CREATE ) === TRUE)
{ 
    $dir = opendir($chemin); 
    while($fichier = readdir($dir)) 
    { 
        if(is_file($chemin.$fichier)) 
        { 
           $zip -> addFile($chemin.$fichier, $fichier); 
        } 
    } 
    $zip ->close(); 
} 

//Zip download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.basename($nomzip).'"');
header('Content-Length: ' . filesize($nomzip));

flush();
readfile($nomzip);	
         
}
else
{
    //Error message display
?>

    <script type="text/javascript">
    alert("You must choose your csv file format and an Andromeda xlsx file for the tool to work ! Please start again.");
    window.location.href = "index.php";
    </script>
<?php
 
}      

         
?>




