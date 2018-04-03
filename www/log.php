<?php

//config
use Nette\ArrayHash;

define('ROOT_DIR', dirname(__FILE__) . '/..');
define('LOG_DIR', ROOT_DIR . '/log');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('session.save_path', ROOT_DIR . '/temp/sessions');
session_start();
ini_set('date.timezone', 'Europe/Prague');
header('Content-type: text/html; charset=utf-8');

require_once ROOT_DIR . '/vendor/autoload.php';


// authorizations
$isLocalAdmin = in_array($_SERVER['REMOTE_ADDR'], array('46.234.112.2', '127.0.0.1', '::1'));
$isPostAuthorized = FALSE;
$notShowFiles = ['.htaccess', 'web.config', '.gitkeep', '.emptyfile'];

if (!$isLocalAdmin && !$isPostAuthorized) {
	header("HTTP/1.0 404 Not Found");
}

if (!empty($_GET['max_file_size'])) {
	if (preg_match('/^[1-9]\d*$/', $_GET['max_file_size'])) {
		define('MAX_FILE_SIZE_PREVIEW', intval($_GET['max_file_size']));
	} else {
		define('MAX_FILE_SIZE_PREVIEW', 3000000);
	}
} else {
	define('MAX_FILE_SIZE_PREVIEW', 3000000);
}


// POST & GET DELETE
if (($isLocalAdmin && isset($_GET['delete'])) || ($isPostAuthorized && isset($_POST['delete'])) || ($isLocalAdmin && isset($_POST['delete']))) {

	if ($isLocalAdmin && isset($_GET['delete'])) {
		$filename = $_GET['delete'];
	} elseif ($isPostAuthorized && isset($_POST['delete'])) {
		$filename = $_POST['delete'];
	} elseif ($isLocalAdmin && isset($_POST['delete'])) {
		$filename = $_POST['delete'];
	}

	if ($filename === 'all') {
		$delete = \Nette\Utils\Finder::findFiles('*')->in(LOG_DIR);
	} elseif (is_array($filename)) {
		$delete = \Nette\Utils\Finder::findFiles($filename)->in(LOG_DIR);
	} else {
		$delete = \Nette\Utils\Finder::findFiles(urldecode($filename))->in(LOG_DIR);
	}

	/** @var SplFileInfo $deleteFile */
	foreach ($delete as $deleteFile) {
		if (in_array($deleteFile->getBasename(), $notShowFiles)) {
			continue;
		}
		unlink($deleteFile->getRealPath());
	}

	if ($isPostAuthorized) {
		return;
	} else {
		header("Location: /log.php");
	}
}

// GET show one log file
if ($isLocalAdmin && isset($_GET['show_file'])) {
	$file = urldecode($_GET['show_file']);
	$content = file_get_contents(LOG_DIR . DIRECTORY_SEPARATOR . $file);
	if (\Nette\Utils\Strings::endsWith($file, '.html')) {
		//		echo '<style type="text/css">#tracy-bs { top: 30px !important;} </style>';
		echo '<a href="log.php"><button style="z-index: 21000; position: fixed; top: 10px; left: 83.5%;">Zpět na výběr</button></a>';
		echo '<a href="log.php?delete=' . $file . '"><button style="z-index: 21000; position: fixed; top: 10px; left: 90%;">Smazat soubor</button></a>';
		echo $content;
	} else {
		echo '<a href="log.php"><button style="z-index: 21000; position: fixed; top: 10px; left: 83.5%;">Zpět na výběr</button></a>';
		echo '<a href="log.php?delete=' . $file . '"><button style="z-index: 21000; position: fixed; top: 10px; left: 90%;">Smazat soubor</button></a>';
		echo "<pre>$content</pre>";
	}
	exit();
}

// GET list log files (access for users logged in admin)
if ($isLocalAdmin) {

	//nacteni souboru
	$filesIterator = \Nette\Utils\Finder::findFiles('*')->in(LOG_DIR);

	$files = iterator_to_array($filesIterator);

	/** @var SplFileInfo $file */
	foreach ($files as $key => $file) {
		if (in_array($file->getBasename(), $notShowFiles)) {
			unset($files[$key]);
		}
	}

	if (count($files) == 0) {
		printBugFree();
		return;
	}

	printCSS();
	printJS();

	echo '<form method="post">';
	echo '<table class="table table-striped table-bordered table-hover">';
	echo '<thead><tr>'
		. '<th>when</th>'
		. '<th>file</th>'
		. '<th>error</th>'
		. '<th>size</th>'
		. '<th><input type="checkbox" title="check all" onclick="checkall(\'delete[]\', this.checked);"><input type="submit" value="delete"></th>'
		. '<th><a href = "?delete=all" onclick = "return confirm(\'Really delete all of them?\');">detele all</a></th>'
		. '</tr></thead>';
	echo '<tbody>';

	$beforeWeek = new \DateTime();
	$beforeWeek->modify("-1 week");

	//serazeni
	$sortFunc = function (SplFileInfo $a, SplFileInfo $b) {
		return $a->getBasename() < $b->getBasename();
	};
	uasort($files, $sortFunc);

	foreach ($files as $file) {
		$second = FALSE;
		//vyparsovani data z nazvu (getCTime a podobne jsou ovlivnene)
		$date = NULL;
		$match = \Nette\Utils\Strings::match($file->getBasename(), '/(\d{4}-\d{2}-\d{2}--\d{2}-\d{2})/i');
		if (empty($match) || !isset($match[1])) {
			$match = \Nette\Utils\Strings::match($file->getBasename(), '/(\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2})/i');
			$second = TRUE;
		}

		if (!empty($match) && isset($match[1])) {
			$match = $match[1];
			if (!$second) {
				$match = \Nette\Utils\Strings::replace($match . '-00', '/--/i', '-');
			}
			$date = DateTime::createFromFormat("Y-m-d-H-i-s", $match);
		} else {
			$date = date("Y-m-d-H-i-s", filectime($file));
			$date = DateTime::createFromFormat("Y-m-d-H-i-s", $date);
		}

		echo '<tr>';
		if ($date) {
			if ($date >= $beforeWeek) {
				echo '<td><strong>' . $date->format('d-m-Y | H:i:s') . '</strong></td>';
			} else {
				echo '<td>' . $date->format('d-m-Y | H:i:s') . '</td>';
			}
		} else {
			echo '<td></td>';
		}
		echo '<td><a href="?show_file=' . urlencode($file->getBasename()) . '">' . $file->getBasename() . '</a></td>';
		echo '<td class="error-detail">';
		if ($file->getSize() < MAX_FILE_SIZE_PREVIEW && $file->getExtension() == 'html') { //jen soubory mensi nez 5MB

			$fh = fopen($file->getRealPath(), 'r');
			$list = '';
			for ($i = 0; $i < 40; $i++) {
				$list .= fread($fh, 4096);
			}

			fclose($fh);

			$dom = new DOMDocument();
			@$dom->loadHTML($list);
			$dom->preserveWhiteSpace = FALSE;
			$err = $dom->getElementById("tracy-bs-error");
			if ($err) {
				$detail = \Nette\Utils\Strings::truncate($err->childNodes->item(3)->nodeValue, 150);
				$detail = removeNonMessage($detail);
				echo "<b>{$err->childNodes->item(1)->textContent}</b> ($detail)</td>";
			} else {
				$err = $dom->getElementById('netteBluescreenError');
				if ($err) {
					$detail = \Nette\Utils\Strings::truncate($err->childNodes->item(3)->nodeValue, 150);
					$detail = removeNonMessage($detail);
					echo "<b>{$err->childNodes->item(1)->textContent}</b> ($detail)</td>";
				}
			}
			$dom = NULL;
			unset($dom);
		} else {
			//echo '<i>too large to parse</i>';
		}
		if (!class_exists('Latte\Runtime\Filters')) {
			class_alias('Nette\Templating\Helpers', 'Latte\Runtime\Filters');
		}
		echo '</td>';
		echo '<td>' . \Latte\Runtime\Filters::bytes($file->getSize()) . '</td>';
		echo '<td><input type="checkbox" name="delete[]" value="' . $file->getBasename() . '"></input></td>';
		echo '<td><a href="?delete=' . urlencode($file->getBasename()) . '">delete</a></td>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</form>';

	return;
}

// POST REQUESTS
if ($isPostAuthorized) {

	// ONLY COUNTS (all files, exceptions)
	if (isset($_POST['counts'])) {
		$exceptions = \Nette\Utils\Finder::findFiles('exception-*')->in(LOG_DIR);
		$num['files'] = 0;
		$num['exceptions'] = 0;
		foreach ($files as $file) {
			$num['files']++;
		}
		foreach ($exceptions as $exceptionFile) {
			$num['exceptions']++;
		}
		echo json_encode($num);
		return;
	}

	// INFO (normal and detail)
	if (isset($_POST['info'])) {
		$out = array();
		foreach ($files as $file) {
			$f = new ArrayHash();
			$f->file = $file->getBasename();
			$f->fullPath = $f->size = $file->getSize();
			$f->lastChanged = $file->getCTime();
			if (isset($_POST['detail']) && $file->getSize() < 1000000) {
				$dom = new DOMDocument();
				@$dom->loadHTMLFile($file->getRealPath());
				$dom->preserveWhiteSpace = FALSE;
				$err = $dom->getElementById("netteBluescreenError");
				if ($err) {
					$f->error = $err->childNodes->item(1)->textContent;
					$f->detail = $err->childNodes->item(3)->nodeValue;
				}
			}
			$out[] = $f;
		}

		echo json_encode($out);
		return;
	}
}

function removeNonMessage($string)
{
	$nonMessage = ['search►', 'skip error►', 'error►'];

	foreach ($nonMessage as $item) {
		$string = str_replace($item, '', $string);
	}

	return $string;
}

/**
 * echo part of bootstrap 3 CSS for tables and own styles
 */
function printCSS()
{
	echo '<style type="text/css">';
	echo '

.error-detail { font-size: smaller; color: grey; }

table { border-collapse: collapse; border-spacing: 0; }
table { max-width: 100%; background-color: transparent; }
th { text-align: left; }
.table { width: 100%; margin-bottom: 20px; }
.table > thead > tr > th,.table > tbody > tr > th,.table > tfoot > tr > th,.table > thead > tr > td,.table > tbody > tr > td,.table > tfoot > tr > td { padding: 3px; line-height: 1.428571429; vertical-align: top; border-top: 1px solid #dddddd; }
.table > thead > tr > th { vertical-align: bottom; border-bottom: 2px solid #dddddd; }
.table > tbody + tbody { border-top: 2px solid #dddddd; }
.table .table { background-color: #ffffff; }
.table-bordered { border: 1px solid #dddddd; }
.table-bordered > thead > tr > th,.table-bordered > tbody > tr > th,.table-bordered > tfoot > tr > th,.table-bordered > thead > tr > td,.table-bordered > tbody > tr > td,.table-bordered > tfoot > tr > td { border: 1px solid #dddddd; }
.table-bordered > thead > tr > th, .table-bordered > thead > tr > td { border-bottom-width: 2px; }
.table-striped > tbody > tr:nth-child(odd) > td, .table-striped > tbody > tr:nth-child(odd) > th { background-color: #f9f9f9; }
.table-hover > tbody > tr:hover > td, .table-hover > tbody > tr:hover > th { background-color: #f5f5f5; }
';
	echo '</style>';
}

/**
 * JS
 */
function printJS()
{
	echo '<script type="text/javascript">';
	echo '
function checkall( name, value )
{
    var elm = document.getElementsByName(name);
    for(i = 0; i < elm.length; i++)
    {
        elm[i].checked = value;
    }
    return false;
}
';
	echo '</script>';
}

function printBugFree()
{
	echo '<img src="data:image/jpeg;base64,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">';
}