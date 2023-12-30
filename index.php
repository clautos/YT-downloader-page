<?php

function downloadYTVideo($url, $choix) {
    $filename=" ";
    $path="/var/www/html/tempDownloads/";
    $cmd = "yt-dlp --get-filename -o \"%(title)s.%(ext)s\" \"$url\"";
    exec($cmd, $output);
    if (!empty($output))
    {
        $filename = "$output[0]";
	if ($choix === 'audio')
	{
	  if (str_ends_with($filename, "webm"))
	  {
     	    $filename = str_replace(".webm", ".mp3", $output[0]);
	  }
	  elseif (str_ends_with($filename, "mkv"))
	  {
	    $filename = str_replace(".mkv", ".mp3", $output[0]);
	  }
	  else
	  {
	    $filename = str_replace(".mp4", ".mp3", $output[0]);
	  }
        }
	elseif ($choix === 'video')
	{
          $filename = "$output[0]";
        }
    }
    else
    {
	echo "Impossible de télécharger la vidéo";
	exit;
    }
    $cmd=" ";
    if ($choix === 'audio')
    {
        $cmd = "cd $path && yt-dlp \"$url\" -x --audio-format mp3 -o \"$filename\"";
    }
    elseif ($choix === 'video')
    {
	if (str_contains($url, "https://youtube.com") OR str_contains($url, "youtu.be") OR str_contains($url, "www.youtube.com"))
	{
          $cmd = "cd $path && yt-dlp \"$url\" -f bestvideo+bestaudio -o \"$filename\"";
	}
	else
	{
	  $cmd = "cd $path && yt-dlp -F \"$url\"";
	  exec($cmd, $formats);
	  //We get the best format for the video
	  $words = explode(" ", end($formats));
	  $cmd = "cd $path && yt-dlp -f \"$words[0]\" \"$url\" -o \"$filename\"";
	}
    }
    exec($cmd);
    $fullname=$path . $filename;
    //If file contains "," we must get rid of them
    $Newfilename = str_replace(",", " ", $fullname);
    $cmd = "mv  \"$fullname\" \"$Newfilename\"";
    exec($cmd);
    $filename = str_replace(",", " ", $filename);
    $fullname = $Newfilename;
    if (file_exists($fullname)) {
        if (str_ends_with($filename, "webm"))
	  {
     	    header('Content-Type: video/webm');
	  }
	  elseif (str_ends_with($filename, "mkv"))
	  {
	    header('Content-Type: video/x-matroska');
	  }
	  elseif (str_ends_with($filename, "mp4"))
	  {
	    header('Content-Type: video/mp4');
	  }
	  else
	  {
            header('Content-Type: application/octet-stream');
	  }
        header('Content-Disposition: attachment; filename='.$filename);
        header('Content-Length: ' . filesize($fullname));
	ob_get_clean();
        readfile($fullname, 'rb');
	ob_end_flush();
	exec("rm -fr \"$fullname\"");
        exit;
    } else {
        echo "Error during file download." . $fullname;
        exit;
    }
    exit;
}

// Traitement du formulaire lorsque le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['url'])) {
        $url = $_POST['url'];
    }
    if (isset($_POST['choix'])) {
        $choix = $_POST['choix'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Youtube-Downloader Facile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

	button[type="submit"] {
    	display: block;
    	margin: 0 auto;
	}
        h1 {
            background-color: #ff351A;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        form {
            max-width: 420px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        select,
        input[type="text"] {
            width: 96%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #ff351A;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
	    align: center;
        }

        button[type="submit"]:hover {
            background-color: #e60000;
        }
    </style>
</head>
<body>
    <h1>Youtube-Downloader<br><br> (do not quit the page as long as download has not started)</h1>
    <form method="POST">
        <label for="choix">Choose an option :</label>
        <select name="choix" id="choix">
            <option value="audio">Audio</option>
            <option value="video">Video</option>
        </select>
        <br><br>
        <label for="url">URL of the YouTube Video :</label>
        <input type="text" name="url" required id="url">
        <br><br>
        <button type="submit">Download</button>
    </form>
    <?php
    if (isset($choix)) {
        downloadYTVideo($_POST['url'], $choix);
    }
    ?>
</body>
</html>
