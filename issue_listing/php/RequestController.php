<?php
include("configFile.php");

function github_request($url)
{
    $ch = curl_init();
    
    // Basic Authentication with token
    // https://developer.github.com/v3/auth/
    // https://github.com/blog/1509-personal-api-tokens
    // https://github.com/settings/tokens
	global $userName;
	global $apiKey;
    $access = $userName.":".$apiKey;
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Agent smith');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERPWD, $access);
    curl_setopt($ch, CURLOPT_TIMEOUT,50);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch);
	if (curl_error($ch))
	    print_r(curl_error($ch));
		
    curl_close($ch);
    $result = json_decode(trim($output), true);
    return $result;
}

function GetLabels($labelsArray)
{
	$labels = "";
	if (!$labelsArray) 
	{
		return $labels;
	}
	foreach($labelsArray as $value)
	{
		$labels.="-".$value[name];
	}
	return $labels;
}

function GetFormatedBody($body)
{
	$startPos = strpos($body,"![");
	
	if ($startPos === false) 
	{
		return $body;
	}

	if ($startPos !== false) 
	{
		$endPos = strpos($body,"]",$startPos)+1;
		$prefixLength = $endPos - $startPos;
		$selectedPrefix = substr($body,$startPos,$prefixLength);
		$body = str_replace($selectedPrefix,"",$body);
		
		
		$startPos = strpos($body,"https");
		$endPos = strpos($body,")",$startPos);
		$linkLength = $endPos - $startPos;
		$selectedString = substr($body,$startPos,$linkLength);
		$decoratedString =  "<a href='".$selectedString."'> See Image </a>";
		$updatedString = str_replace($selectedString,$decoratedString,$body);
		return $updatedString;
	}
}

function ParseResult($result)
{
	$htmlString = "<tr>";
	$messageLabel = "messageDevTool";
	foreach ($result as $array)
	{
		foreach($array as $value)
		{
			$labels = GetLabels($value[labels]);
			$formatedBody = GetFormatedBody($value[body]);
			if($value[pull_request]!=null)
			{
				continue;
			}
			if (strpos($labels,$messageLabel) !== false && strlen($labels) <= strlen($messageLabel)+1) 
			{
				continue;
			}

			$htmlString .= "<th scope='col' class='issue_id'>#".$value[number]."</th>";
			$htmlString .= "<th scope='col'>".$value[title]."</th>";
			$htmlString .= "<th scope='col'>".$formatedBody."</th>";
			$htmlString .= "<th scope='col'>".$labels."</th>";
			$htmlString .= "<th scope='col'>".$value[created_at]."</th>";
			$htmlString.="</tr><tr>";
		}
	}
	
	$htmlString .= "";
	echo $htmlString;
}

function GetPreviousWeekDate()
{
	$date = date('Y-m-d\TH:i:s.Z\Z', strtotime("-1 week"));
	return $date;
}

function GetIssueCount($result)
{
	$count;
	for ($i = 0; $i < sizeof($result); $i++) 
	{
		foreach($result[$i] as $value)
		{
			if($value[pull_request]!=null)
			{
				continue;
			}
			$count++;
		}
	}
	echo $count;
	return $count;
}

function GetClosedIssuesSinceLastWeek()
{
	global $gitRepoUrl;
	$test[0]= github_request($gitRepoUrl."/issues?per_page=100&state=closed&since=".GetPreviousWeekDate());
	if (sizeof($test[0])>=99) 
	{
		$test[1]=github_request($gitRepoUrl."/issues?per_page=100&state=closed&since=".GetPreviousWeekDate());
	}
	return $test;
}


function GetRequestResult()
{
	global $gitRepoUrl;
	$test[0]= github_request($gitRepoUrl."/issues?per_page=100");
	if (sizeof($test[0])>=99) 
	{
		$test[1]=github_request($gitRepoUrl."/issues?per_page=100&page=2");
		if (sizeof($test[1])>=99) 
		{
			$test[2]=github_request($gitRepoUrl."/issues?per_page=100&page=3");
			if (sizeof($test[2])>=99) 
			{
				$test[3]=github_request($gitRepoUrl."/issues?per_page=100&page=4");
			}
		}
	}
	return $test;
}

