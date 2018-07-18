<?php include 'php/cachestart.php';?>
<?php require 'php/RequestController.php';?>
<!DOCTYPE html>
<head>
	<link rel="stylesheet"  href="css/style.css">
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
<table>
	<thead>
		<tr>
            <th scope='row'>Total Open Issues</th>
            <th scope='row'>Issues Closed In The Previous Week</th>
        </tr>
		<tr>
			<th class='number number--open'><?php GetIssueCount(GetRequestResult());?></th>
			<th class='number number--closed'><?php GetIssueCount(GetClosedIssuesSinceLastWeek());?></th>
		</tr>
	</thead>
</table>
<table>
    <thead>
        <tr>
            <th scope='row'>Issue ID</th>
            <th scope='row'>Issue Title</th>
            <th scope='row'>Issue Description</th>
            <th scope='row'>Labels</th>
            <th scope='row'>Issue Created</th>
        </tr>
    </thead>
	<?php ParseResult(GetRequestResult("open"));?>
</table>
</body>
</html>
<?php include 'php/cacheend.php';?>
