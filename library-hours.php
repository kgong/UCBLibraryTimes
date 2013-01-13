<?php
  date_default_timezone_set('America/Los_Angeles');
  error_reporting(E_ERROR | E_PARSE);
	$actualInput = trim($_REQUEST['Body']);
	$inputBody = strtolower($actualInput);

/* Parse the Berkeley hours page */
	$curl = curl_init('http://www.lib.berkeley.edu/hours/');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
	$html = curl_exec($curl);
	curl_close($curl);

/* Catch if html is wrong for whatever reason */
	if (!$html)
		die("something's wrong");

/* Setup the DOM and XPath */
	$dom = new DOMDocument;
	$dom->loadHTML($html);
	$dom->preserveWhiteSpace = false;
	$xpath = new DOMXPath($dom);
	
	$allLibraries = array();
  $counter = -1;
  $idPairs = array(
    0  => "Anthropology",
    2  => "Art History/Classics",
    4  => "Bancroft",
    6  => "Berkeley Law",
    9  => "Bioscience & Natural Resources",
    11 => "Business",
    14 => "Career Counseling",
    15 => "Chemistry and Chemical Engineering",
    20 => "Doe",
    22 => "Main Stacks",
    26 => "Earth Sciences and Map",
    28 => "East Asian",
    30 => "Education Psychology",
    32 => "Engineering",
    35 => "Environmental Design",
    37 => "Ethnic Studies",
    39 => "Giannini Foundation",
    42 => "Graduate Theological Union",
    44 => "Institute of Research on Labor and Employment",
    46 => "Institute of Governmental Studies",
    48 => "Institute of Transportation Studies",
    51 => "Mathematics Statistics",
    54 => "Moffitt",
    57 => "Morrison",
    58 => "Music",
    60 => "NISEE/PEER Earthquake Engineering",
    62 => "Newspapers and Microforms",
    64 => "Optometry and Health Services",
    66 => "Pacific Film Archive",
    68 => "Physics-Astronomy",
    71 => "Public Health",
    73 => "Social Welfare",
    75 => "Southeast"
  );


	foreach ($xpath->query("//table[1]//tr[2]//table//tr[1]/td[1]/table[2]/tbody/tr/td[2]") as $node) {
    $counter = $counter + 1;
    if ($counter == 0 || $counter == 2 || $counter == 4 || $counter == 6 || $counter == 9 || $counter == 11 || $counter == 14 || $counter == 15 || $counter == 20 || $counter == 22 || $counter == 26 || $counter == 28 || $counter == 30 || $counter == 32 || $counter == 35 || $counter == 37 || $counter == 39 || $counter == 42 || $counter == 44 || $counter == 46 || $counter == 48 || $counter == 51 || $counter == 54 || $counter == 57 || $counter == 58 || $counter == 60 || $counter == 62 || $counter == 64 || $counter == 66 || $counter == 68 || $counter == 71 || $counter == 73 || $counter == 75) {
	    $rawValue = ($node->nodeValue);
      if (strstr($rawValue, 'Closed' | strstr(strtolower($rawValue), 'by'))) {
        $allLibraries[] = 'Closed';
      }
      else {
        $splitTime = explode('-', $rawValue);
        $openTime = strtotime($splitTime[0]);
        $closeTime = strtotime($splitTime[1]);
        $open = False;
        if ($curr["openTime"] < strtotime("now") && strtotime("now") < $curr["closeTime"])
          $open = True;
        $allLibraries[] = array(
          "name" => $idPairs[$counter],
          "open" => $open,
          "rawValue" => trim($rawValue),
          "openTime" => $openTime,
          "closeTime" => $closeTime,
        );
      }
    }
  }

/* Logic for detecting input */
  if($inputBody == "open") {
    $openFound = False;
    foreach ($allLibraries as $curr) {
      if ($curr["open"]) {
        $openFound = True;
        $toReturn = $toReturn.", ".$curr["name"];
      }
    }
    if ($openFound == False)
      $toReturn = "No libraries are open now. Try SLC or other department buildings!";
  }
	elseif($inputBody == "anthropology" || $inputBody == "anthro") 
		$toReturn = "The Anthropology Library is open ".$allLibraries[0]["rawValue"];
  elseif ($inputBody == "art history" || $inputBody == "art")
		$toReturn = "The Art History/Classics Library is open ".$allLibraries[1]["rawValue"];
  elseif ($inputBody == "bancroft")
		$toReturn = "The Bancroft Library/University Archives is open ".$allLibraries[2]["rawValue"];
  elseif ($inputBody == "berkeley law" || $inputBody == "boalt" || $inputBody == "law")
		$toReturn = "The Berkeley Law Library is open ".$allLibraries[3]["rawValue"];
  elseif ($inputBody == "bioscience")
		$toReturn = "The Bioscience & Natural Resources Library is open ".$allLibraries[4]["rawValue"];
  elseif ($inputBody == "business" || $inputBody == "haas")
		$toReturn = "The Business Library is open ".$allLibraries[5]["rawValue"];
  elseif ($inputBody == "career counseling" || $inputBody == "career")
		$toReturn = "The Career Counseling Library is open ".$allLibraries[6]["rawValue"];
  elseif ($inputBody == "chemistry" || $inputBody == "chem" || $inputBody == "chemical engineering" || $inputBody == "cheme" || $inputBody == "chem e")
		$toReturn = "The Chemistry and Chemical Engineering Library is open ".$allLibraries[7]["rawValue"];
  elseif ($inputBody == "doe")
		$toReturn = "Doe Library is open ".$allLibraries[8]["rawValue"];
	elseif ($inputBody == "stacks" || $inputBody == "mainstacks" || $inputBody == "main stacks")
		$toReturn = "Main Stacks is open ".$allLibraries[9]["rawValue"];
  elseif ($inputBody == "earth sciences" || $inputBody == "map")
		$toReturn = "The Earth Sciences and Map Library is open ".$allLibraries[10]["rawValue"];
  elseif ($inputBody == "east asian")
		$toReturn = "The East Asian Library is open ".$allLibraries[11]["rawValue"];
  elseif ($inputBody == "education psychology" || $inputBody == "psychology" || $inputBody == "psych")
		$toReturn = "The Education Psychology Library is open ".$allLibraries[12]["rawValue"];
  elseif ($inputBody == "engineering" || $inputBody == "bechtel")
		$toReturn = "The Engineering Library is open ".$allLibraries[13]["rawValue"];
  elseif ($inputBody == "environmental design" || $inputBody == "enviro" || $inputBody == "enviro design" || $inputBody == "environmental")
		$toReturn = "The Environmental Design Library is open ".$allLibraries[14]["rawValue"];
	elseif ($inputBody == "ethnic studies" || $inputBody == "ethnic" || $inputBody == "ethn")
		$toReturn = "The Ethnic Studies Library is open ".$allLibraries[15]["rawValue"];
  elseif ($inputBody == "giannini")
		$toReturn = "Giannini Foundation Library is open ".$allLibraries[16]["rawValue"];
  elseif ($inputBody == "graduate theological union" || $inputBody == "theological" || $inputBody == "theological union" || $inputBody == "stats" || $inputBody == "grad theological union")
		$toReturn = "The Graduate Theological Union Library is open ".$allLibraries[17]["rawValue"];
  elseif ($inputBody == "institute of research on labor" || $inputBody == "employment" || $inputBody == "institute of research on labor and employment" || $inputBody == "irle")
		$toReturn = "The Institute of Research on Labor and Employment Library is open ".$allLibraries[18]["rawValue"];
  elseif ($inputBody == "institute of governmental studies" || $inputBody == "governmental studies" || $inputBody == "igs")
		$toReturn = "The Institute of Governmental Studies Library is open ".$allLibraries[19]["rawValue"];
  elseif ($inputBody == "institute of transportation studies" || $inputBody == "transportation studies" || $inputBody == "its")
		$toReturn = "The Institute of Transportation Studies Library is open ".$allLibraries[20]["rawValue"];
  elseif ($inputBody == "mathematics statistics" || $inputBody == "math" || $inputBody == "stat" || $inputBody == "stats" || $inputBody == "math stats")
		$toReturn = "The Mathematics Statistics Library is open ".$allLibraries[21]["rawValue"];
  elseif ($inputBody == "moffitt")
		$toReturn = "Moffitt is open ".$allLibraries[22]["rawValue"];
  elseif ($inputBody == "morrison")
		$toReturn = "Morrison Library is open ".$allLibraries[23]["rawValue"];
  elseif ($inputBody == "music")
		$toReturn = "The Music Library is open ".$allLibraries[24]["rawValue"];
  elseif ($inputBody == "nisee" || $inputBody == "peer" || $inputBody == "earthquake" || $inputBody == "earthquake engineering")
    $toReturn = "The NISEE/PEER Earthquake Engineering Library is open ".$allLibraries[25]["rawValue"];
  elseif ($inputBody == "newspapers" || $inputBody == "newspaper" || $inputBody == "microforms" || $inputBody == "newspapers and microforms")
    $toReturn = "The Newspapers and Microforms Library is open ".$allLibraries[26]["rawValue"];
  elseif ($inputBody == "optometry and health services" || $inputBody == "optometry" || $inputBody == "optom")
		$toReturn = "The Optometry and Health Services Library is open ".$allLibraries[27]["rawValue"];
  elseif ($inputBody == "pacific film archive" || $inputBody == "film study")
    $toReturn = "The Pacific Film Archive Library and Film Study Center is open ".$allLibraries[28]["rawValue"];
  elseif ($inputBody == "physics astronomy" || $inputBody == "physics" || $inputBody == "astronomy" || $inputBody == "physics-astronomy")
		$toReturn = "The Physics-Astronomy Library is open ".$allLibraries[29]["rawValue"];
  elseif ($inputBody == "public health" || $inputBody == "ph")
		$toReturn = "The Public Health Library is open ".$allLibraries[30]["rawValue"];
  elseif ($inputBody == "social welfare" || $inputBody == "social")
		$toReturn = "The Social Welfare Library is open ".$allLibraries[31]["rawValue"];
  elseif ($inputBody == "southeast asia" || $inputBody == "south asia" || $inputBody == "southeast asian" || $inputBody == "south/southeast asia")
		$toReturn = "The Southeast Asia Library is open ".$allLibraries[32]["rawValue"];
	elseif ($inputBody == "slc" || $inputBody == "student learning center")
		$toReturn = "The SLC is open 24 hours!";
	else
		$toReturn = $actualInput."is not a valid library name. Just enter the library name such as doe or stacks. No need for the word library.";
	
	if(strstr($toReturn, 'Closed'))
		$toReturn = "That library is closed today =(.";
?>

<Response>
	<Sms><?php echo $toReturn?></Sms>
</Response>
